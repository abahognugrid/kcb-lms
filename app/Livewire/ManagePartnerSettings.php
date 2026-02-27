<?php

namespace App\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Livewire\Component;
use Rawilk\Settings\Facades\Settings;

class ManagePartnerSettings extends Component
{
    public array $settings = [
        'others' => [
            'payments' => [
                'lowOnTime' => 20,
                'lowOnTimeNotifyDay' => Carbon::WEDNESDAY,
            ],
            'loans' => [
                'firstTimeLoanPercentage' => 30,
                'firstTimeLoanNotifyDay' => Carbon::SUNDAY,
            ],
        ],
    ];

    public function mount(): void
    {
        $this->getSettings();
    }

    public function render(): View
    {
        return view('livewire.manage-partner-settings', [
            'weekdayOptions' => $this->weekdayOptions(),
        ]);
    }

    public function updatedSettingsOthersPaymentsLowOnTimeNotifyDay($value): void
    {
        if (empty($value)) {
            $value = config('lms.others.payments.lowOnTimeNotifyDay');
        }

        $this->setSettings('others.payments.lowOnTimeNotifyDay', $value);
    }

    public function updatedSettingsOthersPaymentsLowOnTime($value): void
    {
        if (empty($value)) {
            $value = config('lms.others.payments.lowOnTime');
        }

        $this->setSettings('others.payments.lowOnTime', $value);
    }

    public function updatedSettingsOthersLoansFirstTimeLoanPercentage($value): void
    {
        if (empty($value)) {
            $value = config('lms.others.loans.firstTimeLoanPercentage');
        }

        $this->setSettings('others.loans.firstTimeLoanPercentage', $value);
    }

    public function updatedSettingsOthersLoansFirstTimeLoanNotifyDay($value): void
    {
        if (empty($value)) {
            $value = config('lms.others.loans.firstTimeLoanNotifyDay');
        }

        $this->setSettings('others.loans.firstTimeLoanNotifyDay', $value);
    }

    protected function setSettings(string $name, string|int $value): void
    {
        Settings::setTeamId(session('partner_id'));
        Settings::set($name, $value);
    }

    protected function getSettings(): void
    {
        Settings::setTeamId(session('partner_id'));

        data_set(
            $this->settings,
            'others.payments.lowOnTime',
            Settings::get('others.payments.lowOnTime', config('lms.others.payments.lowOnTimeNotifyDay'))
        );
        data_set(
            $this->settings,
            'others.payments.lowOnTimeNotifyDay',
            Settings::get('others.payments.lowOnTimeNotifyDay', config('lms.others.payments.lowOnTimeNotifyDay'))
        );
        data_set(
            $this->settings,
            'others.loans.firstTimeLoanPercentage',
            Settings::get('others.loans.firstTimeLoanPercentage', config('lms.others.loans.firstTimeLoanPercentage'))
        );
        data_set(
            $this->settings,
            'others.loans.firstTimeLoanNotifyDay',
            Settings::get('others.loans.firstTimeLoanNotifyDay', config('lms.others.loans.firstTimeLoanNotifyDay'))
        );
    }

    protected function weekdayOptions(): array
    {
        return [
            Carbon::SUNDAY => 'Sunday',
            Carbon::MONDAY => 'Monday',
            Carbon::TUESDAY => 'Tuesday',
            Carbon::WEDNESDAY => 'Wednesday',
            Carbon::THURSDAY => 'Thursday',
            Carbon::FRIDAY => 'Friday',
            Carbon::SATURDAY => 'Saturday',
        ];
    }
}
