-- Tarifario referencial odontologico en USD.
-- Fecha: 2026-09-14
--
-- Reglas de seguridad:
--   * Solo afecta procedimientos activos de la especialidad 3 (Odontologia).
--   * Solo completa precios que actualmente estan en NULL.
--   * Conserva un respaldo permanente de cada valor anterior antes de actualizar.
--   * Es idempotente: al volver a ejecutarlo no reemplaza precios editados manualmente.

SET NAMES utf8mb4;
START TRANSACTION;

CREATE TABLE IF NOT EXISTS procedimiento_odontologia_precios_bkp_20260914 (
    id_procedimiento INT NOT NULL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    precio_anterior DECIMAL(12,2) NULL,
    respaldado_en DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO procedimiento_odontologia_precios_bkp_20260914
    (id_procedimiento, nombre, precio_anterior, respaldado_en)
SELECT p.id, p.nombre, p.precio, NOW()
FROM procedimiento p
WHERE p.id_especialidad = 3
  AND p.estado = 1
  AND p.deleted_at IS NULL
  AND p.precio IS NULL;

UPDATE procedimiento p
SET p.precio = CASE p.observacion
    WHEN 'Consulta, diagnóstico y planificación' THEN CASE
        WHEN LOWER(p.nombre) LIKE '%biopsia excisional%' THEN 160.00
        WHEN LOWER(p.nombre) LIKE '%biopsia incisional%' THEN 120.00
        WHEN LOWER(p.nombre) LIKE '%tomografía%' THEN 25.00
        WHEN LOWER(p.nombre) LIKE '%modelos de estudio%' THEN 45.00
        WHEN LOWER(p.nombre) LIKE '%impresiones dentales%' THEN 35.00
        WHEN LOWER(p.nombre) LIKE '%fotografías clínicas%' THEN 25.00
        WHEN LOWER(p.nombre) LIKE '%registro de mordida%' THEN 25.00
        WHEN LOWER(p.nombre) LIKE '%radiografía%' THEN 20.00
        WHEN LOWER(p.nombre) LIKE '%interconsulta%' OR LOWER(p.nombre) LIKE '%referencia%' THEN 25.00
        WHEN LOWER(p.nombre) LIKE '%consulta odontológica inicial%' THEN 30.00
        WHEN LOWER(p.nombre) LIKE '%consulta odontológica subsecuente%' THEN 20.00
        WHEN LOWER(p.nombre) LIKE '%control%' THEN 18.00
        WHEN LOWER(p.nombre) LIKE '%evaluación%' OR LOWER(p.nombre) LIKE '%examen clínico%' THEN 25.00
        WHEN LOWER(p.nombre) LIKE '%sondaje%' THEN 30.00
        ELSE 20.00 END

    WHEN 'Promoción y prevención de salud bucal' THEN CASE
        WHEN LOWER(p.nombre) LIKE '%tratamiento intensivo%' THEN 65.00
        WHEN LOWER(p.nombre) LIKE '%profilaxis y aplicación%' OR LOWER(p.nombre) LIKE '%profilaxis y fluorización%' THEN 50.00
        WHEN LOWER(p.nombre) LIKE '%sellante%' THEN 25.00
        WHEN LOWER(p.nombre) LIKE '%profilaxis%' OR LOWER(p.nombre) LIKE '%limpieza dental%' THEN 35.00
        WHEN LOWER(p.nombre) LIKE '%flúor%' THEN 22.00
        WHEN LOWER(p.nombre) LIKE '%mantenimiento%' THEN 30.00
        WHEN LOWER(p.nombre) LIKE '%control preventivo%' OR LOWER(p.nombre) LIKE '%reevaluación%' THEN 20.00
        WHEN LOWER(p.nombre) LIKE '%educación%' OR LOWER(p.nombre) LIKE '%instrucción%' OR LOWER(p.nombre) LIKE '%consejería%' THEN 15.00
        ELSE 20.00 END

    WHEN 'Anestesia y manejo del paciente' THEN CASE
        WHEN LOWER(p.nombre) LIKE '%anestesia general%' THEN 550.00
        WHEN LOWER(p.nombre) LIKE '%sedación%' THEN 120.00
        WHEN LOWER(p.nombre) LIKE '%regional%' OR LOWER(p.nombre) LIKE '%troncular%' THEN 28.00
        WHEN LOWER(p.nombre) LIKE '%infiltrativa%' OR LOWER(p.nombre) LIKE '%pediátricos%' THEN 18.00
        WHEN LOWER(p.nombre) LIKE '%tópica%' THEN 10.00
        ELSE 20.00 END

    WHEN 'Operatoria y restauración dental' THEN CASE
        WHEN LOWER(p.nombre) LIKE '%perno y corona%' THEN 320.00
        WHEN LOWER(p.nombre) LIKE '%carilla%' THEN 190.00
        WHEN LOWER(p.nombre) LIKE '%reconstrucción coronaria%' THEN 110.00
        WHEN LOWER(p.nombre) LIKE '%fractura de esmalte y dentina%' THEN 95.00
        WHEN LOWER(p.nombre) LIKE '%fractura de esmalte%' THEN 70.00
        WHEN LOWER(p.nombre) LIKE '%resina%' THEN 70.00
        WHEN LOWER(p.nombre) LIKE '%ionómero%' THEN 50.00
        WHEN LOWER(p.nombre) LIKE '%amalgama%' OR LOWER(p.nombre) LIKE '%aleación de plata%' THEN 55.00
        WHEN LOWER(p.nombre) LIKE '%definitiva%' THEN 65.00
        WHEN LOWER(p.nombre) LIKE '%temporal%' THEN 30.00
        WHEN LOWER(p.nombre) LIKE '%protección pulpar%' THEN 40.00
        WHEN LOWER(p.nombre) LIKE '%ajuste%' OR LOWER(p.nombre) LIKE '%pulido%' THEN 25.00
        ELSE 50.00 END

    WHEN 'Endodoncia' THEN CASE
        WHEN LOWER(p.nombre) LIKE '%apicectomía%' THEN 280.00
        WHEN LOWER(p.nombre) LIKE '%retratamiento%' THEN 220.00
        WHEN LOWER(p.nombre) LIKE '%dientes permanentes%' THEN 190.00
        WHEN LOWER(p.nombre) LIKE '%dientes temporales%' THEN 120.00
        WHEN LOWER(p.nombre) LIKE '%tratamiento de conductos%' THEN 180.00
        WHEN LOWER(p.nombre) LIKE '%biopulpectomía%' OR LOWER(p.nombre) LIKE '%necropulpectomía%' THEN 150.00
        WHEN LOWER(p.nombre) LIKE '%pulpectomía%' THEN 130.00
        WHEN LOWER(p.nombre) LIKE '%pulpotomía%' THEN 90.00
        WHEN LOWER(p.nombre) LIKE '%restauración definitiva%' THEN 75.00
        WHEN LOWER(p.nombre) LIKE '%pulpitis%' OR LOWER(p.nombre) LIKE '%necrosis%' OR LOWER(p.nombre) LIKE '%gangrena%' THEN 140.00
        WHEN LOWER(p.nombre) LIKE '%absceso%' OR LOWER(p.nombre) LIKE '%periodontitis apical%' THEN 110.00
        WHEN LOWER(p.nombre) LIKE '%apertura cameral%' OR LOWER(p.nombre) LIKE '%drenaje%' THEN 55.00
        WHEN LOWER(p.nombre) LIKE '%obturación de conductos%' OR LOWER(p.nombre) LIKE '%preparación biomecánica%' THEN 85.00
        WHEN LOWER(p.nombre) LIKE '%control radiográfico%' THEN 20.00
        ELSE 45.00 END

    WHEN 'Periodoncia' THEN CASE
        WHEN LOWER(p.nombre) LIKE '%regeneración tisular%' THEN 480.00
        WHEN LOWER(p.nombre) LIKE '%cirugía periodontal regenerativa%' THEN 450.00
        WHEN LOWER(p.nombre) LIKE '%cirugía mucogingival%' THEN 380.00
        WHEN LOWER(p.nombre) LIKE '%cirugía periodontal resectiva%' THEN 350.00
        WHEN LOWER(p.nombre) LIKE '%alargamiento de corona%' THEN 220.00
        WHEN LOWER(p.nombre) LIKE '%gingivectomía%' OR LOWER(p.nombre) LIKE '%gingivoplastia%' THEN 190.00
        WHEN LOWER(p.nombre) LIKE '%campo abierto%' THEN 180.00
        WHEN LOWER(p.nombre) LIKE '%campo cerrado%' THEN 95.00
        WHEN LOWER(p.nombre) LIKE '%ferulización%' THEN 120.00
        WHEN LOWER(p.nombre) LIKE '%extracción%' THEN 80.00
        WHEN LOWER(p.nombre) LIKE '%absceso%' OR LOWER(p.nombre) LIKE '%pericoronal%' THEN 60.00
        WHEN LOWER(p.nombre) LIKE '%detartraje%' THEN 50.00
        WHEN LOWER(p.nombre) LIKE '%mantenimiento%' OR LOWER(p.nombre) LIKE '%reevaluación%' THEN 45.00
        WHEN LOWER(p.nombre) LIKE '%tratamiento de periodontitis%' THEN 110.00
        ELSE 40.00 END

    WHEN 'Odontopediatría' THEN CASE
        WHEN LOWER(p.nombre) LIKE '%anestesia general%' THEN 550.00
        WHEN LOWER(p.nombre) LIKE '%mantenedor de espacio%' THEN 140.00
        WHEN LOWER(p.nombre) LIKE '%férula%' THEN 110.00
        WHEN LOWER(p.nombre) LIKE '%biopulpectomía%' OR LOWER(p.nombre) LIKE '%necropulpectomía%' THEN 120.00
        WHEN LOWER(p.nombre) LIKE '%pulpotomía%' THEN 85.00
        WHEN LOWER(p.nombre) LIKE '%exodoncia%' OR LOWER(p.nombre) LIKE '%extracción%' THEN 55.00
        WHEN LOWER(p.nombre) LIKE '%restauración%' THEN 55.00
        WHEN LOWER(p.nombre) LIKE '%trauma%' OR LOWER(p.nombre) LIKE '%luxación%' OR LOWER(p.nombre) LIKE '%intrusión%' OR LOWER(p.nombre) LIKE '%extrusión%' OR LOWER(p.nombre) LIKE '%avulsión%' THEN 90.00
        WHEN LOWER(p.nombre) LIKE '%impresión%' OR LOWER(p.nombre) LIKE '%banda%' OR LOWER(p.nombre) LIKE '%cementación%' THEN 40.00
        ELSE 50.00 END

    WHEN 'Odontopediatría / Cirugía oral' THEN 75.00

    WHEN 'Cirugía oral' THEN CASE
        WHEN LOWER(p.nombre) LIKE '%implante dental%' THEN 850.00
        WHEN LOWER(p.nombre) LIKE '%quistes%' OR LOWER(p.nombre) LIKE '%tumores%' THEN 650.00
        WHEN LOWER(p.nombre) LIKE '%injerto óseo%' THEN 550.00
        WHEN LOWER(p.nombre) LIKE '%glándulas salivales%' OR LOWER(p.nombre) LIKE '%seno maxilar%' THEN 400.00
        WHEN LOWER(p.nombre) LIKE '%diente retenido%' OR LOWER(p.nombre) LIKE '%diente incluido%' OR LOWER(p.nombre) LIKE '%diente impactado%' THEN 250.00
        WHEN LOWER(p.nombre) LIKE '%alveoloplastia%' OR LOWER(p.nombre) LIKE '%proceso alveolar%' OR LOWER(p.nombre) LIKE '%exostosis%' THEN 220.00
        WHEN LOWER(p.nombre) LIKE '%frenectomía%' OR LOWER(p.nombre) LIKE '%operculectomía%' THEN 180.00
        WHEN LOWER(p.nombre) LIKE '%biopsia%' OR LOWER(p.nombre) LIKE '%mucocele%' THEN 180.00
        WHEN LOWER(p.nombre) LIKE '%osteotomía%' OR LOWER(p.nombre) LIKE '%ostectomía%' OR LOWER(p.nombre) LIKE '%odontosección%' THEN 140.00
        WHEN LOWER(p.nombre) LIKE '%exodoncia simple%' OR LOWER(p.nombre) LIKE '%pieza permanente%' THEN 80.00
        WHEN LOWER(p.nombre) LIKE '%pieza temporal%' THEN 55.00
        WHEN LOWER(p.nombre) LIKE '%supernumerario%' OR LOWER(p.nombre) LIKE '%raíz retenida%' THEN 110.00
        WHEN LOWER(p.nombre) LIKE '%absceso%' OR LOWER(p.nombre) LIKE '%drenaje%' OR LOWER(p.nombre) LIKE '%alveolitis%' OR LOWER(p.nombre) LIKE '%pericoronaritis%' THEN 70.00
        WHEN LOWER(p.nombre) LIKE '%sutura%' OR LOWER(p.nombre) LIKE '%retiro de suturas%' THEN 35.00
        WHEN LOWER(p.nombre) LIKE '%control de osteointegración%' THEN 45.00
        ELSE 120.00 END

    WHEN 'Cirugía maxilofacial y ATM' THEN CASE
        WHEN LOWER(p.nombre) LIKE '%cáncer%' OR LOWER(p.nombre) LIKE '%oncológica%' THEN 2500.00
        WHEN LOWER(p.nombre) LIKE '%cirugía bimaxilar%' THEN 3200.00
        WHEN LOWER(p.nombre) LIKE '%fractura%' OR LOWER(p.nombre) LIKE '%fijación%' THEN 1800.00
        WHEN LOWER(p.nombre) LIKE '%cirugía del maxilar%' OR LOWER(p.nombre) LIKE '%cirugía de la mandíbula%' THEN 2200.00
        WHEN LOWER(p.nombre) LIKE '%mentoplastia%' OR LOWER(p.nombre) LIKE '%bichectomía%' OR LOWER(p.nombre) LIKE '%mioplastia%' THEN 900.00
        WHEN LOWER(p.nombre) LIKE '%labioplastia%' OR LOWER(p.nombre) LIKE '%palatoplastia%' OR LOWER(p.nombre) LIKE '%hendiduras%' THEN 1400.00
        WHEN LOWER(p.nombre) LIKE '%prótesis maxilofacial compleja%' OR LOWER(p.nombre) LIKE '%reconstrucción facial%' THEN 1200.00
        WHEN LOWER(p.nombre) LIKE '%prótesis%' OR LOWER(p.nombre) LIKE '%obturador palatino%' THEN 650.00
        WHEN LOWER(p.nombre) LIKE '%reducción de luxación%' THEN 180.00
        WHEN LOWER(p.nombre) LIKE '%plastía%' THEN 250.00
        WHEN LOWER(p.nombre) LIKE '%curación%' THEN 45.00
        ELSE 150.00 END

    WHEN 'Ortodoncia y ortopedia dentofacial' THEN CASE
        WHEN LOWER(p.nombre) LIKE '%aparatología fija%' THEN 850.00
        WHEN LOWER(p.nombre) LIKE '%tratamiento ortopédico%' OR LOWER(p.nombre) LIKE '%expansión esquelética%' THEN 650.00
        WHEN LOWER(p.nombre) LIKE '%máscara facial%' OR LOWER(p.nombre) LIKE '%placa de expansión%' THEN 380.00
        WHEN LOWER(p.nombre) LIKE '%quad helix%' OR LOWER(p.nombre) LIKE '%arco transpalatino%' OR LOWER(p.nombre) LIKE '%arco lingual%' OR LOWER(p.nombre) LIKE '%lip bumper%' THEN 250.00
        WHEN LOWER(p.nombre) LIKE '%adhesión de brackets%' THEN 300.00
        WHEN LOWER(p.nombre) LIKE '%adhesión de tubos%' OR LOWER(p.nombre) LIKE '%cementación de bandas%' THEN 120.00
        WHEN LOWER(p.nombre) LIKE '%retención%' THEN 180.00
        WHEN LOWER(p.nombre) LIKE '%diagnóstico%' OR LOWER(p.nombre) LIKE '%análisis%' THEN 60.00
        WHEN LOWER(p.nombre) LIKE '%control%' OR LOWER(p.nombre) LIKE '%ajuste%' OR LOWER(p.nombre) LIKE '%activación%' THEN 45.00
        WHEN LOWER(p.nombre) LIKE '%tratamiento%' THEN 350.00
        ELSE 85.00 END

    WHEN 'Rehabilitación oral y prótesis' THEN CASE
        WHEN LOWER(p.nombre) LIKE '%soportada por implantes%' THEN 1800.00
        WHEN LOWER(p.nombre) LIKE '%prótesis fija%' THEN 750.00
        WHEN LOWER(p.nombre) LIKE '%prótesis parcial removible%' THEN 550.00
        WHEN LOWER(p.nombre) LIKE '%prótesis total%' THEN 700.00
        WHEN LOWER(p.nombre) LIKE '%corona sobre perno%' THEN 420.00
        WHEN LOWER(p.nombre) LIKE '%corona acrílica%' THEN 180.00
        WHEN LOWER(p.nombre) LIKE '%perno colado%' THEN 180.00
        WHEN LOWER(p.nombre) LIKE '%perno no metálico%' THEN 220.00
        WHEN LOWER(p.nombre) LIKE '%incrustación%' THEN 220.00
        WHEN LOWER(p.nombre) LIKE '%reparación%' OR LOWER(p.nombre) LIKE '%reacondicionamiento%' THEN 120.00
        WHEN LOWER(p.nombre) LIKE '%instalación%' OR LOWER(p.nombre) LIKE '%cementación%' THEN 90.00
        WHEN LOWER(p.nombre) LIKE '%impresión%' OR LOWER(p.nombre) LIKE '%registro de relaciones%' OR LOWER(p.nombre) LIKE '%prueba de dientes%' THEN 65.00
        WHEN LOWER(p.nombre) LIKE '%ajuste%' OR LOWER(p.nombre) LIKE '%control%' OR LOWER(p.nombre) LIKE '%mantenimiento%' THEN 45.00
        WHEN LOWER(p.nombre) LIKE '%carilla%' OR LOWER(p.nombre) LIKE '%estética%' THEN 190.00
        ELSE 150.00 END

    ELSE 25.00
END,
p.updated_at = NOW()
WHERE p.id_especialidad = 3
  AND p.estado = 1
  AND p.deleted_at IS NULL
  AND p.precio IS NULL;

COMMIT;

SELECT COUNT(*) AS procedimientos_odontologia,
       SUM(precio IS NOT NULL) AS con_precio,
       SUM(precio IS NULL) AS sin_precio,
       MIN(precio) AS precio_minimo,
       MAX(precio) AS precio_maximo
FROM procedimiento
WHERE id_especialidad = 3
  AND estado = 1
  AND deleted_at IS NULL;
