<?php

namespace App\Actions\Reports;

use App\Enums\LoanApplicationStatus;
use App\Enums\MaritalStatus;
use App\Models\LoanApplication;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use League\Csv\CannotInsertRecord;
use League\Csv\Exception;
use League\Csv\InvalidArgument;
use League\Csv\Writer;

class ExportCapReportAction
{
    /**
     *
     * @throws InvalidArgument
     */
    public function execute(): string
    {
        $path = 'partners/' . auth()->user()->partner->Identification_Code . '/' . $this->getCAPFilename();
        $csvFile = Writer::createFromString()->setDelimiter('|')->setEnclosure('"');

        $filters = [
            'startDate' => now()->subMonth()->startOfMonth()->format('Y-m-d'),
            'endDate' => now()->subMonth()->endOfMonth()->format('Y-m-d'),
        ];
        $applications = app(GetLoanApplicationReportDetailsAction::class)->filters($filters)->execute();

        $headerRecord = $this->getHeaderRecord();

        try {
            $csvFile->insertOne($headerRecord);

            $applications->each(function (LoanApplication $application) use ($csvFile) {
                $csvFile->insertOne($this->getCapRecord($application));
            });

            Storage::disk('local')->put($path, $csvFile->toString());

            return $path;
        } catch (CannotInsertRecord|Exception $e) {
            Log::debug($e->getMessage());

            return '';
        }
    }

    protected function getCAPFilename(): string
    {
        return auth()->user()->partner->Identification_Code .
            now()->subMonth()->endOfMonth()->format('Ymd') .
            'CAP.CSV';
    }

    protected function getCapRecord(LoanApplication $application): array
    {
        return [
            "D",
            auth()->user()->partner->Identification_Code,
            "001",
            $application->Customer_ID,
            $application->application_number,
            "0",
            $application->Credit_Application_Date->format('Ymd'),
            $application->Amount,
            $application->Currency,
            $application->Credit_Account_or_Loan_Product_Type,
            LoanApplicationStatus::getValueFromName($application->Credit_Application_Status),
            $application->Last_Status_Change_Date->format('Ymd'),
            $application->Credit_Application_Duration,
            $application->Rejection_Reason,
            'Y',
            $application->Group_Identification_Joint_Account_Identification,
            $application->Credit_Application_Status == 'Approved' ? $application->Credit_Amount_Approved : null,
            $application->Credit_Application_Status == 'Approved' ? $application->Currency_Approved : null,
            null, // $application->ii_registration_certificate_number,
            null, // $application->ii_tax_identification_number,
            null, // $application->ii_fcs_number,
            null, // $application->ii_passport_number,
            null, //$application->ii_drivers_licence_id_number,
            null, //$application->ii_drivers_license_permit_number,
            $application->customer->ID_Number,
            'UG', // $application->ii_country_issuing_authority,
            'UG', // $application->ii_nationality,
            'UG', //$application->ii_country_of_issue,
            null, // $application->ii_refugee_number,
            null, // $application->ii_work_permit_number,
            null, // $application->gscafb_business_name,
            null, // $application->gscafb_trading_name,
            null, // $application->gscafb_activity_description,
            null, // $application->gscafb_industry_sector_code,
            null, // $application->gscafb_date_registered,
            null, // $application->gscafb_business_type_code,
            $application->customer->Last_Name, //$application->gscafb_surname,
            $application->customer->First_Name, //$application->gscafb_forename1,
            $application->customer->Other_Name, //$application->gscafb_forename2,
            null,//$application->gscafb_forename3,
            strtolower($application->customer->Gender) === 'male' ? 0 : 1, //$application->gscafb_gender,
            MaritalStatus::getValueFromName($application->customer->Marital_Status), //$application->gscafb_marital_status,
            $application->customer->Date_of_Birth->format('Ymd'), //$application->gscafb_date_of_birth,
            3, // $application->ei_employment_type,
            null, // $application->ei_primary_occupation,
            null, // $application->ei_employer_name,
            null, // $application->ei_employee_number,
            null, // $application->ei_employment_date,
            null, // $application->ei_income_band,
            null, // $application->ei_salary_frequency,
            null, // $application->pci_unit_number,
            null, // $application->pci_building_name,
            null, // $application->pci_floor_number,
            null, // $application->pci_plot_or_street_number,
            null, // $application->pci_lc_or_street_name,
            'KAMPALA', // $application->pci_parish,
            'KAMPALA', // $application->pci_suburb,
            'KAMPALA', // $application->pci_village,
            'KAMPALA', // $application->pci_county_or_town,
            'KAMPALA', // $application->pci_district,
            '0', // $application->pci_region,
            null, // $application->pci_po_box_number,
            null, // $application->pci_post_office_town,
            'UG', // $application->pci_country_code,
            '1', // $application->pci_period_at_address,
            'T', // $application->pci_flag_of_ownership,
            '256', // $application->pci_primary_number_country_dialling_code,
            str($application->customer->Telephone_Number)->after('256')->toString(), // $application->pci_primary_number_telephone_number,
            null, // $application->pci_other_number_country_dialling_code,
            null, // $application->pci_other_number_telephone_number,
            null, // $application->pci_mobile_number_country_dialling_code,
            null, // $application->pci_mobile_number_telephone_number,
            null, // $application->pci_facsimile_country_dialling_code,
            null, // $application->pci_facsimile_number,
            null, // $application->pci_email_address,
            null, // $application->pci_web_site,
            null, // $application->pci_latitude,
            null, // $application->pci_longitude,
            null, // $application->sci_unit_number,
            null, // $application->sci_unit_name,
            null, // $application->sci_floor_number,
            null, // $application->sci_plot_or_street_number,
            null, // $application->sci_lc_or_street_name,
            null, // $application->sci_parish,
            null, // $application->sci_suburb,
            null, // $application->sci_village,
            null, // $application->sci_county_or_town,
            null, // $application->sci_district,
            null, // $application->sci_region,
            null, // $application->sci_po_box_number,
            null, // $application->sci_post_office_town,
            null, // $application->sci_country_code,
            null, // $application->sci_period_at_address,
            null, // $application->sci_flag_for_ownership,
            '256', // $application->sci_primary_number_country_dialling_code,
            str($application->customer->Telephone_Number)->after('256')->toString(), // $application->sci_primary_number_telephone_number,
            null, // $application->sci_other_number_country_dialling_code,
            null, // $application->sci_other_number_telephone_number,
            null, // $application->sci_mobile_number_country_dialling_code,
            null, // $application->sci_mobile_number_telephone_number,
            null, // $application->sci_facsimile_country_dialling_code,
            null, // $application->sci_facsimile_number,
            null, // $application->sci_email_address,
            null, // $application->sci_web_site,
            null, // $application->sci_latitude,
            null, // $application->sci_longitude,
        ];
    }

    /**
     * @return array
     */
    public function getHeaderRecord(): array
    {
        return [
            "H",
            auth()->user()->partner->Identification_Code,
            auth()->user()->partner->Institution_Name,
            now()->subMonth()->endOfMonth()->format('Ymd'),
            "8.0",
            now()->format('Ymd'),
            "CAP"
        ];
    }
}
