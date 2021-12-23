<?php

namespace support\Query;

use SQLBuilder\Driver\BaseDriver;
use support\Db;

class BaseQuery
{
    protected BaseDriver $_driver;
    protected Db $db;

    public function __construct(BaseDriver $driver, Db $db)
    {
        $this->_driver = $driver;
        $this->db = $db;
    }

}