<?php

namespace support\Query;

use SQLBuilder\ArgumentArray;
use SQLBuilder\Criteria;
use Throwable;

/**
 * Class SelectQuery
 * @method self where($expr = null, array $args = array())
 * @method self equal($expr, $val)  // =
 * @method self notEqual($expr, $val)  // <>
 * @method self lessThan($expr, $val)  // <
 * @method self lessThanOrEqual($expr, $val)  // <=
 * @method self greaterThan($expr, $val)  // >
 * @method self greaterThanOrEqual($expr, $val)  // >=
 * @method self is($expr, $boolean)  // IS
 * @method self isNot($expr, $boolean)  // IS NOT
 * @method self in($expr, array $values)  // IN (...)
 * @method self notIn($expr, array $values)  // NOT IN (...)
 * @method self like($expr, $pattern, $criteria = Criteria::CONTAINS)  // LIKE
 * @method self notLike($expr, $pattern, $criteria = Criteria::CONTAINS)  // NOT LIKE
 * @method self between($expr, $min, $max)  // BETWEEN {min} AND {max}
 * @method self group()  // ( expr )
 * @method self and ()  // AND
 * @method self or ()  // OR
 * @method self regexp()  // REGEXP
 * @method self notRegexp()  // NOT REGEXP
 * @package support\Query
 */
class SelectQuery extends BaseQuery
{

    private \SQLBuilder\Universal\Query\SelectQuery $_queryBuilder;

    /**
     * @var array
     */
    private array $callables = [];

    public function table($table, $alias = null): static
    {
        $table = '`' . $this->db->table_prefix . $table . '`';
        $this->_queryBuilder = (new \SQLBuilder\Universal\Query\SelectQuery())->from($table, $alias);
        return $this;
    }

    /**
     * @param mixed ...$columns
     * @return $this
     */
    public function select(...$columns): static
    {
        if (empty($columns)) $columns = ['*'];
        $this->_queryBuilder->select(...$columns);
        return $this;
    }

    /**
     * @param $table
     * @param null $alias
     * @param null $conditionExpr
     * @param array $args
     * @return $this
     */
    public function left($table, $alias = null, $conditionExpr = null, array $args = array()): static
    {
        $this->_queryBuilder->join($table, $alias)->left()->on($conditionExpr, $args);
        return $this;
    }

    /**
     * @param $table
     * @param null $alias
     * @param null $conditionExpr
     * @param array $args
     * @return $this
     */
    public function right($table, $alias = null, $conditionExpr = null, array $args = array()): static
    {
        $this->_queryBuilder->join($table, $alias)->right()->on($conditionExpr, $args);
        return $this;
    }

    /**
     * @param $table
     * @param null $alias
     * @param null $conditionExpr
     * @param array $args
     * @return $this
     */
    public function inner($table, $alias = null, $conditionExpr = null, array $args = array()): static
    {
        $this->_queryBuilder->join($table, $alias)->inner()->on($conditionExpr, $args);
        return $this;
    }

    /**
     * @return array|null
     * @throws Throwable
     */
    public function all(): array|null
    {
        $args = new ArgumentArray();
        if (empty($this->_queryBuilder->getSelect())) $this->_queryBuilder->select(['*']);
        $sql = $this->_queryBuilder->toSql($this->_driver, $args);
        if ($this->callables) {
            return $this->db->queryStream($sql, array_values($args->toArray(true)), $this->callables);
        }
        $queryResult = $this->db->query($sql, array_values($args->toArray(true)));
        return $queryResult->resultRows;
    }

    /**
     * @param callable $callable
     * @return $this
     */
    public function map(callable $callable): static
    {
        $this->callables[] = $callable;
        return $this;
    }

    /**
     * @return array|null
     * @throws Throwable
     */
    public function first(): array|null
    {
        $this->_queryBuilder->limit(1);
        $result = $this->all();
        return $result[0] ?? null;
    }

    /**
     * @param $page
     * @param int $pageSize
     * @return array|null
     * @throws Throwable
     */
    public function page($page, $pageSize = 10): array|null
    {
        $this->_queryBuilder->page($page, $pageSize);
        return $this->all();
    }

    /**
     * @param $byExpr
     * @param null $sorting
     * @return $this
     */
    public function orderBy($byExpr, $sorting = null): static
    {
        $this->_queryBuilder->orderBy($byExpr, $sorting);
        return $this;
    }

    /**
     * @param $limit
     * @return $this
     */
    public function limit($limit): static
    {
        $this->_queryBuilder->limit($limit);
        return $this;
    }

    /**
     * @param string $name
     * @param array $arguments
     * @return $this
     */
    public function __call(string $name, array $arguments): static
    {
        $this->_queryBuilder->where()->$name(...$arguments);
        return $this;
    }
}