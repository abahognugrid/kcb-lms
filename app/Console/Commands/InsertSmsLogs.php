<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Row;

class InsertSmsLogs extends Command
{
    // Command signature
    protected $signature = 'lms:insert-sms-logs';

    // Command description
    protected $description = 'Insert Nzuri Mobile Loan SMS logs';

    public function handle()
    {
        $filePath = base_path('nzuri_50k_new.xlsx');

        // Process in chunks of 1000 rows
        Excel::import(new PhoneNumbersImport, $filePath);

        $this->info("SMS logs inserted successfully!");
    }
}

class PhoneNumbersImport implements OnEachRow, WithChunkReading
{
    public function onRow(Row $row)
    {
        $row = $row->toArray();

        if (!empty($row[0])) {
            DB::table('sms_logs')->insert([
                'partner_id'       => 9,
                'Telephone_Number' => trim($row[0]),
                'Message'          => 'Need quick cash? You could qualify for a Nzuri Trust Express Loan! Dial 185*8*9# on Airtel to check eligibility and access cash instantly.',
                'Category'         => 'Express Loan',
                'created_at'       => Carbon::now(),
                'updated_at'       => Carbon::now(),
            ]);
        }
    }

    public function chunkSize(): int
    {
        return 1000; // process 1000 rows at a time
    }
}
