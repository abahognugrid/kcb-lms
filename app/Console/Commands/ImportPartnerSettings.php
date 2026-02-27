<?php

namespace App\Console\Commands;

use App\Models\Partner;
use App\Models\Switches;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class ImportPartnerSettings extends Command
{
    protected $signature = 'lms:import {partnerCode} {--file=partner_settings.json}';
    protected $description = 'Import partner settings from a JSON file';

    public function handle()
    {
        try {
            $partnerCode = $this->argument('partnerCode');
            $filename = $this->option('file');

            if (!file_exists($filename)) {
                throw new Exception("File {$filename} not found");
            }

            $data = json_decode(file_get_contents($filename), true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception("Invalid JSON in file: " . json_last_error_msg());
            }

            DB::transaction(function () use ($partnerCode, $data) {
                // Create the partner
                $partner = Partner::where(['Identification_Code' => $partnerCode])->first();

                $paymentSwitch = Switches::where('Category', 'Payment')->first();

                // Create loan products
                foreach ($data['loan_products'] as $productData) {
                    // Replace Switch_ID with the new switch ID for Payment category
                    if (isset($productData['Switch_ID']) && $paymentSwitch) {
                        $productData['Switch_ID'] = $paymentSwitch->id;
                    }
                    $product = $partner->loan_products()->create(
                        array_merge($productData, ['partner_id' => $partner->id])
                    );
                    // Create loan product terms
                    if (isset($productData['loan_product_terms'])) {
                        foreach ($productData['loan_product_terms'] as $termData) {
                            $product->loan_product_terms()->create(
                                array_merge(
                                    $termData,
                                    ['partner_id' => $partner->id]
                                )
                            );
                        }
                    }

                    if (isset($productData['sms_templates'])) {
                        foreach ($productData['sms_templates'] as $templates) {
                            $product->sms_templates()->create(array_merge(
                                $templates,
                                ['partner_id' => $partner->id]
                            ));
                        }
                    }

                    // Create fees
                    if (isset($productData['fees'])) {
                        foreach ($productData['fees'] as $feeData) {
                            $product->fees()->create(array_merge(
                                $feeData,
                                ['partner_id' => $partner->id]
                            ));
                        }
                    }

                    // Create penalties
                    if (isset($productData['penalties'])) {
                        foreach ($productData['penalties'] as $penaltyData) {
                            $product->penalties()->create(array_merge(
                                $penaltyData,
                                ['partner_id' => $partner->id]
                            ));
                        }
                    }
                }

                // Create API setting
                if (isset($data['api_setting'])) {
                    $partner->api_setting()->create(array_merge(
                        $data['api_setting'],
                        ['partner_id' => $partner->id]
                    ));
                }

                // Create OVAS
                if (isset($data['ovas'])) {
                    $partner->ovas()->create(array_merge(
                        $data['ovas'],
                        ['partner_id' => $partner->id]
                    ));
                }
            });

            $this->info("Partner settings imported successfully for partner code: {$partnerCode}");

            return 0;
        } catch (Exception $e) {
            $this->error($e->getMessage());
            return 1;
        }
    }
}
