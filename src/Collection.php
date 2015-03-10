<?php

namespace Base;

class Collection
{
    /**
     * @param Base $Base
     * @param string $table
     */
    function __construct(Base $Base, $table)
    {
        $this->Base = $Base;
        $this->table = $table;

        $this->tableClause = "`$table`";
        $this->whereClause = '1';
    }

    private $Base;
    private $table;

    # ~

    private $parameters = array();

    private $tableClause;
    private $whereClause;
    private $groupClause;
    private $limitClause;
    private $orderClause;

    #
    # Relationships
    #

    /**
     * @param string $table
     * @param string $foreignKey
     * @return $this
     */
    function has($table, $foreignKey = null)
    {
        $foreignKey = $foreignKey ?: $this->table.$this->Base->fkEnding;

        $this->tableClause .= " LEFT JOIN `$table` ON `$this->table`.id = `$table`.`$foreignKey`";

        return $this;
    }

    /**
     * @param string $table
     * @param string $foreignKey
     * @return $this
     */
    function belongsTo($table, $foreignKey = null)
    {
        $foreignKey = $foreignKey ?: $table.$this->Base->fkEnding;

        $this->tableClause .= " LEFT JOIN `$table` ON `$this->table`.`$foreignKey` = `$table`.`id`";

        return $this;
    }

    /**
     * @param string $table
     * @return $this
     */
    function hasAndBelongsTo($table)
    {
        $tables = array($this->table, $table);

        sort($tables);

        $joinTable = join('_', $tables);

        $aKey = $this->table.$this->Base->fkEnding;
        $bKey = $table.$this->Base->fkEnding;

        $this->tableClause .= "
			LEFT JOIN `$joinTable` ON `$this->table`.`id` = `$joinTable`.`$aKey`
			LEFT JOIN `$table` ON `$table`.id = `$joinTable`.`$bKey`";

        return $this;
    }

    #
    # Conditions
    #

    /**
     * @param string $condition
     * @param array $values
     * @return $this
     */
    function where($condition, array $values = array())
    {
        $this->whereClause .= " AND $condition";

        foreach ($values as $value)
        {
            $this->parameters []= $value;
        }

        return $this;
    }

    /**
     * @param string $field
     * @param $value
     * @param bool $reverse
     * @return $this
     */
    function whereEqual($field, $value, $reverse = false)
    {
        $field = $this->sanitizePath($field);

        $operator = $reverse ? '!=' : '=';

        $this->whereClause .= " AND $field $operator ?";

        $this->parameters []= $value;

        return $this;
    }

    /**
     * @param string $field
     * @param $value
     * @return $this
     */
    function whereNotEqual($field, $value)
    {
        $this->whereEqual($field, $value, true);

        return $this;
    }

    /**
     * @param string $field
     * @param array $values
     * @param bool $reverse
     * @return $this
     */
    function whereIn($field, array $values, $reverse = false)
    {
        $field = $this->sanitizePath($field);

        $operator = $reverse ? 'NOT IN' : 'IN';

        $this->whereClause .= " AND $field $operator (";

        foreach ($values as $value)
        {
            $this->whereClause .= '?, ';

            $this->parameters []= $value;
        }

        $this->whereClause = substr_replace($this->whereClause, ')', - 2);

        return $this;
    }

    /**
     * @param string $field
     * @param array $values
     * @return $this
     */
    function whereNotIn($field, array $values)
    {
        $this->whereIn($field, $values, true);

        return $this;
    }

    /**
     * @param string $field
     * @param bool $reverse
     * @return $this
     */
    function whereNull($field, $reverse = false)
    {
        $field = $this->sanitizePath($field);

        $operator = $reverse ? 'IS NOT' : 'IS';

        $this->whereClause .= " AND $field $operator NULL";

        return $this;
    }

    /**
     * @param string $field
     * @return $this
     */
    function whereNotNull($field)
    {
        $this->whereNull($field, true);

        return $this;
    }

    #
    # GROUP BY
    #

