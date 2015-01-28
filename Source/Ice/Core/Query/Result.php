<?php
/**
 * Ice core data class
 *
 * @link http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\Core;

use Ice\Core;
use Ice\Helper\Arrays;
use Ice\Helper\Serializer;

/**
 * Class Data
 *
 * Core Data class
 *
 * @see Ice\Core\Container
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package Ice
 * @subpackage Core
 */
class Query_Result
{
    const ROWS = 'rows';
    const QUERY = 'query';
    const NUM_ROWS = 'numRows';
    const AFFECTED_ROWS = 'affectedRows';
    const INSERT_ID = 'insertId';

    /**
     * Default result
     *
     * @var array
     */
    protected $_default = [
        Query_Result::ROWS => [],
        Query_Result::QUERY => null,
        Query_Result::NUM_ROWS => 0,
        Query_Result::AFFECTED_ROWS => 0,
        Query_Result::INSERT_ID => null
    ];

    /**
     * Result
     *
     * @var array
     */
    private $_result = [];
    private $_modelClass = null;

    /**
     * Attached transformations
     *
     * @var array
     */
    private $_transformations = [];

    /**
     * Constructor of data object
     *
     * @param $modelClass
     * @param array $result
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    private function __construct($modelClass, array $result)
    {
        $this->_modelClass = $modelClass;
        $this->_result = Arrays::defaults($this->_default, $result);
    }

    /**
     * Return data from cache
     *
     * @param $modelClass
     * @param array $result
     * @return Query_Result
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.0
     */
    public static function create($modelClass, array $result = [])
    {
        return new Query_Result($modelClass, $result);
    }

    /**
     * Return all rows from data as array
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getRows()
    {
        $rows = $this->getResult()[self::ROWS];
        return empty($rows) ? [] : $rows;
    }

    /**
     * Result data
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getResult()
    {
        if ($this->_transformations === null) {
            return $this->_result;
        }

        $this->_result[self::ROWS] = $this->applyTransformations($this->_result[self::ROWS]);

        return $this->_result;
    }

    /**
     * Apply all attached transformations
     *
     * @param $rows
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    private function applyTransformations($rows)
    {
        if (empty($this->_transformations)) {
            $this->_transformations = null;
            return $rows;
        }

        $transformData = [];
        foreach ($this->_transformations as $transformation) {
            list($transformationName, $params) = $transformation;
            $transformData[] = Data_Transformation::getInstance($transformationName)
                ->transform($this->getModelClass(), $rows, $params);
        }

        foreach ($rows as $key => &$row) {
            foreach ($transformData as $transform) {
                $row = array_merge($row, $transform[$key]);
            }
        }

        $this->_transformations = null;
        return $rows;
    }

    /**
     * Return target model class of data
     *
     * @return Model
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    public function getModelClass()
    {
        return $this->_modelClass;
    }

    /**
     * Get collection from data
     *
     * @return Model_Collection
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.4
     * @since 0.0
     */
    public function getModelCollection()
    {
        return Model_Collection::create($this->getModelClass(), $this->getRows(), $this->getQuery());
    }

    /**
     * Get value from data
     *
     * @desc Результат запроса - единственное значение.
     *
     * @param null $columnName
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getValue($columnName = null)
    {
        $row = $this->getRow();
        return $row ? ($columnName ? $row[$columnName] : reset($row)) : null;
    }

    /**
     * Get first row from data
     *
     * @desc Результат запроса - единственная запись таблицы.
     *
     * @param null $pk
     * @return array|null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getRow($pk = null)
    {
        $rows = $this->getResult()[self::ROWS];

        if (empty($rows)) {
            return null;
        }

        if (isset($pk)) {
            return isset($rows[$pk]) ? $rows[$pk] : null;
        }

        return reset($rows);
    }

    /**
     * Return model from data
     *
     * @param null $pk
     * @return Model|null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getModel($pk = null)
    {
        $row = $this->getRow($pk);

        if (empty($row)) {
            return null;
        }

        $modelClass = $this->getModelClass();

        return $modelClass::create($row)->clearAffected();
    }

    /**
     * Return count of rows returned by query
     *
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getNumRows()
    {
        return $this->_result[Query_Result::NUM_ROWS];
    }

    /**
     * Remove row from data by pk
     *
     * @param $pk
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function delete($pk = null)
    {
        if (empty($pk)) {
            $this->_result[Query_Result::ROWS] = [];
            return [];
        }

        $row = $this->_result[Query_Result::ROWS][$pk];
        unset($this->_result[Query_Result::ROWS][$pk]);

        return $row;
    }

    /**
     * Attach data transformation
     *
     * @param $transformation
     * @param $params
     * @return $this
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function addTransformation($transformation, $params)
    {
        if ($this->_transformations === null) {
            $this->_transformations = [];
        }

        $this->_transformations[] = [$transformation, $params];
        return $this;
    }

    /**
     * Filter data
     *
     * @param $filterScheme
     * @return Query_Result
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function filter($filterScheme)
    {
        $data = clone $this;
        $data->_result[Query_Result::ROWS] = Arrays::filter($data->_result[Query_Result::ROWS], $filterScheme);
        return $data;
    }

    /**
     * Return column in data
     *
     * @param null $fieldName
     * @param null $indexKey
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getColumn($fieldName = null, $indexKey = null)
    {
        return empty($fieldName)
            ? $this->getKeys()
            : Arrays::column($this->_result[Query_Result::ROWS], $fieldName, $indexKey);
    }

    /**
     * Return keys of data
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getKeys()
    {
        return array_keys($this->_result[Query_Result::ROWS]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to retrieve
     * @link http://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset) ? $this->_result[Query_Result::ROWS][$offset] : null;
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Whether a offset exists
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->_result[Query_Result::ROWS][$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to set
     * @link http://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->_result[Query_Result::ROWS][] = $value;
        } else {
            $this->_result[Query_Result::ROWS][$offset] = $value;
        }
    }

    /**
     * (PHP 5 &gt;= 5.0.0)<br/>
     * Offset to unset
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->_result[Query_Result::ROWS][$offset]);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Count elements of an object
     * @link http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     * </p>
     * <p>
     * The return value is cast to an integer.
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function count()
    {
        return count($this->_result[Query_Result::ROWS]);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function serialize()
    {
        return Serializer::serialize($this->_result);
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function unserialize($serialized)
    {
        $this->_result = Serializer::unserialize($serialized);
    }

    /**
     * Return inserted id
     *
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getInsertId()
    {
        return $this->_result[Query_Result::INSERT_ID];
    }

    /**
     * Return random key
     *
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since 0.0
     */
    public function getRandKey()
    {
        return array_rand($this->getResult()[Query_Result::ROWS]);
    }

    /**
     * Return count of affectd rows
     *
     * @return int
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.2
     */
    public function getAffectedRows()
    {
        return $this->_result[Query_Result::AFFECTED_ROWS];
    }

    /**
     * Return query of query result
     *
     * @return Query
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since 0.2
     */
    public function getQuery()
    {
        return $this->_result[Query_Result::QUERY];
    }
}