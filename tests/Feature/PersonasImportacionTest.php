<?php

namespace Tests\Feature;

use Aitrol\Configuracion\Infrastructure\Persistence\EmpresaModel;
use App\Jobs\ImportarPersonas;
use App\Models\Persona;
use App\Models\TokenAcceso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;
use ZipArchive;

final class PersonasImportacionTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_carga_queda_en_cola_y_el_worker_procesa_personas_sin_duplicarlas(): void
    {
        $disk = Storage::fake('local');
        $administrador = $this->administrador();
        Persona::query()->create(['identificacion' => '0920000002', 'id_tipo_identificacion' => 1, 'nombre' => 'Conservar']);
        $archivo = $this->excel([
            ['Cédula', 'Primer nombre', 'Segundo nombre', 'Primer apellido', 'Segundo apellido', 'Correo electrónico', 'Fecha nacimiento'],
            [920000001, 'Ana', 'Inés', 'Reyes', 'Soto', 'ana@example.test', 36526],
            ['0920000002', 'Ignorar', '', 'Dato', '', '', ''],
            ['incorrecta', 'Omitir', '', '', '', '', ''],
            ['0920000001', 'Ana', 'Inés', 'Reyes', 'Soto', '', ''],
        ]);

        $this->actingAs($administrador)->post(route('configuracion.personas.importaciones.store'), ['archivo' => $archivo])
            ->assertRedirect(route('configuracion.personas.importaciones.index'));
        $tarea = DB::table('personas_importaciones')->first();
        $disk->assertExists(ImportarPersonas::ruta($tarea->id));
        $this->assertDatabaseHas('jobs', ['queue' => 'importaciones-personas']);
        $this->assertDatabaseMissing('personas', ['identificacion' => '0920000001']);
        $this->get(route('configuracion.personas.importaciones.index'))->assertOk()->assertSee('En cola')->assertSee('personas.xlsx');

        $this->artisan('queue:work', ['connection' => 'personas', '--once' => true])->assertSuccessful();

        $this->assertDatabaseHas('personas', [
            'identificacion' => '0920000001', 'nombre' => 'Ana Inés', 'apellido' => 'Reyes Soto',
            'primer_nombre' => 'Ana', 'segundo_nombre' => 'Inés', 'primer_apellido' => 'Reyes', 'segundo_apellido' => 'Soto',
            'email' => 'ana@example.test', 'fecha_nacimiento' => '2000-01-01', 'id_usuario_creacion' => $administrador->id,
        ]);
        $this->assertDatabaseHas('personas', ['identificacion' => '0920000002', 'nombre' => 'Conservar']);
        $this->assertDatabaseHas('personas_importaciones', [
            'id' => $tarea->id, 'estado' => 'completada', 'total' => 4, 'procesadas' => 4,
            'importadas' => 1, 'existentes' => 2, 'omitidas' => 1,
        ]);
        $disk->assertMissing(ImportarPersonas::ruta($tarea->id));
        $this->get(route('configuracion.personas.importaciones.estado'))->assertOk()
            ->assertSee('Completada')->assertSee('100 %')->assertSee('1 / 2 / 1');
    }

    public function test_un_excel_sin_encabezados_requeridos_muestra_el_error_en_el_historial(): void
    {
        Storage::fake('local');
        $administrador = $this->administrador();

        $this->actingAs($administrador)
            ->post(route('configuracion.personas.importaciones.store'), ['archivo' => $this->excel([['Código'], ['09']])])
            ->assertRedirect(route('configuracion.personas.importaciones.index'));
        $this->artisan('queue:work', ['connection' => 'personas', '--once' => true])->assertSuccessful();

        $this->get(route('configuracion.personas.importaciones.estado'))->assertOk()
            ->assertSee('Interrumpida')->assertSee('No se encontró una hoja');
    }

    public function test_importa_el_documento_del_excel_de_gastroclinica(): void
    {
        Storage::fake('local');
        $archivo = $this->excel([
            ['ID', 'nombre1', 'nombre2', 'apellido1', 'apellido2', 'telefono1', 'telefono2', 'telefono3'],
            ['0920000001', 'Ana', 'Inés', 'Reyes', 'Soto', '042000000', '0900000000', ''],
        ]);
        $this->actingAs($this->administrador())
            ->post(route('configuracion.personas.importaciones.store'), ['archivo' => $archivo])
            ->assertRedirect(route('configuracion.personas.importaciones.index'));

        $this->artisan('queue:work', ['connection' => 'personas', '--once' => true])->assertSuccessful();

        $this->assertDatabaseHas('personas', [
            'identificacion' => '0920000001', 'nombre' => 'Ana Inés', 'apellido' => 'Reyes Soto',
            'telefono' => '042000000', 'celular' => '0900000000',
        ]);
    }

    public function test_las_actualizaciones_del_historial_no_consumen_el_limite_de_cargas(): void
    {
        $this->actingAs($this->administrador());
        for ($intento = 0; $intento < 7; $intento++) {
            $this->get(route('configuracion.personas.importaciones.estado'))->assertOk();
        }

        $this->postJson(route('configuracion.personas.importaciones.store'), [])
            ->assertUnprocessable()->assertJsonValidationErrors('archivo');
    }

    public function test_un_administrador_de_empresa_no_puede_importar_al_catalogo_global(): void
    {
        $this->actingAs($this->administrador(false));

        $this->get(route('configuracion.personas.importaciones.index'))->assertForbidden();
        $this->post(route('configuracion.personas.importaciones.store'), [])->assertForbidden();
        $this->get(route('configuracion.personas.importaciones.estado'))->assertForbidden();
        $this->postJson(route('configuracion.personas.importaciones.token'), ['nombre' => 'Integración'])->assertForbidden();
        $this->get(route('configuracion.usuarios.index'))->assertOk()
            ->assertDontSee(route('configuracion.personas.importaciones.index'));
    }

    public function test_el_administrador_global_encuentra_la_importacion_en_el_menu_lateral(): void
    {
        $this->actingAs($this->administrador())->get(route('configuracion.usuarios.index'))->assertOk()
            ->assertSee('data-workspace-link="configuracion.personas.importaciones.index"', false);
    }

    public function test_el_administrador_global_puede_emitir_un_token_de_solo_lectura_desde_la_pantalla(): void
    {
        $administrador = $this->administrador();
        $this->freezeTime();
        $this->actingAs($administrador)->get(route('configuracion.personas.importaciones.index'))->assertOk()
            ->assertSee('Generar token');

        $respuesta = $this->postJson(route('configuracion.personas.importaciones.token'), ['nombre' => 'Gastroclínica'])
            ->assertCreated()->assertHeader('Cache-Control', 'no-store, private')->assertJsonStructure(['token']);

        $token = TokenAcceso::findToken($respuesta->json('token'));
        $this->assertNotNull($token);
        $this->assertSame('Gastroclínica', $token->name);
        $this->assertSame($administrador->id, $token->tokenable_id);
        $this->assertSame(['personas:leer'], $token->abilities);
        $this->assertSame(now()->addDays(90)->toDateTimeString(), $token->expires_at->toDateTimeString());
    }

    public function test_al_volver_a_la_pantalla_se_recupera_el_avance_y_el_tiempo_estimado(): void
    {
        $administrador = $this->administrador();
        $this->freezeTime();
        DB::table('personas_importaciones')->insert([
            'id' => (string) Str::uuid(),
            'id_usuario' => $administrador->id,
            'archivo' => 'importacion-en-curso.xlsx',
            'estado' => 'procesando',
            'total' => 1000,
            'procesadas' => 250,
            'importadas' => 250,
            'inicio_progreso' => now()->subSeconds(10),
            'updated_at' => now(),
            'created_at' => now()->subSeconds(15),
        ]);

        $this->actingAs($administrador)->get(route('configuracion.personas.importaciones.index'))->assertOk()
            ->assertSee('importacion-en-curso.xlsx')->assertSee('25 %')->assertSee('~30 s');
    }

    private function administrador(bool $global = true): User
    {
        $empresa = EmpresaModel::create(['ruc' => '1273300000001', 'razon_social' => 'Empresa de prueba']);

        return User::factory()->for($empresa, 'empresa')->create(['is_admin' => true, 'is_admin_global' => $global]);
    }

    private function excel(array $filas): UploadedFile
    {
        $ruta = tempnam(sys_get_temp_dir(), 'personas-');
        $zip = new ZipArchive;
        $zip->open($ruta, ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml', '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"/>');
        $zip->addFromString('xl/workbook.xml', '<workbook xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><workbookPr/></workbook>');
        $hoja = '<worksheet xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"><sheetData>';
        $textos = [];
        foreach ($filas as $indice => $fila) {
            $numero = $indice + 1;
            $hoja .= '<row r="'.$numero.'">';
            foreach ($fila as $columna => $valor) {
                $celda = chr(65 + $columna).$numero;
                if (is_int($valor)) {
                    $hoja .= '<c r="'.$celda.'"><v>'.$valor.'</v></c>';
                } else {
                    $hoja .= '<c r="'.$celda.'" t="s"><v>'.count($textos).'</v></c>';
                    $textos[] = '<si><t>'.htmlspecialchars($valor, ENT_XML1).'</t></si>';
                }
            }
            $hoja .= '</row>';
        }
        $zip->addFromString('xl/worksheets/sheet1.xml', $hoja.'</sheetData></worksheet>');
        $zip->addFromString('xl/sharedStrings.xml', '<sst xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main">'.implode('', $textos).'</sst>');
        $zip->close();
        $contenido = file_get_contents($ruta);
        unlink($ruta);

        return UploadedFile::fake()->createWithContent('personas.xlsx', $contenido);
    }
}
