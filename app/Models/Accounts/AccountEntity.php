<?php

namespace App\Models\Accounts;

use Franzose\ClosureTable\Models\Entity;

class AccountEntity extends Entity
{
    /**
     * Overrides Builds closure table "where in" query on the given column in Entity.
     *
     * @param string $column
     * @param bool $withSelf
     * @return QueryBuilder
     */
    protected function subqueryClosureBy($column, $withSelf = false)
    {
        switch ($column) {
            case 'ancestor':
                $selectedColumn = $this->closure->getAncestorColumn();
                $whereColumn = $this->closure->getDescendantColumn();
                break;

            case 'descendant':
                $selectedColumn = $this->closure->getDescendantColumn();
                $whereColumn = $this->closure->getAncestorColumn();
                break;
        }

        $depthOperator = ($withSelf === true ? '>=' : '>');

        $ids = $this->closure->select($selectedColumn)
            ->where($whereColumn, '=', $this->getKey())
            ->where($this->closure->getDepthColumn(), $depthOperator, 0)
            ->pluck('id');

        return $this->whereIn($this->getQualifiedKeyName(), $ids);
    }
}
