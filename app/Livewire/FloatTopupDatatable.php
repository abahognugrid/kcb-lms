<?php

namespace App\Livewire;

use App\Models\FloatTopUp;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\DataTableComponent;

class FloatTopupDatatable extends DataTableComponent
{
    // class name should be similar to file name
    protected $model = FloatTopUp::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function builder(): Builder
    {
        return FloatTopUp::with('partner')->select([
            'id',
            'partner_id',
            'Amount',
            'Proof_Of_Payment',
            'Status',
        ]);
    }

    // public function columns(): array
    // {
    //     return [
    //         Column::make("Amount", "Amount")
    //             ->label(function ($record) {
    //                 return 'UGX ' . number_format($record->Amount, 2);
    //             })
    //             ->sortable(),
    //         Column::make("Proof Of Payment", "Proof_Of_Payment"),
    //         Column::make("Status", "Status")
    //             ->sortable(),
    //         Column::make("Created at", "created_at")
    //             ->sortable(),
    //         Column::make("Updated at", "updated_at")
    //             ->sortable(),
    //     ];
    // }

    public function columns(): array
    {
        return [
            Column::make("Amount", "Amount")
                ->label(function ($record) {
                    return 'UGX ' . number_format($record->Amount, 2);
                })
                ->sortable(),

            // Make the Proof Of Payment column clickable
            Column::make("Proof Of Payment", "Proof_Of_Payment")
                ->label(function ($record) {
                    if ($record->Proof_Of_Payment) {
                        return '<a href="' . asset($record->Proof_Of_Payment) . '" target="_blank">View Proof</a>';
                    } else {
                        return 'No Proof';
                    }
                })
                ->html(), // Ensures the HTML for the link is rendered

            Column::make("Status", "Status")
                ->label(function ($record) {
                    if ($record->Status == "Approved") {
                        return '<span class="badge bg-label-success">Approved</span>';
                    }
                    return $record->Status;
                })
                ->html()
                ->sortable(),

            Column::make("Created at", "created_at")
                ->sortable(),

            Column::make("Updated at", "updated_at")
                ->sortable(),
        ];
    }
}
