<?php

namespace support\Query;

use SQLBuilder\ArgumentArray;
use Throwable;

class InsertQuery extends BaseQuery
{

    private \SQLBuilder\Universal\Query\InsertQuery|null $_queryBuilder;

    public function table($table): static
    {
        $table = '`' . $this->db->table_prefix . $table . '`';
        $this->_queryBuilder = (new \SQLBuilder\Universal\Query\InsertQuery())->into($table);
        return $this;
    }

    /**
     * @param $data
     * @param array|null $returning
     * @return int|bool|array|null
     * @throws Throwable
     */
    public function insert($data, array $returning = null): int|bool|array|null
    {
        $this->_queryBuilder->insert($data);
        if ($returning !== null) $this->_queryBuilder->returning($returning);
        $args = new ArgumentArray();
        $sql = $this->_queryBuilder->toSql($this->_driver, $args);
        $queryResult = $this->db->query($sql, array_values($args->toArray(true)));
        if ($queryResult->affectedRows > 0) {
            if ($returning !== null) {
                return $queryResult->resultRows;
            } else {
                return $queryResult->insertId;
            }
        }
        return null;
    }


}