    /**
     * @param string $group
     * @return $this
     */
    function group($group)
    {
        $this->groupClause = $group;

        return $this;
    }

    #
    # LIMIT
    #

    /**
     * @param string $limit
     * @return $this
     */
    function limit($limit)
    {
        $this->limitClause = $limit;

        return $this;
    }

    #
    # ORDER BY
    #

    /**
     * @param string $order
     * @return $this
     */
    function order($order)
    {
        $this->orderClause = $order;

        return $this;
    }

    /**
     * @param string $field
     * @return $this
     */
    function orderAsc($field)
    {
        $field = $this->sanitizePath($field);

        $this->orderClause = $field . ' ASC';

        return $this;
    }

    /**
     * @param string $field
     * @return $this
     */
    function orderDesc($field)
    {
        $field = $this->sanitizePath($field);

        $this->orderClause = $field . ' DESC';

        return $this;
    }

    #
    #
    # Actions
    #
    #

    /**
     * @param string $selectExpression
     * @return array
     */
    function read($selectExpression = null)
    {
        $statement = $this->composeReadStatement($selectExpression);

        $Records = $this->Base->read($statement, $this->parameters);

        return $Records;
    }

    /**
     * @param string $selectExpression
     * @return array
     */
    function readRecord($selectExpression = null)
    {
        $statement = $this->composeReadStatement($selectExpression);

        $Record = $this->Base->readRecord($statement, $this->parameters);

        return $Record;
    }

    /**
     * @param string $selectExpression
     * @return string
     */
    function readField($selectExpression = null)
    {
        $statement = $this->composeReadStatement($selectExpression);

        $field = $this->Base->readField($statement, $this->parameters);

        return $field;
    }

    /**
     * @param string $selectExpression
     * @return array
     */
    function readFields($selectExpression = null)
    {
        $statement = $this->composeReadStatement($selectExpression);

        $fields = $this->Base->readFields($statement, $this->parameters);

        return $fields;
    }

    /**
     * @return int
     */
    function count()
    {
        $count = (int) $this->readField('COUNT(*)');

        return $count;
    }

    /**
     * @param array $Data
     * @return int
     */
    function update(array $Data)
    {
        $statement = "UPDATE `$this->table` SET ";

        $fields = array_keys($Data);

        foreach ($fields as $field)
        {
            $statement .= "`$this->table`.`$field` = ?, ";
        }

        $statement = substr_replace($statement, " WHERE $this->whereClause", - 2);

        $this->limitClause and $statement .= " LIMIT $this->limitClause";

        $parameters = array_values($Data);
        $parameters = array_merge($parameters, $this->parameters);

        $impactedRecordCount = $this->Base->update($statement, $parameters);

        return $impactedRecordCount;
    }

    /**
     * @return int
     */
    function delete()
    {
        $statement = "DELETE FROM $this->tableClause WHERE $this->whereClause";

        $this->orderClause and $statement .= " ORDER BY $this->orderClause";
        $this->limitClause and $statement .= " LIMIT $this->limitClause";

        $impactedRecordCount = $this->Base->update($statement, $this->parameters);

        return $impactedRecordCount;
    }

    #
    # Protected
    #

    /**
     * @param string $selectExpression
     * @return string
     */
    protected function composeReadStatement($selectExpression = null)
    {
        $selectExpression = $selectExpression ?: "`$this->table`.*";

        $query = "SELECT $selectExpression FROM $this->tableClause WHERE $this->whereClause";

        $this->groupClause and $query .= " GROUP BY $this->groupClause";
        $this->orderClause and $query .= " ORDER BY $this->orderClause";
        $this->limitClause and $query .= " LIMIT $this->limitClause";

        return $query;
    }

    /**
     * @param string $path
     * @return string
     */
    protected function sanitizePath($path)
    {
        $path = str_replace('`', '', $path);
        $path = str_replace('.', '`.`', $path);
        $path = '`'.$path.'`';

        return $path;
    }
}
