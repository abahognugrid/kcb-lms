<?php

namespace App\Services;

use App\Actions\Loans\CreateApprovedLoanAction;
use App\Models\Partner;
use App\Models\Customer;
use App\Models\Transaction;
use App\Notifications\SmsNotification;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;
use App\Jobs\TransactableGetThroughPhoneJob;
use App\Models\Accounts\Account;
use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\WrittenOffLoanRecovered;
use App\Services\Account\AccountSeederService;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class LoanService
{
    public static function initiateDisbursement(Transaction $transaction): bool
    {
        try {
            DB::beginTransaction();

            $response = self::disburse($transaction);
            $responseStatus = Arr::get($response, 'TXNSTATUS');
            $responseStatus = (int) $responseStatus;

            $updateDetails = [
                'Provider_TXN_ID' => Arr::get($response, 'TXNID'),
                'Payment_Service_Provider' => 'AIRTEL',
                'Narration' => Arr::get($response, 'MESSAGE'),
            ];

            if ($responseStatus !== 200) {
                $updateDetails['Status'] = 'Failed';
                $transaction->update($updateDetails);
                DB::commit();
                return false;
            } else {
                $updateDetails['Status'] = 'Completed';
            }
            $transaction->update($updateDetails);
            DB::commit();
            return true;
        } catch (Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return false;
        }
    }


    public static function initiateRepayment(Partner $partner, Customer $customer, float $amount, $loan): void
    {
        $txnID = Transaction::generateID();
        $providerTxnID = app()->isLocal() ? Transaction::generateID() : null;
        DB::beginTransaction();
        try {
            $transaction = Transaction::create([
                'partner_id' => $partner->id,
                'Type' => Transaction::REPAYMENT,
                'Amount' => $amount,
                'Status' => 'Completed',
                'Telephone_Number' => $customer->Telephone_Number,
                'TXN_ID' => $txnID,
                'Loan_ID' => $loan->id,
                'Loan_Application_ID' => $loan->Loan_Application_ID,
                'Provider_TXN_ID' => $providerTxnID,
            ]);

            if ($loan->isWrittenOff()) {
                self::recoverWrittenOffLoan($transaction, $loan);
            } else {
                $repayment = LoanRepayment::createPayment($loan, $transaction->Amount);
                $repayment->Transaction_ID = $transaction->id;
                $repayment->save();
                $repayment->saveJournalEntries($transaction->id);
                $repayment->updateLoanStatus();
            }
            $message = 'Thank you for paying UGX ' . number_format($transaction->Amount) . ' to ' . $transaction->partner->Institution_Name . '. Your payment has been received successfully.';
            $transaction->customer->notify(new SmsNotification($message, $transaction->Telephone_Number, $transaction->customer->id, $partner->id, $partner->smsPrice(), $partner->smsCost()));
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Failed to initiate repayment: " . $e->getMessage());
            throw $e;
        }
    }

    public static function disburse(Transaction $transaction)
    {
        $bankName = config('lms.payments.bank_name');
        $bankAccount = config('lms.payments.bank_account_no');
        $bankUsername = config('lms.payments.bank_username');
        $bankPassword = config('lms.payments.bank_password');
        $phone = $transaction->Telephone_Number;
        $phone = '256752600157'; // hard coded for test
        $phone = Str::after($phone, '256');
        $amount = $transaction->Amount;
        $transactionId = $transaction->TXN_ID;
        $xmlRequest = <<<XML
        <COMMAND>
            <TYPE>MERCHCASHIN</TYPE>
            <INTERFACEID>{$bankName}</INTERFACEID>
            <MSISDN>{$bankAccount}</MSISDN>
            <MSISDN2>{$phone}</MSISDN2>
            <AMOUNT>{$amount}</AMOUNT>
            <EXTTRID>{$transactionId}</EXTTRID>
            <REFERENCE>{$transactionId}</REFERENCE>
            <USERNAME>{$bankUsername}</USERNAME>
            <PASSWORD>{$bankPassword}</PASSWORD>
        </COMMAND>
        XML;
        Log::info('Disbursement Request', ['body' => $xmlRequest]);
        $url = config('lms.payments.bank_api_url');

        try {
            if (app()->isLocal()) {
                return [
                    'TXNSTATUS' => 200,
                    'TXNID' => random_int(100000000000, 999999999999),
                    'MESSAGE' => 'SENT.TID 131609401618. UGX 148,500 to SAM KAKOOZA  0740618886. Fee UGX 0. Bal UGX 1,066,244,314. Date 26-September-2025 17:54.',
                    'EXTTRID' => $transactionId,
                ];
            }
            $response = Http::withHeaders([
                'Content-Type' => 'application/xml',
            ])->send('POST', $url, [
                'body' => $xmlRequest,
            ]);
            $responseXml = $response->body();
            Log::info('Disbursement Response', ['body' => $responseXml]);
            $xml = simplexml_load_string($responseXml, "SimpleXMLElement", LIBXML_NOCDATA);
            $json = json_encode($xml);
            return json_decode($json, true);
        } catch (\Exception $e) {
            Log::error('Disbursement error', ['error' => $e->getMessage()]);
            return false;
        }
    }

    protected static function recoverWrittenOffLoan(Transaction $transaction, Loan $loan): bool
    {
        $transaction->loadMissing(['loan', 'customer']);
        $collectionAccount = Account::query()
            ->where('slug', AccountSeederService::COLLECTION_OVA_SLUG)
            ->where('partner_id', $transaction->partner_id)
            ->first();
        $recoveriesFromWrittenOffLoansAccount = Account::query()
            ->where('slug', AccountSeederService::RECOVERIES_FROM_WRITTEN_OFF_LOANS_SLUG)
            ->where('partner_id', $transaction->partner_id)
            ->first();

        if (empty($collectionAccount) || empty($recoveriesFromWrittenOffLoansAccount)) {
            Log::error('Missing collection or loan recoveries account');

            return false;
        }

        $loan_write_off_recovery = WrittenOffLoanRecovered::createTransactable(
            $loan,
            $transaction->Amount,
        );

        $loan_write_off_recovery->saveJournalEntries($transaction->id);

        $loan->update([
            'Written_Off_Amount_Recovered' => $loan->write_offs()->where('Is_Recovered', 1)
                ->sum('Amount_Written_Off'),
            'Last_Recovered_At' => now()
        ]);

        return true;
    }
}
