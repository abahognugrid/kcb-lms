<?php

namespace App\Services\Account;

use App\Models\LoanProduct;
use App\Models\Accounts\Account;
use App\Exceptions\AccountException;
use App\Models\SavingsProduct;
use Illuminate\Database\Eloquent\Model;

class AccountSeederService extends SharedSeederServiceHelper
{
    // Parent Accounts
    const ASSETS_NAME = 'Assets';
    const ASSETS_SLUG = 'assets';
    const ASSETS_IDENTIFIER = 'A';

    const CAPITAL_NAME = 'Capital';
    const CAPITAL_SLUG = 'capital';
    const CAPITAL_IDENTIFIER = 'C';

    const EXPENSES_NAME = 'Expenses';
    const EXPENSES_SLUG = 'expenses';
    const EXPENSES_IDENTIFIER = 'E';

    const INCOME_NAME = 'Income';
    const INCOME_SLUG = 'income';
    const INCOME_IDENTIFIER = 'I';

    const LIABILITIES_NAME = 'Liabilities';
    const LIABILITIES_SLUG = 'liabilities';
    const LIABILITIES_IDENTIFIER = 'L';

    // Child Accounts
    const OPERATIONAL_EXPENSES = 'Operational Expenses';
    const OPERATIONAL_EXPENSES_SLUG = 'operational-expenses';

    const INTEREST_INCOME_FROM_LOANS_NAME = 'Interest Income from Loans';
    const INTEREST_INCOME_FROM_LOANS_SLUG = 'interest-income-from-loans';

    const INCOME_FROM_FINES_NAME = 'Fees and Fines';
    const INCOME_FROM_FINES_SLUG = 'fees-and-fines';
    const INCOME_FROM_FINES_IDENTIFIER = 'IF';

    const PENALTIES_FROM_LOAN_PAYMENTS_NAME = 'Penalties From Loan Payments';
    const PENALTIES_FROM_LOAN_PAYMENTS_SLUG = 'penalties-from-loan-payments';

    const SAVINGS_PRODUCTS_NAME = 'Savings Products';
    const SAVINGS_PRODUCTS_FIXED_SLUG = 'savings-products';

    const CASH_AT_BANK_NAME = 'Cash at Bank';
    const CASH_AT_BANK_FIXED_SLUG = 'cash-at-bank';

    const CASH_AT_MNO_NAME = 'Cash at MNO/Bank';
    const CASH_AT_MNO_FIXED_SLUG = 'cash-at-mno/bank';

    const LOAN_PRODUCTS_NAME = 'Loan Products';
    const LOAN_PRODUCTS_FIXED_SLUG = 'loan-products';

    const RECEIVABLES_NAME = 'Receivables';
    const RECEIVABLES_FIXED_SLUG = 'receivables';

    const INTEREST_RECEIVABLES_NAME = 'Interest Receivables';
    const INTEREST_RECEIVABLES_SLUG = 'interest-receivables';

    const PENALTIES_RECEIVABLES_NAME = 'Penalties Receivables';
    const PENALTIES_RECEIVABLES_SLUG = 'penalties-receivables';

    const MOBILE_MONEY_COLLECTIONS_NAME = 'Mobile Money Collections';
    const MOBILE_MONEY_COLLECTIONS_SLUG = 'mobile-money-collections';

    const FLOAT_ACCOUNT_FIXED_NAME = 'Float Account';
    const FLOAT_ACCOUNT_SLUG = 'float-account';

    const COLLECTION_OVA_NAME = 'Collection Account';
    const COLLECTION_OVA_SLUG = 'collection-account';

    const DISBURSEMENT_OVA_NAME = 'Disbursement Account';
    const DISBURSEMENT_OVA_SLUG = 'disbursement-account';

    const LOAN_OVA_ESCROW_BANK_ACCOUNT_NAME = 'Loan Account Escrow Bank Account';
    const LOAN_OVA_ESCROW_BANK_ACCOUNT_SLUG = 'loan-account-escrow-bank-account';

