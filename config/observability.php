<?php

return [
    'log_directory' => env('OBSERVABILITY_LOG_DIRECTORY', storage_path('logs')),
    'log_max_scan_bytes' => (int) env('OBSERVABILITY_LOG_MAX_SCAN_BYTES', 16 * 1024 * 1024),
    'log_max_entries' => (int) env('OBSERVABILITY_LOG_MAX_ENTRIES', 10000),
    'log_activity_families' => (int) env('OBSERVABILITY_LOG_ACTIVITY_FAMILIES', 18),
    'log_flame_max_depth' => (int) env('OBSERVABILITY_LOG_FLAME_MAX_DEPTH', 32),
    'log_flame_max_stacks' => (int) env('OBSERVABILITY_LOG_FLAME_MAX_STACKS', 240),
    'per_page' => (int) env('OBSERVABILITY_PER_PAGE', 25),
];
