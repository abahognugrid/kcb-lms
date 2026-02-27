<?php

namespace App\Livewire\Loans;

use App\Models\LoanApplication;
use Illuminate\Contracts\View\View;
use Livewire\Component;
use Livewire\WithPagination;

class LoanApplications extends Component
{
    use WithPagination;

    public string $searchTerm = '';

    protected $queryString = ['searchTerm']; // This maintains search in URL

    public function updatingSearchTerm(): void
    {
        $this->resetPage(); // Reset pagination when search term changes
    }

    public function render(): View
    {
        return view('livewire.loan-applications.index', $this->getViewData());
    }

    protected function getRecords()
    {
        $query = LoanApplication::query()
            ->with(['customer'])
            ->has('transactions');

        if ($this->searchTerm) {
            $searchTerm = '%' . $this->searchTerm . '%';

            $query->where(function ($q) use ($searchTerm) {
                $q->where('id', 'LIKE', str($searchTerm)->afterLast('0')->prepend('%')->toString())->orWhereRelation('customer', 'Telephone_Number', 'LIKE', $searchTerm)
                    ->orWhereRelation('customer', 'First_Name', 'LIKE', $searchTerm)
                    ->orWhereRelation('customer', 'Last_Name', 'LIKE', $searchTerm)
                    ->orWhereHas('customer', function ($q) use ($searchTerm) {
                        $q->whereRaw("CONCAT(First_Name, ' ', Last_Name) LIKE ?", [$searchTerm]);
                    });
            });
        }

        return $query->latest()->paginate(100);
    }

    protected function getViewData(): array
    {
        return [
            'records' => $this->getRecords(),
        ];
    }
}
