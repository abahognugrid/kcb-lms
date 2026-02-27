<?php

namespace App\Console\Commands;

use App\Models\Partner;
use Exception;
use Illuminate\Console\Command;

class ExportPartnerSettings extends Command
{
    protected $signature = 'lms:export {partnerCode} {--file=partner_settings.json}';
    protected $description = 'Export partner settings to a JSON file';

    public function handle()
    {
        try {
            $partnerCode = $this->argument('partnerCode');
            $filename = $this->option('file');

            if (!$partnerCode) {
                throw new Exception('Please provide the partner code');
            }

            $partner = Partner::where('Identification_Code', $partnerCode)->with(
                'loan_products.loan_product_terms',
                'loan_products.fees',
                'loan_products.penalties',
                'loan_products.sms_templates',
                'api_setting',
                'ovas',
            )->first();

            if (!$partner) {
                throw new Exception('Partner with the provided partner code could not be found');
            }

            // Convert to array and remove IDs and timestamps
            $data = $partner->toArray();
            $this->removeIdsAndTimestamps($data);

            // Save to file
            file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT));

            $this->info("Partner settings exported successfully to {$filename}");

            return 0;
        } catch (Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }
    }

    protected function removeIdsAndTimestamps(&$data)
    {
        unset($data['id'], $data['created_at'], $data['updated_at'], $data['deleted_at']);

        foreach ($data['loan_products'] as &$product) {
            unset($product['id'], $product['partner_id'], $product['created_at'], $product['updated_at'], $product['deleted_at']);

            foreach ($product['loan_product_terms'] as &$term) {
                unset($term['id'], $term['created_at'], $term['updated_at'], $term['deleted_at']);
            }

            foreach ($product['fees'] as &$fee) {
                unset($fee['id'], $fee['created_at'], $fee['updated_at'], $fee['deleted_at']);
            }

            foreach ($product['penalties'] as &$penalty) {
                unset($penalty['id'], $penalty['created_at'], $penalty['updated_at'], $penalty['deleted_at']);
            }
        }

        if (isset($data['api_setting'])) {
            unset($data['api_setting']['id'], $data['api_setting']['created_at'], $data['api_setting']['updated_at']);
        }

        if (isset($data['ovas'])) {
            unset($data['ovas']['id'], $data['ovas']['created_at'], $data['ovas']['updated_at']);
        }
    }
}
