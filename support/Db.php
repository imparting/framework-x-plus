<?php

namespace support;

use DI\Annotation\Inject;
use JetBrains\PhpStorm\Pure;
use React\EventLoop\Loop;
use React\MySQL\ConnectionInterface;
use React\MySQL\Factory;
use React\MySQL\QueryResult;
use SQLBuilder\Driver\MySQLDriver;
use support\Query\DeleteQuery;
use support\Query\InsertQuery;
use support\Query\SelectQuery;
use support\Query\UpdateQuery;
use Throwable;
use function React\Async\await;

/**
 * Class Db
 * @package support
 * Operator    Method
 */
class Db
{
    private string $_uri;

    private ConnectionInterface $_connection;

    /**
     * @Inject()
     * @var MySQLDriver $_driver
     */
    private MySQLDriver $_driver;

    public string $table_prefix = '';

    /**
     * Db constructor.
     * @Inject({"db.config"})
     * @param $config
     */
    public function __construct($config)
    {
        $name = $config['default'] ?? 'mysql';
        $config = $config['connections'][$name];
        $this->table_prefix = $config['table_prefix'] ?? '';
        $this->_uri = "{$config['username']}:{$config['password']}@{$config['host']}:{$config['port']}/{$config['database']}?timeout={$config['timeout']}&charset={$config['charset']}";
        $this->_conn();
        $this->_ping();
    }

    private function _ping()
    {
        $that = $this;
        Loop::get()->addPeriodicTimer(50, function () use ($that) {
            $that->_connection->ping();
        });
    }

    private function _conn()
    {
        $this->_connection = (new Factory())->createLazyConnection($this->_uri);
        $that = $this;
        $this->_connection->once('close', function () use ($that) {
            $that->_conn();
        });
    }

    /**
     * @return SelectQuery
     */
    public function selectQuery(): SelectQuery
    {
        return new SelectQuery($this->_driver, $this);
    }

    /**
     * @return InsertQuery
     */
    public function insertQuery(): InsertQuery
    {
        return new InsertQuery($this->_driver, $this);
    }

    /**
     * @return UpdateQuery
     */
    public function updateQuery(): UpdateQuery
    {
        return new UpdateQuery($this->_driver, $this);
    }

    /**
     * @return DeleteQuery
     */
    public function deleteQuery(): DeleteQuery
    {
        return new DeleteQuery($this->_driver, $this);
    }

    /**
     * @throws Throwable
     */
    public function begin()
    {
        $this->query('begin');
    }

    /**
     * @throws Throwable
     */
    public function commit()
    {
        $this->query('commit');
    }

    /**
     * @throws Throwable
     */
    public function rollback()
    {
        $this->query('rollback');
    }

    /**
     * @param string $sql
     * @param array $vars
     * @return QueryResult
     * @throws Throwable
     * insertId 1
     * affectedRows 1
     * resultFields null
     * resultRows null
     * warningCount 0
     */
    public function query(string $sql, array $vars = []): QueryResult
    {
        return await($this->_connection->query($sql, $vars));
    }

    /**
     * @param string $sql
     * @param array $vars
     * @param array $callables
     * @return mixed
     * @throws Throwable
     */
    public function queryStream(string $sql, array $vars = [], $callables = []): mixed
    {
        $stream = $this->_connection->queryStream($sql, $vars);
        return await(streamMap($stream, ...$callables));
    }
}