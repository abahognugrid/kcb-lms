<?php

namespace App\Livewire\Dashboard;

use App\Traits\ExportsData;
use Asantibanez\LivewireCharts\Models\PieChartModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CustomerRegistrationGenderDistributionChart extends Component
{
    use ExportsData;

    public function mount(): void
    {
        $this->endDate = now()->toDateString();
    }

    public function render()
    {
        $partner_id = Auth::user()->partner_id;

        $bindings = [];
        $customer_registration_where_condition = 'l.id = (select id from loans where customer_id = c.id';

        if (!is_null($partner_id)) {
            $customer_registration_where_condition .= ' and partner_id = ?';
            $bindings[] = $partner_id;
        }

        $customer_registration_where_condition .= ' order by id asc limit 1)';

        $customers_count_per_gender = DB::table('customers as c')
            ->selectRaw('c.gender as gender, count(*) as customers_count')
            ->join('loans as l', 'l.customer_id', '=', 'c.id')
            ->where('l.credit_account_date', '<=', $this->endDate)
            ->whereRaw($customer_registration_where_condition, $bindings)
            ->when($partner_id, function ($query, $partner_id) {
                $query->where('l.partner_id', $partner_id);
            })
            ->groupBy('c.Gender')
            ->get();

        $chart_model = (new PieChartModel())
            ->setTitle('Customer registrations classified by gender')
            ->setJsonConfig([
                'chart.height' => 400
            ]);
        foreach ($customers_count_per_gender as $result) {
            $chart_model->addSlice($result->gender, $result->customers_count, $this->getRandomColor());
        }

        return view('livewire.dashboard.customer-registration-gender-distribution-chart', compact('chart_model'));
    }

    protected function getRandomColor()
    {
        return '#' . str_pad(dechex(mt_rand(0, 0xFFFFFF)), 6, '0', STR_PAD_LEFT);
    }
}