    const GNUGRID_FEES_ACCOUNT = 'Gnugrid Fees Account';
    const GNUGRID_FEES_ACCOUNT_SLUG = 'gnugrid-fees-account';

    const GNUGRID_COMMISSION_ACCOUNT = 'Gnugrid Commission Account';
    const GNUGRID_COMMISSION_ACCOUNT_SLUG = 'gnugrid-commission-account';

    const PROVISION_FOR_BAD_DEBT_NAME = 'Provision For Bad Debts';
    const PROVISION_FOR_BAD_DEBT_SLUG = 'provision-for-bad-debts';

    const LOAN_OVER_PAYMENTS = 'Loan Over Payments';
    const LOAN_OVER_PAYMENTS_SLUG = 'loan-over-payments';

    const OTHER_EXPENSES = 'Other Expenses';
    const OTHER_EXPENSES_SLUG = 'other-expenses';

    const PAYABLES_SLUG = "payables";
    const PAYABLES_NAME = "Payables";
    const PAYABLES_IDENTIFIER = "LPY";

    const LOAN_LOSS_PROVISION_SLUG = "loan-loss-provision";
    const LOAN_LOSS_PROVISION_NAME = "Loan Loss Provision";
    const LOAN_LOSS_PROVISION_IDENTIFIER = "LLP";

    const OTHER_INCOME_NAME = 'Other Income';
    const OTHER_INCOME_SLUG = 'other-income';

    const RECOVERIES_FROM_WRITTEN_OFF_LOANS_NAME = 'Recoveries From Written Off Loans';
    const RECOVERIES_FROM_WRITTEN_OFF_LOANS_SLUG = 'recoveries-from-written-off-loans';
    const RECOVERIES_FROM_WRITTEN_OFF_LOANS_IDENTIFIER = 'IRWL';

    const OTHER_EXPENSES_NAME = 'Other Expenses';

    const VSLA_TOOLS_EXPENSE_NAME = 'VSLA tools';
    const VSLA_TOOLS_EXPENSE_SLUG = 'vsla-tools';


