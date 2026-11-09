<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateOdontologiaFormulario033Tables extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('users') && ! $this->hasColumn('users', 'codigo_msp_acess')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('codigo_msp_acess', 60)->nullable();
            });
        }

        if (! Schema::hasTable('odont_formulario033')) {
            Schema::create('odont_formulario033', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('hcid');
                $table->unsignedInteger('id_agenda')->nullable();
                $table->string('id_paciente', 20);
                $table->string('id_doctor', 20)->nullable();
                $table->unsignedInteger('version')->default(1);
                $table->date('fecha_atencion')->nullable();
                $table->time('hora_atencion')->nullable();
                $table->string('tipo_atencion', 30)->nullable();
                $table->integer('nro_hoja')->nullable();
                $table->string('institucion_sistema', 30)->nullable();
                $table->string('unicodigo', 30)->nullable();
                $table->string('establecimiento')->nullable();
                $table->string('servicio', 30)->nullable();
                $table->text('motivo_consulta')->nullable();
                $table->text('enfermedad_actual')->nullable();
                $table->string('embarazo', 5)->nullable();
                $table->string('presion_arterial', 40)->nullable();
                $table->decimal('frecuencia_cardiaca', 8, 2)->nullable();
                $table->decimal('temperatura', 8, 2)->nullable();
                $table->decimal('frecuencia_respiratoria', 8, 2)->nullable();
                $table->tinyInteger('alerta_signos_vitales')->default(0);
                $table->text('plan_diagnostico')->nullable();
                $table->text('plan_terapeutico')->nullable();
                $table->text('plan_educacional')->nullable();
                $table->mediumText('examenes_complementarios_json')->nullable();
                $table->integer('cpod_c')->default(0);
                $table->integer('cpod_p')->default(0);
                $table->integer('cpod_o')->default(0);
                $table->integer('cpod_total')->default(0);
                $table->integer('ceod_c')->default(0);
                $table->integer('ceod_e')->default(0);
                $table->integer('ceod_o')->default(0);
                $table->integer('ceod_total')->default(0);
                $table->longText('snapshot_json');
                $table->dateTime('fecha_apertura');
                $table->date('vigente_hasta');
                $table->tinyInteger('estado')->default(1);
                $this->auditColumns($table);
                $table->unique('hcid', 'uq_odont033_hcid');
                $table->index('id_paciente', 'idx_odont033_paciente');
                $table->index('id_agenda', 'idx_odont033_agenda');
            });
        }

        if (! Schema::hasTable('odont_antecedentes')) {
            Schema::create('odont_antecedentes', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('id_formulario033');
                $table->enum('tipo', ['PERSONAL', 'FAMILIAR']);
                $table->string('antecedente', 120);
                $table->tinyInteger('marcado')->default(0);
                $table->text('descripcion')->nullable();
                $this->auditColumns($table);
                $table->index('id_formulario033', 'idx_odont_ant_form');
            });
        }

        if (! Schema::hasTable('odont_examen_estomatognatico')) {
            Schema::create('odont_examen_estomatognatico', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('id_formulario033');
                $table->string('region', 80);
                $table->tinyInteger('patologia')->default(0);
                $table->text('descripcion')->nullable();
                $this->auditColumns($table);
                $table->index('id_formulario033', 'idx_odont_est_form');
            });
        }

        if (! Schema::hasTable('odontogramas')) {
            Schema::create('odontogramas', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('id_formulario033');
                $table->dateTime('fecha_apertura');
                $table->date('vigente_hasta');
                $table->longText('snapshot_json');
                $table->tinyInteger('estado')->default(1);
                $this->auditColumns($table);
                $table->index('id_formulario033', 'idx_odontograma_form');
            });
        }

        if (! Schema::hasTable('odontograma_piezas')) {
            Schema::create('odontograma_piezas', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('id_odontograma');
                $table->string('fdi', 2);
                $table->enum('denticion', ['PERMANENTE', 'TEMPORAL']);
                $table->string('marca', 60)->nullable();
                $table->string('color', 20)->nullable();
                $table->tinyInteger('movilidad')->nullable();
                $table->tinyInteger('recesion')->nullable();
                $table->mediumText('meta_json')->nullable();
                $this->auditColumns($table);
                $table->index('id_odontograma', 'idx_odont_pieza_odo');
                $table->index('fdi', 'idx_odont_pieza_fdi');
            });
        }

        if (! Schema::hasTable('odontograma_superficies')) {
            Schema::create('odontograma_superficies', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('id_pieza');
                $table->enum('superficie', ['V', 'L', 'M', 'D', 'O']);
                $table->string('condicion', 60)->nullable();
                $table->string('color', 20)->nullable();
                $this->auditColumns($table);
                $table->index('id_pieza', 'idx_odont_sup_pieza');
            });
        }

        if (! Schema::hasTable('odont_indicadores')) {
            Schema::create('odont_indicadores', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('id_formulario033');
                $table->unsignedInteger('id_odontograma')->nullable();
                $table->decimal('ihos_placa', 8, 2)->default(0);
                $table->decimal('ihos_calculo', 8, 2)->default(0);
                $table->decimal('ihos_gingivitis', 8, 2)->default(0);
                $table->decimal('ihos_total', 8, 2)->default(0);
                $table->string('enfermedad_periodontal', 30)->nullable();
                $table->string('oclusion', 30)->nullable();
                $table->string('fluorosis', 30)->nullable();
                $this->auditColumns($table);
                $table->index('id_formulario033', 'idx_odont_ind_form');
            });
        }

        if (! Schema::hasTable('odont_ihos_detalles')) {
            Schema::create('odont_ihos_detalles', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('id_formulario033');
                $table->string('fdi', 2);
                $table->tinyInteger('placa')->nullable();
                $table->tinyInteger('calculo')->nullable();
                $table->tinyInteger('gingivitis')->nullable();
                $table->tinyInteger('ausente')->default(0);
                $this->auditColumns($table);
                $table->index('id_formulario033', 'idx_odont_ihos_form');
            });
        }

        if (! Schema::hasTable('odont_indices')) {
            Schema::create('odont_indices', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('id_formulario033');
                $table->mediumText('cpod_json')->nullable();
                $table->mediumText('ceod_json')->nullable();
                $this->auditColumns($table);
                $table->index('id_formulario033', 'idx_odont_indices_form');
            });
        }

        if (! Schema::hasTable('odont_diagnosticos')) {
            Schema::create('odont_diagnosticos', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('id_formulario033');
                $table->integer('orden')->default(1);
                $table->string('cie10', 10)->nullable();
                $table->string('descripcion')->nullable();
                $table->enum('presuntivo_definitivo', ['PRE', 'DEF'])->nullable();
                $this->auditColumns($table);
                $table->index('id_formulario033', 'idx_odont_dx_form');
                $table->index('cie10', 'idx_odont_dx_cie');
            });
        }

        if (! Schema::hasTable('odont_evoluciones')) {
            Schema::create('odont_evoluciones', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('id_formulario033');
                $table->integer('sesion')->default(1);
                $table->date('fecha')->nullable();
                $table->text('diagnostico')->nullable();
                $table->text('complicaciones')->nullable();
                $table->text('procedimientos')->nullable();
                $table->text('prescripciones')->nullable();
                $table->string('id_profesional', 20)->nullable();
                $table->string('firma')->nullable();
                $table->string('codigo_msp_acess', 60)->nullable();
                $table->integer('nro_hoja')->nullable();
                $this->auditColumns($table);
                $table->index('id_formulario033', 'idx_odont_evo_form');
                $table->index('id_profesional', 'idx_odont_evo_prof');
            });
        }
    }

    public function down(): void
    {
        foreach ([
            'odont_evoluciones', 'odont_diagnosticos', 'odont_indices',
            'odont_ihos_detalles', 'odont_indicadores', 'odontograma_superficies',
            'odontograma_piezas', 'odontogramas', 'odont_examen_estomatognatico',
            'odont_antecedentes', 'odont_formulario033',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }

    private function auditColumns(Blueprint $table): void
    {
        $table->string('id_usuariocrea', 20);
        $table->string('id_usuariomod', 20);
        $table->string('ip_creacion', 45);
        $table->string('ip_modificacion', 45);
        $table->timestamps();
    }

    private function hasColumn(string $table, string $column): bool
    {
        if (DB::connection()->getDriverName() !== 'mysql') {
            return Schema::hasColumn($table, $column);
        }

        return DB::table('information_schema.COLUMNS')
            ->where('TABLE_SCHEMA', DB::connection()->getDatabaseName())
            ->where('TABLE_NAME', $table)
            ->where('COLUMN_NAME', $column)
            ->exists();
    }
}
