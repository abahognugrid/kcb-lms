<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\Transaction;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class TransactionsDatatable extends DataTableComponent
{
    protected $model = Transaction::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setTableWrapperAttributes([
            'class' => 'max-h-56 md:max-h-72 lg:max-h-96 overflow-y-scroll',
        ]);
    }

    public function builder(): Builder
    {
        return Transaction::with("partner")
            ->select();
    }


    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable(),
            Column::make("Type", "Type")
                ->sortable(),
            Column::make("Status", "Status")
                ->label(function ($record) {
                    if ($record->Status == "Completed") {
                        return '<span class="badge bg-label-success">Completed</span>';
                    }
                    if ($record->Status == "Failed") {
                        return '<span class="badge bg-label-danger">Failed</span>';
                    }
                    return $record->Status;
                })
                ->html()
                ->sortable(),
            Column::make("Amount", "Amount")
                ->label(function ($record) {
                    return "UGX " . number_format($record->Amount, 2);
                })
                ->sortable(),
            Column::make("Telephone", "Telephone_Number")
                ->sortable(),
            Column::make("TXN ID", "TXN_ID"),
            Column::make("Provider TXN ID", "Provider_TXN_ID"),
            Column::make("Updated", "updated_at")
                ->label(function ($record) {
                    return Carbon::parse($record->updated_at)->toFormattedDateString();
                })
                ->sortable(),
        ];
    }
}
