-- Inserta exactamente los 30 documentos solicitados para V3.
-- Los campos operativos se tomaron de expdata y se eligió una fila por cada nombre de Hoja1.

-- La fuente utiliza secuencias de hasta 340; el catálogo legado usa TINYINT.
ALTER TABLE documento
    MODIFY secuencia SMALLINT UNSIGNED NOT NULL DEFAULT 1;

START TRANSACTION;

SET @documento_actor_id := (
    SELECT id FROM users WHERE estado = 1 ORDER BY id LIMIT 1
);
SET @documento_doctor_role_id := COALESCE(
    (SELECT id FROM tipousuario WHERE id = 5 AND estado = 1 LIMIT 1),
    (SELECT id FROM tipousuario WHERE estado = 1 AND nombre LIKE '%DOCTOR%' ORDER BY id LIMIT 1)
);

-- La carga debe reemplazar por completo el contenido actual del catálogo.
DELETE FROM documento;

INSERT INTO documento (
    id, secuencia, nombre, nombre_corto, proc_consul, tipo_seguro, id_seguro,
    id_subseguro, id_tipo_usuario, id_sala, est_doc_tarea, id_empresa, link,
    tipo_documento, msp, estado, formato, principal, rpt_prin, sistema,
    id_usuariocrea, id_usuariomod, ip_creacion, ip_modificacion, created_at, updated_at
) VALUES
(5, 100, '3 ULTIMAS APORTACIONES', 'APORTACIONES', 1, 0, IF(EXISTS(SELECT 1 FROM seguros WHERE id = 2), 2, NULL), IF(EXISTS(SELECT 1 FROM subseguro WHERE id = 6) AND EXISTS(SELECT 1 FROM seguros WHERE id = 2), 6, NULL), @documento_doctor_role_id, NULL, 0, NULL, NULL, 'APO', 0, 1, NULL, NULL, 1, 1, @documento_actor_id, @documento_actor_id, '127.0.0.1', '127.0.0.1', NOW(), NOW()),
(9, 110, '3 ULTIMOS COMPROBANTES DE PAGO DEL S.S.C.', 'S.S.C', 1, 0, IF(EXISTS(SELECT 1 FROM seguros WHERE id = 2), 2, NULL), IF(EXISTS(SELECT 1 FROM subseguro WHERE id = 1) AND EXISTS(SELECT 1 FROM seguros WHERE id = 2), 1, NULL), @documento_doctor_role_id, NULL, 0, NULL, NULL, 'ULTPSC', 0, 1, NULL, NULL, 1, 0, @documento_actor_id, @documento_actor_id, '127.0.0.1', '127.0.0.1', NOW(), NOW()),
(12, 190, 'ACTA DE ENTREGA', 'ACTA ENTREGA', 1, NULL, NULL, NULL, @documento_doctor_role_id, NULL, 0, IF(EXISTS(SELECT 1 FROM empresa WHERE id = '0992704152001'), '0992704152001', NULL), NULL, 'ACT', 0, 1, 'actaentrega', NULL, NULL, 1, @documento_actor_id, @documento_actor_id, '127.0.0.1', '127.0.0.1', NOW(), NOW()),
(33, 330, 'ACTA DE ENTREGA INFORME MEDICO', 'INFORME MEDICO', 1, NULL, NULL, NULL, @documento_doctor_role_id, NULL, 0, NULL, NULL, 'INFOMED', 0, 1, 'actadeentrega2', NULL, NULL, 1, @documento_actor_id, @documento_actor_id, '127.0.0.1', '127.0.0.1', NOW(), NOW()),
(8, 60, 'CARNET CAMPESINO', 'CARNET CAM', 2, 0, IF(EXISTS(SELECT 1 FROM seguros WHERE id = 2), 2, NULL), IF(EXISTS(SELECT 1 FROM subseguro WHERE id = 1) AND EXISTS(SELECT 1 FROM seguros WHERE id = 2), 1, NULL), @documento_doctor_role_id, NULL, 0, NULL, NULL, 'CRTCAM', 0, 1, NULL, NULL, 1, 1, @documento_actor_id, @documento_actor_id, '127.0.0.1', '127.0.0.1', NOW(), NOW()),
(6, 70, 'CARNET JUBILADOS', 'CARNET JUB', 2, 0, IF(EXISTS(SELECT 1 FROM seguros WHERE id = 2), 2, NULL), IF(EXISTS(SELECT 1 FROM subseguro WHERE id = 2) AND EXISTS(SELECT 1 FROM seguros WHERE id = 2), 2, NULL), @documento_doctor_role_id, NULL, 0, NULL, NULL, 'CRTJBL', 0, 1, NULL, NULL, 1, 1, @documento_actor_id, @documento_actor_id, '127.0.0.1', '127.0.0.1', NOW(), NOW()),
(7, 80, 'CARNET MONTEPIO', 'CARNET MTP', 2, 0, IF(EXISTS(SELECT 1 FROM seguros WHERE id = 2), 2, NULL), IF(EXISTS(SELECT 1 FROM subseguro WHERE id = 3) AND EXISTS(SELECT 1 FROM seguros WHERE id = 2), 3, NULL), @documento_doctor_role_id, NULL, 0, NULL, NULL, 'CRTMTP', 0, 1, NULL, NULL, 1, 1, @documento_actor_id, @documento_actor_id, '127.0.0.1', '127.0.0.1', NOW(), NOW()),
(97, 340, 'CARTA SEGURO PRIVADO', 'CARTASEGPRI', 1, NULL, NULL, NULL, @documento_doctor_role_id, NULL, 0, NULL, NULL, 'CARSEGPRI', 0, 1, 'cartasegprivado', NULL, NULL, 1, @documento_actor_id, @documento_actor_id, '127.0.0.1', '127.0.0.1', NOW(), NOW()),
(82, 315, 'COBERTURA DE SALUD PRIVADA - MAMÁ', 'COBERTURA PM', 2, 0, NULL, NULL, @documento_doctor_role_id, NULL, 0, NULL, NULL, 'COB_PM', 0, 1, NULL, NULL, NULL, 1, @documento_actor_id, @documento_actor_id, '127.0.0.1', '127.0.0.1', NOW(), NOW()),
(81, 316, 'COBERTURA DE SALUD PRIVADA - PAPÁ', 'COBERTURA PP', 2, 0, NULL, NULL, @documento_doctor_role_id, NULL, 0, NULL, NULL, 'COB_PP', 0, 1, NULL, NULL, NULL, 1, @documento_actor_id, @documento_actor_id, '127.0.0.1', '127.0.0.1', NOW(), NOW()),
(84, 312, 'COBERTURA DE SALUD PUBLICA - MAMÁ', 'COBERTURA M', 2, 0, NULL, NULL, @documento_doctor_role_id, NULL, 0, NULL, NULL, 'COB_M', 0, 1, NULL, NULL, NULL, 1, @documento_actor_id, @documento_actor_id, '127.0.0.1', '127.0.0.1', NOW(), NOW()),
(80, 313, 'COBERTURA DE SALUD PUBLICA - PAPÁ', 'COBERTURA P', 2, 0, NULL, NULL, @documento_doctor_role_id, NULL, 0, NULL, NULL, 'COB_P', 0, 1, NULL, NULL, NULL, 1, @documento_actor_id, @documento_actor_id, '127.0.0.1', '127.0.0.1', NOW(), NOW()),
(32, 314, 'COBERTURA SALUD PRIVADA', 'COBER PRIVA', 2, 0, NULL, NULL, @documento_doctor_role_id, NULL, 0, NULL, 'http://www.acess.gob.ec/acess-app-servicio-ciudadano/public/medicinaPrepagada/consulta.jsf;jsessionid=5df5e0a0ca9415e24ef20f20bcf8', 'CBRPRV', 0, 1, NULL, NULL, NULL, 1, @documento_actor_id, @documento_actor_id, '127.0.0.1', '127.0.0.1', NOW(), NOW()),
(34, 304, 'COBERTURA SALUD PUBLICA', 'COBERTURA P/M', 1, 0, IF(EXISTS(SELECT 1 FROM seguros WHERE id = 2), 2, NULL), IF(EXISTS(SELECT 1 FROM subseguro WHERE id = 5) AND EXISTS(SELECT 1 FROM seguros WHERE id = 2), 5, NULL), @documento_doctor_role_id, NULL, 0, NULL, NULL, 'COBASOPM', 2, 1, NULL, NULL, NULL, 0, @documento_actor_id, @documento_actor_id, '127.0.0.1', '127.0.0.1', NOW(), NOW()),
(4, 180, 'CODIGO DE VALIDACION', 'CODIGO VAL', 1, 0, NULL, NULL, @documento_doctor_role_id, NULL, 0, NULL, NULL, 'VAL', 0, 1, NULL, NULL, NULL, 1, @documento_actor_id, @documento_actor_id, '127.0.0.1', '127.0.0.1', NOW(), NOW()),
(11, 200, 'CONSENTIMIENTO INFORMADO', 'CONSENTIMIENTO', 1, NULL, NULL, NULL, @documento_doctor_role_id, NULL, 0, NULL, NULL, 'CONINF', 0, 1, 'consentimiento', NULL, NULL, 1, @documento_actor_id, @documento_actor_id, '127.0.0.1', '127.0.0.1', NOW(), NOW()),
(88, 221, 'CONSENTIMIENTOS DE PROCEDIMIENTOS', 'CONS_PROC', 1, NULL, NULL, NULL, @documento_doctor_role_id, NULL, 0, NULL, NULL, 'CONS_PROC', 0, 1, NULL, NULL, NULL, 1, @documento_actor_id, @documento_actor_id, '127.0.0.1', '127.0.0.1', NOW(), NOW()),
(18, 230, 'CONTRAREFERENCIA', 'CONTRAREF', 1, 0, IF(EXISTS(SELECT 1 FROM seguros WHERE id = 5), 5, NULL), NULL, @documento_doctor_role_id, NULL, 0, NULL, NULL, 'CTRREF', 0, 1, 'contrareferencia', NULL, NULL, 1, @documento_actor_id, @documento_actor_id, '127.0.0.1', '127.0.0.1', NOW(), NOW()),
(43, 22, 'COPIA CEDULA (Padre/Madre)', 'CEDPM', 1, 0, IF(EXISTS(SELECT 1 FROM seguros WHERE id = 2), 2, NULL), IF(EXISTS(SELECT 1 FROM subseguro WHERE id = 5) AND EXISTS(SELECT 1 FROM seguros WHERE id = 2), 5, NULL), @documento_doctor_role_id, NULL, 0, NULL, NULL, 'CEDPM', 0, 1, NULL, NULL, NULL, 0, @documento_actor_id, @documento_actor_id, '127.0.0.1', '127.0.0.1', NOW(), NOW()),
(56, 30, 'COPIA DE CEDULA ACOMPAÑANTE', 'ACOM', 1, 2, NULL, NULL, @documento_doctor_role_id, NULL, 0, NULL, NULL, 'ACOM', 0, 1, NULL, NULL, NULL, 1, @documento_actor_id, @documento_actor_id, '127.0.0.1', '127.0.0.1', NOW(), NOW()),
(1, 10, 'COPIA DE CEDULA PACIENTE.', 'CEDULA', 2, 0, NULL, NULL, @documento_doctor_role_id, NULL, 0, NULL, NULL, 'CED', 4, 1, NULL, NULL, NULL, 0, @documento_actor_id, @documento_actor_id, '127.0.0.1', '127.0.0.1', NOW(), NOW()),
(79, 21, 'COPIA DE CEDULA PAPÁ', 'CEDP', 1, 0, IF(EXISTS(SELECT 1 FROM seguros WHERE id = 5), 5, NULL), IF(EXISTS(SELECT 1 FROM subseguro WHERE id = 26) AND EXISTS(SELECT 1 FROM seguros WHERE id = 5), 26, NULL), @documento_doctor_role_id, NULL, 0, NULL, NULL, 'CEDP', 0, 1, NULL, NULL, NULL, 1, @documento_actor_id, @documento_actor_id, '127.0.0.1', '127.0.0.1', NOW(), NOW()),
(60, 20, 'COPIA DE CEDULA PRINCIPAL', 'COPIA_PRIN', 2, 0, NULL, NULL, @documento_doctor_role_id, NULL, 0, NULL, NULL, 'COPIAPRIN', 0, 1, NULL, 0, NULL, 1, @documento_actor_id, @documento_actor_id, '127.0.0.1', '127.0.0.1', NOW(), NOW()),
(48, 40, 'CREDENCIAL DEL TITULAR', 'CREDENCIAL', 1, 0, IF(EXISTS(SELECT 1 FROM seguros WHERE id = 3), 3, NULL), NULL, @documento_doctor_role_id, NULL, 0, NULL, NULL, 'CREDISS', 0, 1, NULL, NULL, 1, 1, @documento_actor_id, @documento_actor_id, '127.0.0.1', '127.0.0.1', NOW(), NOW()),
(39, 220, 'FORMULARIO 012 ORDEN DE PROCEDIMIENTO IESS', 'F012', 1, 0, NULL, NULL, @documento_doctor_role_id, NULL, 0, NULL, NULL, 'F012', 0, 1, NULL, NULL, NULL, 1, @documento_actor_id, @documento_actor_id, '127.0.0.1', '127.0.0.1', NOW(), NOW()),
(38, 170, 'FORMULARIO 053 REFERENCIA DE DERIVACION', 'F053', 1, 0, NULL, NULL, @documento_doctor_role_id, NULL, 0, NULL, NULL, 'F053', 0, 1, NULL, NULL, NULL, 1, @documento_actor_id, @documento_actor_id, '127.0.0.1', '127.0.0.1', NOW(), NOW()),
(63, 210, 'FORMULARIO 07', 'F07', 1, 0, NULL, NULL, @documento_doctor_role_id, NULL, 0, NULL, NULL, 'F07', 0, 1, NULL, NULL, 1, 1, @documento_actor_id, @documento_actor_id, '127.0.0.1', '127.0.0.1', NOW(), NOW()),
(47, 310, 'INFORME MEDICO', 'IMED', 1, 0, IF(EXISTS(SELECT 1 FROM seguros WHERE id = 3), 3, NULL), NULL, @documento_doctor_role_id, NULL, 0, NULL, NULL, 'IMED', 0, 1, NULL, NULL, NULL, 0, @documento_actor_id, @documento_actor_id, '127.0.0.1', '127.0.0.1', NOW(), NOW()),
(10, 120, 'PASE DE LA COMUNA', 'PASE', 1, 0, IF(EXISTS(SELECT 1 FROM seguros WHERE id = 2), 2, NULL), IF(EXISTS(SELECT 1 FROM subseguro WHERE id = 1) AND EXISTS(SELECT 1 FROM seguros WHERE id = 2), 1, NULL), @documento_doctor_role_id, NULL, 0, NULL, NULL, 'PSECOM', 0, 1, NULL, NULL, 1, 1, @documento_actor_id, @documento_actor_id, '127.0.0.1', '127.0.0.1', NOW(), NOW()),
(24, 121, 'PASE DE LA COMUNA (Principal)', 'PASE', 1, 0, IF(EXISTS(SELECT 1 FROM seguros WHERE id = 2), 2, NULL), IF(EXISTS(SELECT 1 FROM subseguro WHERE id = 7) AND EXISTS(SELECT 1 FROM seguros WHERE id = 2), 7, NULL), @documento_doctor_role_id, NULL, 0, NULL, NULL, 'PSECO1', 0, 1, NULL, NULL, 10, 1, @documento_actor_id, @documento_actor_id, '127.0.0.1', '127.0.0.1', NOW(), NOW())
ON DUPLICATE KEY UPDATE
    secuencia = VALUES(secuencia),
    nombre = VALUES(nombre),
    nombre_corto = VALUES(nombre_corto),
    proc_consul = VALUES(proc_consul),
    tipo_seguro = VALUES(tipo_seguro),
    id_seguro = VALUES(id_seguro),
    id_subseguro = VALUES(id_subseguro),
    id_tipo_usuario = VALUES(id_tipo_usuario),
    id_sala = VALUES(id_sala),
    est_doc_tarea = VALUES(est_doc_tarea),
    id_empresa = VALUES(id_empresa),
    link = VALUES(link),
    msp = VALUES(msp),
    estado = VALUES(estado),
    formato = VALUES(formato),
    principal = VALUES(principal),
    rpt_prin = VALUES(rpt_prin),
    sistema = VALUES(sistema),
    id_usuariomod = VALUES(id_usuariomod),
    ip_modificacion = VALUES(ip_modificacion),
    updated_at = VALUES(updated_at);

COMMIT;

SELECT COUNT(*) AS total_documentos,
       COUNT(DISTINCT tipo_documento) AS codigos_unicos
FROM documento;

