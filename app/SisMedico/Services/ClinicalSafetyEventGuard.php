<?php

namespace Sis_medico\Services;

class ClinicalSafetyEventGuard
{
    private static $mutationDepth = 0;

    public static function run(callable $callback)
    {
        self::$mutationDepth++;
        try { return $callback(); }
        finally { self::$mutationDepth--; }
    }

    public static function assertMutation($event)
    {
        if (self::$mutationDepth > 0) return;
        $protected = array('status','assigned_to','notification_required','notified_at','notified_by',
            'notification_recipient','review_due_at','root_cause','corrective_actions','closed_at','closed_by');
        foreach ($protected as $field) {
            if ($event->isDirty($field)) throw new \LogicException('Los cambios de seguridad clínica deben usar ClinicalSafetyEventService.');
        }
    }
}
