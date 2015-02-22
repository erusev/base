<?php

namespace Base;

use Exception;
use PDO;
use PDOStatement;

class Base
{
    function __construct($dsn, $username = null, $password = null, array $options = array())
    {
        $PDO = new PDO($dsn, $username, $password, $options);

        $this->PDO = $PDO;
    }

    private $PDO;

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
            ->whereEqual('id', $id)
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
            ->whereEqual('id', $id)
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

    function deleteItem($table, $id)
    {
        $impactedRecordCount = $this->find($table)
            ->whereEqual('id', $id)
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
}