    /**
     * These are special accounts assumed to be in the system
     * for every group. Each can be specified using using the
     * const strings above, and each can be added to using one of the
     * add[AccountType]Account() functions below
     */
    const FIXED_ACCOUNTS = [
        self::CASH_AT_BANK_FIXED_SLUG => [
            'name' => self::CASH_AT_BANK_NAME,
            'identifier' => 'CCAB',
            'class' => null,
            'parent-slug' => self::CAPITAL_SLUG,
        ],
        self::CASH_AT_MNO_FIXED_SLUG => [
            'name' => self::CASH_AT_MNO_NAME,
            'identifier' => 'ACAM',
            'class' => null,
            'parent-slug' => self::ASSETS_SLUG,
        ],
        // self::SAVINGS_PRODUCTS_FIXED_SLUG => [
        //     'name' => self::SAVINGS_PRODUCTS_NAME,
        //     'identifier' => 'LSP',
        //     'class' => SavingsProduct::class,
        //     'parent-slug' => self::LIABILITIES_SLUG,
        // ],
        self::PAYABLES_SLUG => [
            'name' => self::PAYABLES_NAME,
            'identifier' => self::PAYABLES_IDENTIFIER,
            'class' => null,
            'parent-slug' => self::LIABILITIES_SLUG,
        ],

        self::LOAN_LOSS_PROVISION_SLUG => [
            'name' => self::LOAN_LOSS_PROVISION_NAME,
            'identifier' => self::LOAN_LOSS_PROVISION_IDENTIFIER,
            'class' => null,
            'parent-slug' => self::LIABILITIES_SLUG,
        ],

        self::LOAN_OVER_PAYMENTS_SLUG => [
            'name' => self::LOAN_OVER_PAYMENTS,
            'identifier' => 'ILOP',
            'class' => null,
            'parent-slug' => self::LIABILITIES_SLUG,
        ],

        // self::GNUGRID_COMMISSION_ACCOUNT_SLUG => [
        //     'name' => self::GNUGRID_COMMISSION_ACCOUNT,
        //     'identifier' => 'LPY.1001',
        //     'class' => null,
        //     'parent-slug' => self::PAYABLES_SLUG,
        // ],

        self::LOAN_PRODUCTS_FIXED_SLUG => [
            'name' => self::LOAN_PRODUCTS_NAME,
            'identifier' => 'ALP',
            'class' => LoanProduct::class,
            'parent-slug' => self::ASSETS_SLUG,
        ],

        self::RECEIVABLES_FIXED_SLUG => [
            'name' => self::RECEIVABLES_NAME,
            'parent-slug' => self::ASSETS_SLUG,
            'identifier' => 'AR',
            'class' => null,
        ],

        // self::FLOAT_ACCOUNT_SLUG => [
        //     'name' => self::FLOAT_ACCOUNT_FIXED_NAME,
        //     'identifier' => 'AFA',
        //     'class' => null,
        //     'parent-slug' => self::ASSETS_SLUG,
        // ],

        self::PENALTIES_FROM_LOAN_PAYMENTS_NAME => [
            'name' => self::PENALTIES_FROM_LOAN_PAYMENTS_NAME,
            'identifier' => 'IP',
            'class' => null,
            'parent-slug' => self::INCOME_SLUG,
        ],

        self::INCOME_FROM_FINES_NAME => [
            'name' => self::INCOME_FROM_FINES_NAME,
            'identifier' => 'IF',
            'class' => null,
            'parent-slug' => self::INCOME_SLUG,
        ],
        self::INTEREST_INCOME_FROM_LOANS_NAME => [
            'name' => self::INTEREST_INCOME_FROM_LOANS_NAME,
            'identifier' => 'IIIFL',
            'class' => null,
            'parent-slug' => self::INCOME_SLUG,
        ],
        self::OTHER_INCOME_SLUG => [
            'name' => self::OTHER_INCOME_NAME,
            'identifier' => 'IOI',
            'class' => null,
            'parent-slug' => self::INCOME_SLUG,
        ],
        self::RECOVERIES_FROM_WRITTEN_OFF_LOANS_SLUG => [
            'name' => self::RECOVERIES_FROM_WRITTEN_OFF_LOANS_NAME,
            'identifier' => 'IRWL',
            'class' => null,
            'parent-slug' => self::INCOME_SLUG,
        ],
        self::PROVISION_FOR_BAD_DEBT_NAME => [
            'name' => self::PROVISION_FOR_BAD_DEBT_NAME,
            'identifier' => 'EBD',
            'class' => null,
            'parent-slug' => self::EXPENSES_SLUG,
        ],
        self::OTHER_EXPENSES_SLUG => [
            'name' => self::OTHER_EXPENSES,
            'identifier' => 'EOE',
            'class' => null,
            'parent-slug' => self::EXPENSES_SLUG,
        ],
    ];

    /**
     * @param Model $accountable the Model to add to the Account
     */
    public static function addToFixedAccount($accountable)
    {
        self::checkGroupId($accountable);

        $fixed_account = Account::where([
            'partner_id' => $accountable->partner_id,
            'slug' => $accountable->fixedParentSlug(),
        ])->first();

        if (!$fixed_account) {
            $fixed_account = Account::where([
                'partner_id' => $accountable->partner_id,
                'slug' => $accountable->fixedParentSlug(),
            ])->first();
        }

        self::checkHasParent($fixed_account, $accountable);

        $fixed_account->addChild(
            self::createManagedAccount($accountable)
        );
    }

    /**
     * Check that the partner_id of this $accountable object is set.
     * Throw error if not set.
     *
     * @param Model $accountable - the accountable object to check
     *
     * @throws AccountException
     */
    private static function checkGroupId($accountable)
    {
        if (!isset($accountable->partner_id) && !isset($accountable->partner_id)) {
            throw new AccountException(
                'No partner id listed for \'' . $accountable->fixedParentSlug() . '\'. Couldn\'t add \'' .
                    $accountable->accountDisplayName() . '\' to the Chart of Accounts'
            );
        }
    }

