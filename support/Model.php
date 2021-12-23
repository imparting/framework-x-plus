<?php

namespace support;

use DI\Annotation\Inject;

/**
 * Class Model
 * @package support
 */
class Model
{
    /**
     * @Inject()
     * @var Db $db
     */
    protected Db $db;

}