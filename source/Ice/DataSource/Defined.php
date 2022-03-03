<?php
/**
 * Ice data source implementation defined class
 *
 * @link      http://www.iceframework.net
 * @copyright Copyright (c) 2014 Ifacesoft | dp <denis.a.shestakov@gmail.com>
 * @license   https://github.com/ifacesoft/Ice/blob/master/LICENSE.md
 */

namespace Ice\DataSource;

use Ice\Core\DataProvider;
use Ice\Core\DataSource;
use Ice\Core\Logger;
use Ice\Core\Model;
use Ice\Core\Module;
use Ice\Core\Query;
use Ice\Core\QueryBuilder;
use Ice\Core\QueryResult;
use Ice\Core\QueryTranslator;
use Ice\Helper\Query as Helper_Query;

/**
 * Class Defined
 *
 * Implements defined data source
 *
 * @see \Ice\Core\DataSource
 *
 * @author dp <denis.a.shestakov@gmail.com>
 *
 * @package    Ice
 * @subpackage DataSource
 */
class Defined extends DataSource
{
    /**
     * Execute query select to data source
     *
     * @param  Query $query
     * @param bool $indexFieldNames
     * @return array
     * @throws \Ice\Core\Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function executeSelect(Query $query, $indexFieldNames = true)
    {
        /**
         * @var Model $modelClass
         */
        $modelClass = $query->getQueryBuilder()->getModelClass();
        $rows = $this->getConnection();

        $pkName = $modelClass::getFieldName('/pk');

        $fieldColumnMap = $modelClass::getScheme()->getFieldColumnMap();
        $flippedFieldNames = array_flip($fieldColumnMap);

        $definedRows = [];
        foreach ($rows as $pk => &$row) {
            $definedRow = [];
            foreach ($row as $fieldName => $fieldValue) {
                if (isset($flippedFieldNames[$fieldName])) {
                    $definedRow[$flippedFieldNames[$fieldName]] = $fieldValue;
                } else {
                    $definedRow[$fieldName] = $fieldValue;
                }
            }
            $definedRow[$fieldColumnMap[$pkName]] = $pk;
            $definedRows[] = $definedRow;
        }
        $rows = &$definedRows;

        $filterFunction = function ($where) {
            return function ($row) use ($where) {
                foreach ($where as $part) {
                    $whereQuery = null;

                    switch ($part[2]) {
                        case QueryBuilder::SQL_COMPARISON_OPERATOR_EQUAL:
                            if (!isset($row[$part[1]]) || $row[$part[1]] != reset($part[3])) {
                                return false;
                            }
                            break;
                        case QueryBuilder::SQL_COMPARISON_OPERATOR_NOT_EQUAL:
                            if ($row[$part[1]] == reset($part[3])) {
                                return false;
                            }
                            break;
                        case QueryBuilder::SQL_COMPARISON_KEYWORD_IN:
                            if (!in_array($row[$part[1]], $part[3])) {
                                return false;
                            }
                            break;
                        case QueryBuilder::SQL_COMPARISON_KEYWORD_IS_NULL:
                            if ($row[$part[1]] !== null) {
                                return false;
                            }
                            break;
                        case QueryBuilder::SQL_COMPARISON_KEYWORD_IS_NOT_NULL:
                            if ($row[$part[1]] === null) {
                                return false;
                            }
                            break;
                        default:
                            Logger::getInstance(__CLASS__)->exception(
                                ['Unknown comparsion operator {$0}', $part[2]],
                                __FILE__,
                                __LINE__
                            );
                    }
                }

                return true;
            };
        };

        $rows = array_filter($rows, $filterFunction(Helper_Query::convertWhereForFilter($query)));

