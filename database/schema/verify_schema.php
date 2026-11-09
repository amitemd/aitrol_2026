<?php

/**
 * Validates the baseline in an isolated temporary database.
 * It never connects to or modifies the configured application database.
 */
$schemaPath = __DIR__.'/aitrol_legacy_structure.sql';
$sql = file_get_contents($schemaPath);
if ($sql === false) {
    fwrite(STDERR, 'No se pudo leer el esquema base: '.$schemaPath.PHP_EOL);
    exit(2);
}

preg_match_all('/CREATE TABLE `([^`]+)`\s*\([\s\S]*?\) ENGINE=.*?;/i', $sql, $matches, PREG_SET_ORDER);
preg_match_all('/CONSTRAINT `[^`]+` FOREIGN KEY/i', $sql, $foreignKeys);
preg_match_all('/PRIMARY KEY\s*\(/i', $sql, $primaryKeys);
preg_match_all('/\b(?:UNIQUE )?KEY `[^`]+`\s*\(/i', $sql, $secondaryKeys);
preg_match_all('/\bINSERT\s+INTO\b/i', $sql, $inserts);

$staticErrors = [];
if (count($matches) !== 802) {
    $staticErrors[] = 'se esperaban 802 tablas y se encontraron '.count($matches);
}
if (count($foreignKeys[0]) !== 2018) {
    $staticErrors[] = 'se esperaban 2018 claves foraneas y se encontraron '.count($foreignKeys[0]);
}
if (count($inserts[0]) !== 0) {
    $staticErrors[] = 'el esquema contiene sentencias INSERT y debe estar vacio';
}

if (! empty($staticErrors)) {
    fwrite(STDERR, 'Esquema base invalido: '.implode('; ', $staticErrors).'.'.PHP_EOL);
    exit(2);
}

echo 'Verificacion estatica correcta: 802 tablas, '
    .count($foreignKeys[0]).' claves foraneas, '
    .count($primaryKeys[0]).' claves primarias y '
    .count($secondaryKeys[0]).' indices secundarios/unique.'.PHP_EOL;

if (in_array('--static-only', $argv, true)) {
    exit(0);
}

$environment = [];
foreach (file(__DIR__.'/../../.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
    $line = trim($line);
    if ($line === '' || strpos($line, '#') === 0 || strpos($line, '=') === false) {
        continue;
    }
    [$key, $value] = explode('=', $line, 2);
    $environment[trim($key)] = trim(trim($value), "\"'");
}

$host = isset($environment['DB_HOST']) ? $environment['DB_HOST'] : '127.0.0.1';
$port = isset($environment['DB_PORT']) ? $environment['DB_PORT'] : '3306';
$user = isset($environment['DB_USERNAME']) ? $environment['DB_USERNAME'] : 'root';
$password = isset($environment['DB_PASSWORD']) ? $environment['DB_PASSWORD'] : '';
$database = '_aitrol_schema_verify_'.strtolower(substr(str_replace('.', '', uniqid('', true)), -12));
$pdo = null;
$exitCode = 0;

try {
    $pdo = new PDO('mysql:host='.$host.';port='.$port.';charset=utf8', $user, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    ]);
    $pdo->exec('CREATE DATABASE `'.$database.'` CHARACTER SET utf8 COLLATE utf8_unicode_ci');
    $pdo->exec('USE `'.$database.'`');
    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
    $pdo->exec('SET UNIQUE_CHECKS=0');

    foreach ($matches as $match) {
        try {
            $pdo->exec($match[0]);
        } catch (Exception $exception) {
            throw new RuntimeException('Tabla '.$match[1].': '.$exception->getMessage(), 0, $exception);
        }
    }

    $created = (int) $pdo->query('SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '.$pdo->quote($database))->fetchColumn();
    echo 'Esquema valido: '.$created.' tablas creadas en una base temporal.'.PHP_EOL;
} catch (Exception $exception) {
    $exitCode = 2;
    fwrite(STDERR, 'No se pudo completar la verificacion aislada: '.$exception->getMessage().PHP_EOL);
} finally {
    if ($pdo instanceof PDO) {
        $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
        $pdo->exec('DROP DATABASE IF EXISTS `'.$database.'`');
    }
}

exit($exitCode);
