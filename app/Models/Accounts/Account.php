<?php

namespace App\Models\Accounts;

use App\Exceptions\AccountException;
use App\Models\JournalEntry;
use App\Models\Partner;
use App\Services\Account\AccountSeederService;
use Franzose\ClosureTable\Contracts\EntityInterface;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Account extends AccountEntity
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'accounts';

    /**
     * ClosureTable model instance.
     */
    protected $closure = 'App\Models\Accounts\AccountClosure';


    const NAME_MAX_LENGTH = 100;
    const CORE_ACCOUNT_DEPTH = 2;
    const DIGITS_PER_CORE_ACCOUNT = 1;
    const DIGITS_PER_NONCORE_ACCOUNT = 2;
    const MAX_CORE_SIBLING_POSITION = 8;
    const MAX_NONCORE_SIBLING_POSITION = 98;

    protected $fillable = ['name', 'partner_id', 'type_letter', 'slug', 'identifier'];

    public function partner(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    /**
     * Loops through a tree and gets the child and grand-child items
     *
     * @param $accounts_tree
     * @return $result
     */
    public static function getChildAndGrandChildAccountsFromTree($accounts_tree)
    {
        foreach ($accounts_tree as $account) {
            if ($account->all_children !== null) {
                foreach ($account->all_children as $child_account) {
                    if ($child_account->all_children !== null) {
                        foreach ($child_account->all_children as $grand_child_account) {
                            $result['option_group'][$account->name . ' > ' . $child_account->name][] = $grand_child_account;
                        }
                    } else {
                        $result['option_group'][$account->name][] = $child_account;
                    }
                }
            } else {
                $result['option'][] = $account;
            }
        }
        return $result;
    }

    /**
     * Find all the leaves from a given slug.
     *
     * @param string $slug - the fixed slug of the gla
     * @return array - the leaves
     */
    public static function leavesFromSlug($slug, $partner_id = null)
    {
        $root = self::where('slug', $slug)
            ->where('partner_id', $partner_id)
            ->first();
        $tree = self::getTreeWhere('identifier', 'LIKE', $root->identifier . '%')
            ->where('partner_id', $partner_id);
        return self::collectLeaves($tree->first());
    }

    public static function childFromSlug($slug, $parent_id, $partner_id = null)
    {
        $root = self::where('slug', $slug)
            ->where('partner_id', $partner_id)
            ->first();

        return self::where('identifier', 'LIKE', $root->identifier . '%')
            ->where('partner_id', $partner_id)
            ->where('parent_id', $parent_id)
            ->get();
    }

    /**
     * A recursive function that collects leaves from whichever tree
     * node is first passed in. See function filteredLeavesFromSlug for
     * usage.
     *
     * @param Account $tree_node - the node to search from
     * @param array $collected_nodes - the array of collected nodes to this point
     * @return array - the collected nodes
     */
    public static function collectLeaves($tree_node, $collected_nodes = [])
    {
        if (isset($tree_node->all_children)) {
            foreach ($tree_node->all_children as $child) {
                $collected_nodes = self::collectLeaves($child, $collected_nodes);
            }
        } else {
            $collected_nodes[] = $tree_node;
        }
        return $collected_nodes;
    }

    public static function fromSlug($slug, $partner_id)
    {
        return Account::where('partner_id', $partner_id)
            ->where('slug', $slug)
            ->first();
    }

    public static function cashBox($partner_id)
    {
        return self::where('slug', 'float-account')
            ->where('partner_id', $partner_id)
            ->first();
    }

    /**
     * Echo the tree of General Ledger Accounts from the root node
     *  described by $identifier.
     *
     * Usage: mgc('gla')::echoTreeFrom('A')
     *
     * @param string $identifier - the identifier to echo from
     */
    public static function echoTreeFrom(string $identifier, $partner_id = 1)
    {
        $tree = self::getTreeWhere('identifier', 'LIKE', $identifier . '%')
            ->where('partner_id', $partner_id)
            ->first();
        self::echoTree($tree);
    }

    /**
     * Recursively iterate tree, echoing as needed
     *
     * Note that this function makes multiple queries to the database.
     * Should not be used in production
     */
    public static function echoTree($tree)
    {
        $tree->detail();
        if ($tree->all_children !== null) {
            foreach ($tree->childrenByPosition() as $child) {
                self::echoTree($child);
            }
        }
    }

    /**
     * Find all child leaves for a given identifier
     *
     * @param string $identifier //
     *
     * @return array
     */
    public static function leavesFromIdentifier($identifier, $partner_id = null)
    {
        $tree = self::getTreeWhere('identifier', 'LIKE', $identifier . '%')
            ->where('partner_id', $partner_id);
        return self::collectLeaves($tree->first());
    }

    /**
     * Get a random leaf from the given slug
     * @param string $slug - the slug to grab
     * @return
     */
    public static function randomLeafFromSlug(string $slug, int $partner_id = null)
    {
        $leaves = self::leavesFromSlug($slug, $partner_id);
        return self::randomAccount($leaves);
    }

    private static function randomAccount($accounts)
    {
        shuffle($accounts);
        return $accounts[0] ?? null;
    }

    /**
     * Set the account type on this account
     *
     *  A11 would get $account->type = 'Asset'
     *
     * @param array $accounts
     */
    private static function setAccountTypes(array $accounts, int $partner_id)
    {
        return array_map(function ($account) use ($partner_id) {
            $account->type = self::where('partner_id', $partner_id)
                ->where('identifier', substr($account->identifier, 0, 1))
                ->first()->name;
            return $account;
        }, $accounts);
    }

    /**
     * @param int accepts an int, account id
     *
     * @return boolean true, if account is an asset or expense account
     */
    public static function isAssetOrExpenseAccount(int $account_id)
    {
        $account_identifier = self::find($account_id)->identifier;
        $assets_identifier = AccountSeederService::ASSETS_IDENTIFIER;
        $expenses_identifier = AccountSeederService::EXPENSES_IDENTIFIER;
        if (
            substr($account_identifier, 0, 1) === $assets_identifier ||
            substr($account_identifier, 0, 1) === $expenses_identifier
        ) {
            return true;
        }
        return false;
    }

    /**
     * Sets the route key name, used to resolve custom route-model-binding
     *
     * @return string the column on which the route attempts to find a match
     */
    public function getRouteKeyName()
    {
        return 'identifier';
    }

    public function setTypeLetter()
    {
        $this->type_letter = ucwords(substr($this->identifier, 0, 1));
    }

    /**
     * Polymorph to Branches, LoanProductAccount, etc.
     */
    public function accountable()
    {
        return $this->morphTo();
    }

    /**
     * Get the validation rules for the group.
     *
     * This function may be called from the FormRequest
     * class, or the ValidTrait to get the rules for a simple entity
     *
     * @return array - the rules
     */
    public function rules($partner_id = null, $parent_id = null)
    {
        $partner_id = $partner_id ?: $this->partner_id;
        $parent_id = $parent_id ?: $this->parent_id;

        return [
            'name' => 'required',
            'identifier' => 'max:20',
            'balance' => 'nullable|numeric',
        ];
    }

    /**
     * Retrieves the top level ID by substringing the first character
     *
     * @return string a single character representing the top-level ID
     */
    public function topLevelId()
    {
        return substr($this->identifier, 0, 1);
    }

    /**
     * Check if children exist on this GLA
     *
     * @return bool - true if children exist
     */
    public function childrenExist()
    {
        return (!isset($this->all_children) || $this->all_children->count() == 0);
    }

    /**
     * Appends a child to the model.
     * @param EntityInterface $child
     * @param int $position
     * @param bool $returnChild
     * @return EntityInterface
     * @throws AccountException
     */
    public function addChild(EntityInterface $child, $position = null, $returnChild = false)
    {
        if (!$this->isFixed() && !$this->hasChildSpace()) {
            return false;
        }

        if ($this->isMaxDepth()) {
            throw new AccountException(
                sprintf(
                    "Creating the child account %s of parent %s would exceed the maximum depth",
                    $child->name,
                    $this->name
                )
            );
        }
        $result = parent::addChild($child, $position, $returnChild);

        $child->save();

        return $result;
    }

    /**
     * @return boolean - true if the account is fixed
     */
    public function isFixed()
    {
        return (bool) $this->is_fixed;
    }

    /**
     *   Checks if there is space for another child
     */
    public function hasChildSpace()
    {
        return ($this->greatestChildPosition() < $this->maxChildrenPosition());
    }

    /**
     *   Gets the greatest Child Position
     */
    public function greatestChildPosition()
    {
        $children = $this->getChildren();
        return self::greatestPosition($children);
    }

    /**
     *   Checks the greatest position in a list of siblings.
     */
    private static function greatestPosition($siblings)
    {
        $max = -1;
        foreach ($siblings as $sibling) {
            if ($sibling->position > $max) {
                $max = $sibling->position;
            }
        }
        return $max;
    }

    /**
     * Retrieves the max sibling number that a child of this node can have
     *
     * Position goes 0-8 so identifiers run 1-9
     *   or 0-98, identifiers 01-99
     *
     * If $this->identifier is A: maxChildrenPosition() == 8
     * If $this->identifier is A1: maxChildrenPosition() == 8
     * If $this->identifier is A11: maxChildrenPosition() == 98
     * If $this->identifier is A1101: maxChildrenPosition() == 98
     *
     * @return int
     */
    private function maxChildrenPosition()
    {
        return ($this->real_depth <= self::CORE_ACCOUNT_DEPTH - 1) ?
            self::MAX_CORE_SIBLING_POSITION :
            self::MAX_NONCORE_SIBLING_POSITION;
    }

    /**
     * Checks if the account is at the maximum depth of
     * 5 where the root is zero.
     *
     * @return  boolean
     */
    public function isMaxDepth()
    {
        return $this->real_depth === 5;
    }

    private function childIdentifier($child)
    {
        return $this->identifier . $child->displayPosition();
    }

    /**
     * Tells whether the account is a core account or not
     *
     * @return boolean true if the account is a Core account, false otherwise
     */
    public function isCore()
    {
        return ($this->real_depth <= self::CORE_ACCOUNT_DEPTH);
    }

    /**
     * Add a fixed child to the chart.
     * Should be outside the regular positioning system, so
     *  the null position is given
     * @param EntityInterface $child - the child to add
     * @return EntityInterface
     */
    public function addFixedAccount(EntityInterface $child, string $identifying_character)
    {
        $child->identifier = $identifying_character ?? $this->identifier;
        $child->position = -1;
        $child->moveTo(null, $this);
        return $child;
    }

    /**
     *   Retrieve the children, usorted by their position.
     */
    public function childrenByPosition()
    {
        $children = $this->all_children->load('accountable')->all();
        usort($children, ['self', 'orderAccounts']);
        return $children;
    }

    /**
     *   Delete an account from anywhere in the tree, and
     * then reorder children. Reordering after deletion ensures that
     * each child's identifier is updated to be its position+1
     *
     *   This leaves no gaps in the sibling position order
     */
    public function deleteAccount()
    {
        $parent = $this->getParent();
        $this->delete();
        $parent->reorderChildren();
    }

    /**
     *   Reposition the children by ordering them, then restarting
     *   positioning at zero.
     */
    private function reorderChildren()
    {
        $children = $this->getChildren()->all();
        usort($children, ['self', 'orderAccounts']);

        $position = 0;
        foreach ($children as $index => $child) {
            if (!$child->isFixed()) {
                $child->position = $position;
                $position++;
                $child->identifier = $this->childIdentifier($child);
            }

            $child->save();
        }
    }

    /**
     * Gets the "children" relation index.
     *
     * @return string
     */
    public function getChildrenRelationIndex()
    {
        return 'all_children';
    }

    /**
     * Walk the tree of accounts and add subaccounts as needed
     *
     * @param mixed $accounts An array of accounts or an account name
     * @param EntityInterface $parent_account The parent account on which to add this child.
     */
    public function insertAccounts($accounts)
    {
        foreach ($accounts as $account => $subaccounts) {
            if (is_array($subaccounts)) {
                if ($new_account = $this->addSubaccount($this, $account)) {
                    $new_account->insertAccounts($subaccounts);
                }
            } else {
                $this->addSubaccount($this, $subaccounts);
            }
        }
    }

    /**
     *  Add a subaccount
     *
     * @param        $parent_account
     * @param string $name - of the subaccount
     *
     * @return
     */
    private function addSubaccount($parent_account, $name)
    {
        $child = new Account(['name' => $name]);
        $child->partner_id = $this->partner_id;
        $child->type_letter = $this->type_letter;
        $child->identifier = $parent_account->identifier . "." . rand(100, 999);
        $child->getSlug();
        $child->save();
        if (!$child) {
            echo "add Subaccount: $name\n";
            print_r("Error occurred: \n");
            echo "--- \n";
            print_r($parent_account->getAttributes());
            echo "end add Subaccount\n";
        }
        return $parent_account->addChild($child, null, true);
    }

    public function getSlug()
    {
        return $this->slug = strtolower(str_replace([' ', ',', '&'], '-', $this->name));
    }

    /**
     * Determines whether this account's child is a core account
     *
     * If $this->real_depth is 2 and CORE_ACCOUNT_DEPTH==2 (indexed at 0)
     *     then childIsCore() == false since the child will have real_depth==3
     * @return boolean
     */
    public function childIsCore()
    {
        return ($this->real_depth <= self::CORE_ACCOUNT_DEPTH - 1);
    }

    /**
     * Echo the details for a single general ledger account
     */
    public function detail()
    {
        for ($i = 0; $i < $this->real_depth; $i++) {
            echo "  ";
        }

        echo "{$this->id} - ";
        echo "@{$this->position} {$this->identifier} - {$this->name}";

        if ($this->slug) {
            echo " - ({$this->slug}) ";
        }
        echo "[";
        echo ($this->is_fixed) ? 'F' : '';
        echo ($this->is_managed) ? 'M' : '';
        echo "]\n";
    }

    /**
     * Given a credit record and debit record (from a line item, presumably)
     *  determine whether this GLA would drop below zero
     *
     *  First, check if the given debits/credits will result in a decreased
     *  balance on this GLA. Depends on the GLA type
     *
     * @param float $debit_record
     * @param float $credit_record
     * @return boolean
     */
    public function wouldDropBelowZero($debit_record, $credit_record)
    {
        if (!$this->willDecreaseBalance($debit_record, $credit_record)) {
            return false;
        }

        $transaction_amount = $debit_record + $credit_record;
        return (($this->balance - $transaction_amount) < 0);
    }

    /**
     * Check if some given debit & credit records will decrease the
     *  balance of this GLA. May be used as a pre-check to see if
     *  the reduction will decrease below zero.
     *
     * @param float $debit_record
     * @param float $credit_record
     * @return boolean
     */
    public function willDecreaseBalance($debit_record, $credit_record)
    {
        return ($debit_record > 0 && $this->isCreditNormal())
            || ($credit_record > 0 && $this->isDebitNormal());
    }

    /**
     * Credit normal accounts are those that increase with credits.
     */
    public function isCreditNormal()
    {
        return !$this->isDebitNormal();
    }

    /**
     * Debit increases balance.
     *
     * @return boolean
     */
    public function isDebitNormal()
    {
        return in_array($this->type_letter, [
            AccountSeederService::ASSETS_IDENTIFIER,
            AccountSeederService::EXPENSES_IDENTIFIER
        ]);
    }

    /**
     * Given a string like "Assets" or "Expenses", return true
     *
     * False otherwise
     */
    public function isAssetExpenseAccount()
    {
        return ($this->type_letter === AccountSeederService::ASSETS_IDENTIFIER ||
            $this->type_letter === AccountSeederService::EXPENSES_IDENTIFIER);
    }

    /**
     * Given a string like "Capital", "Income", or "Liability",
     * return true
     *
     * False otherwise
     *
     * Equity is sometimes used in place of Capital. Both are accepted on import
     */
    public function isCapitalIncomeLiabilityAccount()
    {
        return ($this->type_letter === AccountSeederService::CAPITAL_IDENTIFIER ||
            $this->type_letter === AccountSeederService::INCOME_IDENTIFIER ||
            $this->type_letter === AccountSeederService::LIABILITIES_IDENTIFIER);
    }

    /**
     * Overrides the inherited method to stop it from reordering. Its
     * reordering method doesn't account for deleted nodes
     */
    protected function reorderSiblings($parentIdChanged = false)
    {
        list($range, $action) = $this->setupReordering($parentIdChanged);

        $positionColumn = $this->getPositionColumn();

        // As the method called twice (before moving and after moving),
        // first we gather "old" siblings by the old parent id value of the model.
        if ($parentIdChanged === true) {
            $query = $this->siblings(false, $this->old_parent_id);
        } else {
            $query = $this->siblings();
        }
    }

    /**
     * Order the accounts A & B based on their fixed name first, then their position.
     *
     * @param Account $a
     * @param Account $b
     * @return int the sorting direction
     */
    private function orderAccounts($a, $b)
    {
        if ($a->is_fixed || $b->is_fixed) {
            return $b->is_fixed <=> $a->is_fixed;
        }
        return $a->position <=> $b->position;
    }

    private function getIdentifier($parent = null)
    {
        if ($parent === null) {
            $parent = $this->getParent();
        }
        return $parent->identifier . $this->displayPosition();
    }

    /**
     *  Returns the padded display position.
     */
    private function displayPosition()
    {
        return str_pad(
            $this->position + 1,
            $this->digitsAtLevel(),
            '0',
            STR_PAD_LEFT
        );
    }

    /**
     * Retrieves the number of digits in this account's identifier
     *
     * If $this->identifier is A1: 1
     * If $this->identifier is A12304: 2
     *
     * @return int
     */
    private function digitsAtLevel()
    {
        return ($this->real_depth <= self::CORE_ACCOUNT_DEPTH) ?
            self::DIGITS_PER_CORE_ACCOUNT :
            self::DIGITS_PER_NONCORE_ACCOUNT;
    }

    private function setAccountableBalance()
    {
        $this->accountable->balance = $this->balance;
        $this->save();
    }

    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    public function journal_entries()
    {
        return $this->hasMany(JournalEntry::class, 'account_id');
    }

    public function currentBalance(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (in_array($this->type_letter, ['A', 'E'])) {
                    return $this->journal_entries()->sum('debit_amount') - $this->journal_entries()->sum('credit_amount');
                }

                return $this->journal_entries()->sum('credit_amount') - $this->journal_entries()->sum('debit_amount');
            }
        );
    }

    public function makeIdentifierFromName()
    {
        $string = $this->name;
        $words = explode(" ", $string);
        $result = '';

        foreach ($words as $word) {
            $result .= strtoupper($word[0]);
        }

        return $result;
    }
}
