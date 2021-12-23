<?php

namespace support\Query;

use SQLBuilder\ArgumentArray;
use SQLBuilder\Criteria;
use Throwable;

/**
 * Class UpdateQuery
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
 * @method self and ()  // AND
 * @method self or ()  // OR
 * @method self regexp()  // REGEXP
 * @method self notRegexp()  // NOT REGEXP
 * @package support\Query
 */
class UpdateQuery extends BaseQuery
{
    /**
     * @var \SQLBuilder\Universal\Query\UpdateQuery|null
     */
    private \SQLBuilder\Universal\Query\UpdateQuery|null $_queryBuilder;

    /**
     * @param $table
     * @param null $alias
     * @return $this
     */
    public function table($table, $alias = null): static
    {
        $table = '`' . $this->db->table_prefix . $table . '`';
        $this->_queryBuilder = (new \SQLBuilder\Universal\Query\UpdateQuery())->update($table, $alias);
        return $this;
    }

    /**
     * @param $data
     * @return int|null
     * @throws Throwable
     */
    public function update($data): ?int
    {
        $this->_queryBuilder->set($data);
        $args = new ArgumentArray();
        $sql = $this->_queryBuilder->toSql($this->_driver, $args);
        return $this->db->query($sql, array_values($args->toArray(true)))->affectedRows;
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