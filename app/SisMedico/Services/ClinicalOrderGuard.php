<?php

namespace Sis_medico\Services;

use Closure;
use Sis_medico\Exceptions\ClinicalOrderTransitionException;

class ClinicalOrderGuard
{
    private static $depth = 0;

    public function run(Closure $operation)
    {
        self::$depth++;
        try { return $operation(); }
        finally { self::$depth--; }
    }

    public static function assertOrderMutation($order)
    {
        if (self::$depth > 0 || !$order->exists) return;
        if ($order->isDirty('status') || $order->isDirty('completed_at') || $order->isDirty('cancel_reason')) {
            throw new ClinicalOrderTransitionException('La orden solo puede cambiar mediante ClinicalOrderService.');
        }
    }
}
