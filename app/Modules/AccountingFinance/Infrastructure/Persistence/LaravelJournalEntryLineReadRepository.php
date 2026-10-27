<?php

namespace App\Modules\AccountingFinance\Infrastructure\Persistence;

use App\Modules\AccountingFinance\Application\Contracts\JournalEntryLineReadRepository;
use App\Modules\AccountingFinance\Application\Data\JournalEntryLine;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class LaravelJournalEntryLineReadRepository implements JournalEntryLineReadRepository
{
    public function summarizedForCompany(int $journalEntryId, string $companyId): Collection
    {
        return DB::table('ct_asientos_detalle as detail')
            ->join('plan_cuentas_empresa as account', 'account.id_plan', '=', 'detail.id_plan_cuenta')
            ->where('detail.id_asiento_cabecera', $journalEntryId)
            ->where('account.id_empresa', $companyId)
            ->select([
                'account.id_plan as account_plan_id',
                'account.nombre as account_name',
            ])
            ->selectRaw('MAX(detail.id) as source_line_id')
            ->selectRaw('MAX(detail.id_asiento_cabecera) as journal_entry_id')
            ->selectRaw('MAX(detail.descripcion) as description')
            ->selectRaw('SUM(detail.debe) as debit')
            ->selectRaw('SUM(detail.haber) as credit')
            ->groupBy('account.id_plan', 'account.nombre')
            ->orderBy('account.id_plan')
            ->get()
            ->map(fn (object $record): JournalEntryLine => new JournalEntryLine(
                sourceLineId: (int) $record->source_line_id,
                journalEntryId: (int) $record->journal_entry_id,
                accountPlanId: (string) $record->account_plan_id,
                accountName: (string) $record->account_name,
                description: (string) $record->description,
                debit: (float) $record->debit,
                credit: (float) $record->credit,
            ));
    }
}
