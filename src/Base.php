<?php

namespace Base;

use Exception;
use PDO;
use PDOStatement;

class Base
{
    protected $pk_key;
    
    private $PDO;
    
    /**
     * Takes the same parameters as the PDO constructor.
     * @link http://php.net/manual/en/pdo.construct.php
     * @param string $dsn
     * @param string $username [optional]
     * @param string $password [optional]
     * @param array $options [optional]
     */
    function __construct($dsn, $username = null, $password = null, array $options = array(), $pk_key = 'id')
    {
        $PDO = new PDO($dsn, $username, $password, $options);

        $this->PDO = $PDO;
        
        $this->pk_key = (string)$pk_key;
    }
    
    public function setPkKey($pk_key = 'id')
    {
        $this->pk_key = (string)$pk_key;
    }
    
    public function getPkKey()
    {
        return $this->pk_key;
    }

    /**
     * @param string $statement
     * @param array $parameters
     * @return array
     */
    function read($statement, array $parameters = array())
    {
        $PDOStatement = $this->execute($statement, $parameters);

        $Records = $PDOStatement->fetchAll(PDO::FETCH_ASSOC);

        return $Records;
    }

    /**
     * @param string $statement
     * @param array $parameters
     * @return array
     */
    function readField($statement, array $parameters = array())
    {
        $PDOStatement = $this->execute($statement, $parameters);

        $Record = $PDOStatement->fetch(PDO::FETCH_COLUMN);

        if ($Record === false)
        {
            return;
        }

        return $Record;
    }

    /**
     * @param string $statement
     * @param array $parameters
     * @return array
     */
    function readFields($statement, array $parameters = array())
    {
        $PDOStatement = $this->execute($statement, $parameters);

        $fields = $PDOStatement->fetchAll(PDO::FETCH_COLUMN);

        return $fields;
    }

    /**
     * @param string $statement
     * @param array $parameters
     * @return array
     */
    function readRecord($statement, array $parameters = array())
    {
        $PDOStatement = $this->execute($statement, $parameters);

        $Record = $PDOStatement->fetch(PDO::FETCH_ASSOC);

        if ($Record === false)
        {
            return;
        }

        return $Record;
    }

    /**
     * @param string $table
     * @param int $id
     * @return array
     */
    function readItem($table, $id)
    {
        $Record = $this->find($table)
            ->whereEqual('id', $this->pk_key)
            ->readRecord();

        return $Record;
    }

    /**
     * @param string $statement
     * @param array $parameters
     * @return int
     */
    function update($statement, array $parameters = array())
    {
        $PDOStatement = $this->execute($statement, $parameters);

        $impactedRecordCount = $PDOStatement->rowCount();

        return $impactedRecordCount;
    }

    /**
     * @param string $table
     * @param int $id
     * @param array $Data
     * @return int
     */
    function updateItem($table, $id, array $Data)
    {
        $impactedRecordCount = $this->find($table)
            ->whereEqual('id', $this->pk_key)
            ->update($Data);

        return $impactedRecordCount;
    }

    /**
     * @param string $table
     * @param array $Data
     * @return int
     */
    function createItem($table, array $Data)
    {
        $statement = "INSERT INTO `$table` SET";

        $parameters = array();

        foreach ($Data as $name => $value)
        {
            $statement .= " `$name` = ?,";

            $parameters []= $value;
        }

        $statement = substr($statement, 0, - 1);

        $this->execute($statement, $parameters);

        $lastId = $this->lastId();

        return $lastId;
    }

    /**
     * @param string $table
     * @param int $id
     * @return int
     */
    function deleteItem($table, $id)
    {
        $impactedRecordCount = $this->find($table)
            ->whereEqual('id', $this->pk_key)
            ->delete();

        return $impactedRecordCount;
    }

    /**
     * @param string $table
     * @return Collection
     */
    function find($table)
    {
        $Collection = new Collection($this, $table);

        return $Collection;
    }

    /**
     * @param string $statement
     * @param array $parameters
     * @return PDOStatement
     * @throws Exception
     */
    function execute($statement, array $parameters = array())
    {
        $PDOStatement = $this->PDO->prepare($statement);

        $successful = $PDOStatement->execute($parameters);

        if ( ! $successful)
        {
            $errorInfo = $PDOStatement->errorInfo();

            throw new Exception($errorInfo[2].': '.$statement);
        }

        return $PDOStatement;
    }

    /**
     * @return int
     */
    function lastId()
    {
        $lastInsertId = (int) $this->PDO->lastInsertId();

        return $lastInsertId;
    }

    /**
     * @return PDO
     */
    function pdo()
    {
        $PDO = $this->PDO;

        return $PDO;
    }

    /**
     * The ending of FK names.
     *
     * @var string
     */
    public $fkEnding = '_id';
}
