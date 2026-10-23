<?php

return [
    'slow_request_ms' => (int) env('V3_MONITOR_SLOW_MS', 2000),
    'slow_query_ms' => (int) env('V3_MONITOR_SLOW_QUERY_MS', 1000),
    'audit_window_days' => (int) env('V3_MONITOR_AUDIT_DAYS', 7),
    'minimum_free_disk_percent' => (float) env('V3_MONITOR_MIN_DISK_PERCENT', 15),
    'maximum_log_size_mb' => (float) env('V3_MONITOR_MAX_LOG_MB', 100),
    'log_tail_bytes' => (int) env('V3_MONITOR_LOG_TAIL_BYTES', 1048576),
    'backup_marker_path' => env('V3_MONITOR_BACKUP_MARKER', ''),
    'maximum_backup_age_hours' => (int) env('V3_MONITOR_MAX_BACKUP_HOURS', 24),
];