    /**
     * Check that the parent account has been found before trying to add to it.
     *
     * @param Account $parent - the parent to check
     * @param Model $accountable - the accountable object to check
     *
     * @throws AccountException
     */
    private static function checkHasParent($parent, $accountable)
    {
        if (!$parent) {
            throw new AccountException(
                'Fixed account \'' . $accountable->fixedParentSlug() . '\' does not exist; couldn\'t add \'' .
                    $accountable->accountDisplayName() . '\''
            );
        }
    }

    /**
     * Create a Managed Account to include under some fixed account.
     *  Throw an error if the managed account is invalid.
     *
     * @param Model $accountable - the accountable object to create
     *
     * @return Account - the managed account
     * @throws AccountException
     */
    private static function createManagedAccount($accountable)
    {
        $managed_account = new Account;
        $managed_account->name = substr($accountable->accountDisplayName(), 0, 100);
        $managed_account->accountable_type = get_class($accountable);
        $managed_account->accountable_id = $accountable->id;
        $managed_account->is_managed = true;
        $managed_account->type_letter = $accountable->getTypeLetter();
        $managed_account->identifier = $accountable->getIndentifier();
        $managed_account->getSlug();

        if ($accountable->partner_id) {
            $managed_account->partner_id = $accountable->partner_id;
        }

        if ($accountable->partner_id) {
            $managed_account->partner_id = $accountable->partner_id;
        }

        $managed_account->save();
        return $managed_account;
    }

    /**
     * Seed the default accounts for a given group
     */
    public function seedDefaultAccounts()
    {
        $this->addAssetAccounts();
        $this->addLiabilitiesAccounts();
        $this->addCapitalAccounts();
        $this->addIncomeAccounts();
        $this->addExpensesAccounts();
        $this->addFixedAccounts();
    }

    private function addAssetAccounts()
    {
        $this->createRoot(self::ASSETS_NAME, self::ASSETS_IDENTIFIER);
    }

    /**
     * Creates a root account
     *
     * @param string $name
     * @param string $identifier
     *
     * @return Account, the new root
     */
    public function createRoot($name, $identifier)
    {
        $root = new Account([
            'name' => $name,
        ]);
        $root->partner_id = $this->partner_id;
        $root->identifier = $identifier;
        $root->type_letter = $identifier;
        $root->getSlug();
        $root->save();
        return $root;
    }

    /**
     * @return void
     */
    private function addLiabilitiesAccounts()
    {
        $this->createRoot(self::LIABILITIES_NAME, self::LIABILITIES_IDENTIFIER);
    }

    private function addCapitalAccounts()
    {
        $this->createRoot(self::CAPITAL_NAME, self::CAPITAL_IDENTIFIER);
    }

    private function addIncomeAccounts()
    {
        $this->createRoot(self::INCOME_NAME, self::INCOME_IDENTIFIER);
    }

    private function addExpensesAccounts()
    {
        $top_level_account = $this->createRoot(self::EXPENSES_NAME, self::EXPENSES_IDENTIFIER);
    }

    private function addFixedAccounts(): void
    {
        foreach (self::FIXED_ACCOUNTS as $slug => $details) {
            $parent = Account::where(
                [
                    'partner_id' => $this->partner_id,
                    'slug' => $details['parent-slug'],
                ]
            )->first();

            if (!$parent) {
                throw new AccountException(
                    "Couldn't find parent account with slug {$details['parent-slug']} for group {$this->partner_id}"
                );
            }

            $fixed_account = new Account;
            $fixed_account->partner_id = $this->partner_id;
            $fixed_account->accountable_type = $details['class'];
            $fixed_account->name = $details['name'];
            $fixed_account->is_fixed = true;
            $fixed_account->position = -1;
            $fixed_account->type_letter = $parent->type_letter;
            $fixed_account->getSlug();
            $fixed_account->save();
            $parent->addFixedAccount($fixed_account, $details['identifier']);
        }
    }
}
