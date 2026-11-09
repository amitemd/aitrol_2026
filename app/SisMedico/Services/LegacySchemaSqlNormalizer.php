<?php

namespace Sis_medico\Services;

/**
 * Makes the legacy schema portable across supported MySQL and MariaDB versions.
 *
 * The source dump describes the logical schema. Storage hints from the server
 * that produced the dump and defaults forbidden by other engines are removed
 * before executing each CREATE TABLE statement.
 */
class LegacySchemaSqlNormalizer
{
    /**
     * Normalize one CREATE TABLE statement without changing business columns.
     *
     * @param string $sql
     * @return string
     */
    public static function normalize($sql)
    {
        $sql = (string) $sql;

        // MySQL versions before 8.0.13 reject literal defaults on BLOB/TEXT.
        // MariaDB accepts some of them, which made the same dump behave
        // differently between development and production.
        $sql = preg_replace_callback('/^\s*`[^`]+`\s+(?:tinytext|text|mediumtext|longtext|tinyblob|blob|mediumblob|longblob)\b[^,\r\n]*,?\s*$/im', function ($match) {
            return preg_replace(
                '/\s+DEFAULT\s+(?!NULL\b)(?:\'(?:\'\'|[^\'])*\'|"(?:""|[^"])*"|[^\s,]+)/i',
                '',
                $match[0]
            );
        }, $sql);

        // NO_ZERO_DATE/strict mode rejects legacy zero dates. A missing date is
        // represented consistently as NULL on a clean installation.
        $sql = preg_replace_callback('/^\s*`[^`]+`\s+(?:date|datetime|timestamp)\b[^,\r\n]*,?\s*$/im', function ($match) {
            $line = $match[0];
            if (!preg_match('/\bDEFAULT\s+[\'\"]0000-00-00(?: 00:00:00)?[\'\"]/i', $line)) {
                return $line;
            }

            $line = preg_replace('/\bNOT\s+NULL\b/i', 'NULL', $line);
            return preg_replace('/\bDEFAULT\s+[\'\"]0000-00-00(?: 00:00:00)?[\'\"]/i', 'DEFAULT NULL', $line);
        }, $sql);

        // Parentheses are not understood by every supported server release.
        $sql = preg_replace('/\bCURRENT_TIMESTAMP\(\)/i', 'CURRENT_TIMESTAMP', $sql);

        // These are physical hints from the source server, not schema rules.
        // Keeping them can fail when Barracuda/DYNAMIC row format is unavailable.
        $sql = preg_replace('/\s+AVG_ROW_LENGTH=\d+/i', '', $sql);
        $sql = preg_replace('/\s+ROW_FORMAT=[A-Z0-9_]+/i', '', $sql);

        return $sql;
    }
}
