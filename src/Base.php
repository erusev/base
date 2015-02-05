<?php

namespace Base;

use Exception;
use PDO;

class Base
{
    function __construct($dsn, $username = null, $password = null)
    {
        $PDO = new PDO($dsn, $username, $password);

        $this->PDO = $PDO;
    }

    private $PDO;

    function read($statement, array $parameters = array())
    {
        $PDOStatement = $this->execute($statement, $parameters);

        $Records = $PDOStatement->fetchAll(PDO::FETCH_ASSOC);

        return $Records;
    }

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

    function readFields($statement, array $parameters = array())
    {
        $PDOStatement = $this->execute($statement, $parameters);

        $fields = $PDOStatement->fetchAll(PDO::FETCH_COLUMN);

        return $fields;
    }

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

    function readItem($table, $id)
    {
        $Record = $this->find($table)
            ->whereEqual('id', $id)
            ->readRecord();

        return $Record;
    }

    function update($statement, array $parameters = array())
    {
        $PDOStatement = $this->execute($statement, $parameters);

        $impactedRecordCount = $PDOStatement->rowCount();

        return $impactedRecordCount;
    }

    function updateItem($table, $id, array $Data)
    {
        $affectedRecordCount = $this->find($table)
            ->whereEqual('id', $id)
            ->update($Data);

        return $affectedRecordCount;
    }

    /**
     * @param string $table
     * @param array $Data
     * @return int
     */
    function createItem($table, array $Data)
    {
        $statement = "INSERT INTO `$table` SET";

        $parameters = [];

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

    function find($table)
    {
        $Collection = new Collection($this, $table);

        return $Collection;
    }

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
