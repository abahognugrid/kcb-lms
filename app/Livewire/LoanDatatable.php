<?php

namespace App\Livewire;

use App\Enums\LoanAccountType;
use App\Models\Loan;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Exceptions\DataTableConfigurationException;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;

class LoanDatatable extends DataTableComponent
{
    public $status;

    protected $model = Loan::class;

    public function builder(): Builder
    {
        $loan_status = $this->status === 'active' ? 'Approved' : str($this->status)->title()->toString();

        return Loan::query()
            ->where('loans.Credit_Application_Status', $loan_status)
            ->with('loan_product', 'customer', 'loan_application', 'schedule')
            ->select('loans.*') // Explicitly select columns from the loans table
            ->orderBy('loans.id', 'desc'); // Specify the table for the id column
    }

    /**
     * @throws DataTableConfigurationException
     */
    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setTableRowUrl(fn($row) => route('loan-accounts.show', $row))
            ->setPerPage(50);
    }

    public function columns(): array
    {
        return [
            Column::make('Id', 'id')
                ->sortable(),
            Column::make('First Name', 'customer.First_Name')->searchable(),
            Column::make('Last Name', 'customer.Last_Name')->searchable(),
            Column::make('Phone Number', 'customer.Telephone_Number')->searchable(),
            Column::make('Account Status', 'Credit_Account_Status')
                ->format(function ($value) {
                    return LoanAccountType::formattedName($value);
                })
                ->sortable(),
            Column::make('Maturity Date', 'Maturity_Date')
                ->label(function ($record) {
                    return '<div class="text-end">' . \Carbon\Carbon::parse($record->Maturity_Date)->format('Y-m-d') . '</div>';
                })
                ->html()
                ->sortable(),
            Column::make('Amount Granted', 'Facility_Amount_Granted')
                ->label(function ($record) {
                    return '<div class="text-end">' . number_format($record->Facility_Amount_Granted, 2) . '</div>';
                })
                ->html()
                ->sortable(),
            Column::make('Interest', 'Credit_Amount')
                ->label(function ($record) {
                    return '<div class="text-end">' . number_format($record->totalInterest(), 2) . '</div>';
                })
                ->html()
                ->sortable(),
            Column::make('Created at', 'created_at')
                ->label(function ($record) {
                    return $record->created_at->diffForHumans();
                })
                ->sortable(),
            LinkColumn::make('Actions')
                ->title(fn($row) => 'View')
                ->location(fn($row) => route('loan-accounts.show', $row)),
        ];
    }
}
