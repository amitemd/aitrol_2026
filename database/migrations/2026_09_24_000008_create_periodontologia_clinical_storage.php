<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreatePeriodontologiaClinicalStorage extends Migration
{
    /**
     * Crea un almacenamiento independiente con la misma estructura estable de
     * Odontologia y migra solamente la informacion de agendas periodontales.
     * Los originales se conservan para que el despliegue sea reversible y no
     * afecte a instalaciones que ya consumen las tablas odontologicas.
     */
    public function up(): void
    {
        $pares = [
            'odont_formulario033' => 'perio_formulario033',
            'odont_antecedentes' => 'perio_antecedentes',
            'odont_examen_estomatognatico' => 'perio_examen_estomatognatico',
            'odontogramas' => 'perio_odontogramas',
            'odontograma_piezas' => 'perio_odontograma_piezas',
            'odontograma_superficies' => 'perio_odontograma_superficies',
            'odont_indicadores' => 'perio_indicadores',
            'odont_ihos_detalles' => 'perio_ihos_detalles',
            'odont_indices' => 'perio_indices',
            'odont_diagnosticos' => 'perio_diagnosticos',
            'odont_evoluciones' => 'perio_evoluciones',
            'odont_procedimientos' => 'perio_procedimientos',
            'odont_paciente_procedimientos' => 'perio_paciente_procedimientos',
            'odont_cotizaciones' => 'perio_cotizaciones',
        ];

        foreach ($pares as $origen => $destino) {
            $this->clonarTabla($origen, $destino);
        }

        $this->copiarFormulariosPeriodontales();
        $this->copiarDetallesFormulario();
        $this->copiarProcedimientosPeriodontales();
        $this->copiarCotizacionesPeriodontales();
    }

    /**
     * No se eliminan tablas en down: despues del despliegue pueden contener
     * nuevos registros periodontales que no existen en las tablas antiguas.
     */
    public function down(): void
    {
        // Migracion deliberadamente no destructiva.
    }

    private function clonarTabla(string $origen, string $destino): void
    {
        if (! Schema::hasTable($origen) || Schema::hasTable($destino)) {
            return;
        }

        if (DB::connection()->getDriverName() !== 'mysql') {
            throw new RuntimeException('La migracion periodontal requiere MySQL para clonar la estructura clinica existente.');
        }

        DB::statement('CREATE TABLE `'.$destino.'` LIKE `'.$origen.'`');
    }

    private function copiarFormulariosPeriodontales(): void
    {
        if (! $this->tablasDisponibles(['odont_formulario033', 'perio_formulario033', 'historiaclinica', 'agenda', 'especialidad'])) {
            return;
        }

        DB::statement("INSERT INTO `perio_formulario033`
            SELECT origen.*
            FROM `odont_formulario033` origen
            INNER JOIN `historiaclinica` h ON h.hcid = origen.hcid
            INNER JOIN `agenda` a ON a.id = h.id_agenda
            INNER JOIN `especialidad` e ON e.id = a.espid
            LEFT JOIN `perio_formulario033` destino
                ON destino.id = origen.id OR destino.hcid = origen.hcid
            WHERE destino.id IS NULL
              AND LOWER(e.nombre) LIKE '%periodon%'");
    }

    private function copiarDetallesFormulario(): void
    {
        $this->copiarPorFormulario('odont_antecedentes', 'perio_antecedentes');
        $this->copiarPorFormulario('odont_examen_estomatognatico', 'perio_examen_estomatognatico');
        $this->copiarPorFormulario('odont_indicadores', 'perio_indicadores');
        $this->copiarPorFormulario('odont_ihos_detalles', 'perio_ihos_detalles');
        $this->copiarPorFormulario('odont_indices', 'perio_indices');
        $this->copiarPorFormulario('odont_diagnosticos', 'perio_diagnosticos');
        $this->copiarPorFormulario('odont_evoluciones', 'perio_evoluciones');

        if ($this->tablasDisponibles(['odontogramas', 'perio_odontogramas', 'perio_formulario033'])) {
            DB::statement('INSERT INTO `perio_odontogramas`
                SELECT origen.*
                FROM `odontogramas` origen
                INNER JOIN `perio_formulario033` formulario ON formulario.id = origen.id_formulario033
                LEFT JOIN `perio_odontogramas` destino ON destino.id = origen.id
                WHERE destino.id IS NULL');
        }

        if ($this->tablasDisponibles(['odontograma_piezas', 'perio_odontograma_piezas', 'perio_odontogramas'])) {
            DB::statement('INSERT INTO `perio_odontograma_piezas`
                SELECT origen.*
                FROM `odontograma_piezas` origen
                INNER JOIN `perio_odontogramas` odontograma ON odontograma.id = origen.id_odontograma
                LEFT JOIN `perio_odontograma_piezas` destino ON destino.id = origen.id
                WHERE destino.id IS NULL');
        }

        if ($this->tablasDisponibles(['odontograma_superficies', 'perio_odontograma_superficies', 'perio_odontograma_piezas'])) {
            DB::statement('INSERT INTO `perio_odontograma_superficies`
                SELECT origen.*
                FROM `odontograma_superficies` origen
                INNER JOIN `perio_odontograma_piezas` pieza ON pieza.id = origen.id_pieza
                LEFT JOIN `perio_odontograma_superficies` destino ON destino.id = origen.id
                WHERE destino.id IS NULL');
        }
    }

    private function copiarPorFormulario(string $origen, string $destino): void
    {
        if (! $this->tablasDisponibles([$origen, $destino, 'perio_formulario033'])) {
            return;
        }

        DB::statement("INSERT INTO `{$destino}`
            SELECT origen.*
            FROM `{$origen}` origen
            INNER JOIN `perio_formulario033` formulario ON formulario.id = origen.id_formulario033
            LEFT JOIN `{$destino}` destino ON destino.id = origen.id
            WHERE destino.id IS NULL");
    }

    private function copiarProcedimientosPeriodontales(): void
    {
        if ($this->tablasDisponibles(['odont_procedimientos', 'perio_procedimientos', 'historiaclinica', 'agenda', 'especialidad'])) {
            DB::statement("INSERT INTO `perio_procedimientos`
                SELECT origen.*
                FROM `odont_procedimientos` origen
                INNER JOIN `historiaclinica` h ON h.hcid = origen.hcid
                INNER JOIN `agenda` a ON a.id = h.id_agenda
                INNER JOIN `especialidad` e ON e.id = a.espid
                LEFT JOIN `perio_procedimientos` destino ON destino.id = origen.id
                WHERE destino.id IS NULL
                  AND LOWER(e.nombre) LIKE '%periodon%'");
        }

        if ($this->tablasDisponibles(['odont_paciente_procedimientos', 'perio_paciente_procedimientos', 'perio_procedimientos'])) {
            DB::statement('INSERT INTO `perio_paciente_procedimientos`
                SELECT origen.*
                FROM `odont_paciente_procedimientos` origen
                INNER JOIN `perio_procedimientos` procedimiento ON procedimiento.id = origen.id_odont_procedimiento
                LEFT JOIN `perio_paciente_procedimientos` destino ON destino.id = origen.id
                WHERE destino.id IS NULL');
        }
    }

    private function copiarCotizacionesPeriodontales(): void
    {
        if (! $this->tablasDisponibles(['odont_cotizaciones', 'perio_cotizaciones', 'historiaclinica', 'agenda', 'especialidad'])) {
            return;
        }

        DB::statement("INSERT INTO `perio_cotizaciones`
            SELECT origen.*
            FROM `odont_cotizaciones` origen
            INNER JOIN `historiaclinica` h ON h.hcid = origen.hcid
            INNER JOIN `agenda` a ON a.id = h.id_agenda
            INNER JOIN `especialidad` e ON e.id = a.espid
            LEFT JOIN `perio_cotizaciones` destino ON destino.id = origen.id
            WHERE destino.id IS NULL
              AND LOWER(e.nombre) LIKE '%periodon%'");
    }

    private function tablasDisponibles(array $tablas): bool
    {
        foreach ($tablas as $tabla) {
            if (! Schema::hasTable($tabla)) {
                return false;
            }
        }

        return true;
    }
}