        return [
            QueryResult::ROWS => $rows,
            QueryResult::NUM_ROWS => count($rows)
        ];
    }

    /**
     * Execute query insert to data source
     *
     * @param  Query $query
     * @return void
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function executeInsert(Query $query)
    {
        throw new \Exception('Implement insert() method.');
    }

    /**
     * Execute query update to data source
     *
     * @param  Query $query
     * @return array
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function executeUpdate(Query $query)
    {
        throw new \Exception('Implement update() method.');
    }

    /**
     * Execute query update to data source
     *
     * @param  Query $query
     * @return array
     * @throws \Exception
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function executeDelete(Query $query)
    {
        throw new \Exception('Implement delete() method.');
    }

    /**
     * Return data scheme
     *
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getDataScheme()
    {
        return DataSource::getDefault()->getDataScheme();
    }

    /**
     * Get data Scheme from data source
     *
     * @param Module $module
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getTables(Module $module)
    {
        // TODO: Implement getTables() method.
    }

    /**
     * Get table scheme from source
     *
     * @param  $tableName
     * @return array
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function getColumns($tableName)
    {
        // TODO: Implement getColumns() method.
    }

    /**
     * Execute query create table to data source
     *
     * @param  Query $query
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function executeCreate(Query $query)
    {
        // TODO: Implement executeCreate() method.
    }

    /**
     * Execute query drop table to data source
     *
     * @param  Query $query
     * @return array
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.0
     * @since   0.0
     */
    public function executeDrop(Query $query)
    {
        // TODO: Implement executeDrop() method.
    }

    /**
     * Get table indexes from source
     *
     * @param  $tableName
     * @return array
     *
     * @author anonymous <email>
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.3
     * @since   0.3
     */
    public function getIndexes($tableName)
    {
        // TODO: Implement getIndexes() method.
    }

    /**
     * Prepare query statement for query
     *
     * @param $body
     * @param array $binds
     * @return mixed
     *
     * @author dp <denis.a.shestakov@gmail.com>
     *
     * @version 0.2
     * @since   0.2
     */
    public function getStatement($body, array $binds)
    {
        // TODO: Implement getStatement() method.
    }

    /**
     * Return data provider class
     *
     * @return DataProvider
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function getDataProviderClass()
    {
        // TODO: Implement getDataProviderClass() method.
    }

    /**
     * Return query translator class
     *
     * @return QueryTranslator
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function getQueryTranslatorClass()
    {
        // TODO: Implement getQueryTranslatorClass() method.
    }

    /**
     * Begin transaction
     *
     * @param string $isolationLevel
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function beginTransaction($isolationLevel = null)
    {
        // TODO: Implement beginTransaction() method.
    }

    /**
     * Commit transaction
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function commitTransaction($retry = 0)
    {
        // TODO: Implement commitTransaction() method.
    }

    /**
     * Rollback transaction
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     * @param null $e
     */
    public function rollbackTransaction($e = null)
    {
        // TODO: Implement rollbackTransaction() method.
    }

    /**
     * Get table references from source
     *
     * @param  $tableName
     * @return array
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function getReferences($tableName)
    {
        // TODO: Implement getReferences() method.
    }

    /**
     * Create save point
     *
     * @param $savePoint
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function savePoint($savePoint)
    {
        // TODO: Implement savePointTransaction() method.
    }

    /**
     * Rollback save point
     *
     * @param $savePoint
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function rollbackSavePoint($savePoint)
    {
        // TODO: Implement rollbackSavePoint() method.
    }

    /**
     * Commit save point
     *
     * @param $savePoint
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function releaseSavePoint($savePoint)
    {
        // TODO: Implement releaseSavePoint() method.
    }

    /**
     * Execute native query
     *
     * @param $query
     * @return QueryResult
     */
    public function executeNativeQuery($query)
    {
        // TODO: Implement executeNativeQuery() method.
    }

    /**
     * Execute native query
     *
     * @param $sql
     * @return mixed
     *
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function query($sql)
    {
        // TODO: Implement query() method.
    }

    /**
     * Translate ice query language for get data
     *
     * @param $iceql
     * @return mixed
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function translateGet(array $iceql)
    {
        // TODO: Implement translateGet() method.
    }

    /**
     * Translate ice query language for set data
     *
     * @param $iceql
     * @return mixed setted value
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function translateSet(array $iceql)
    {
        // TODO: Implement translateSet() method.
    }

    /**
     * Translate ice query language for delete data
     *
     * @param $iceql
     * @return bool|mixed
     * @author anonymous <email>
     *
     * @version 0
     * @since   0
     */
    public function translateDelete(array $iceql)
    {
        // TODO: Implement translateDelete() method.
    }

    public function escapeString($string)
    {
        // TODO: Implement escapeString() method.
    }
}
