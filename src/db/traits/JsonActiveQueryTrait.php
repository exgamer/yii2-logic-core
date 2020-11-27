<?php

namespace concepture\yii2logic\db\traits;

use Exception;
use Yii;
use yii\base\InvalidConfigException;

/**
 * Trait JsonActiveQueryTrait
 * @package concepture\yii2logic\db\traits
 */
trait JsonActiveQueryTrait
{
//    public function addJsonSelect($params)
//    {
//        $columns = [];
//        foreach ($params as $key => $value) {
//            if (is_numeric($key)) {
//                list($cont, $column) = $this->splitJsonColumn($value);
//                $valArr = explode('.', $value);
//                $as     = end($valArr);
//            } else {
//                list($cont, $column) = $this->splitJsonColumn($key);
//                $as = $value;
//            }
//
//            $columns[] = "JSON_EXTRACT($cont, '$column') AS $as";
//        }
//
//        $this->addSelect($columns);
//
//        return $this;
//    }

    public function andJsonWhereNotNull($params)
    {
        if (! is_array($params)) {
            $params[$params] = null;
        }

        $tmp = [];
        foreach ($params as $key => $param) {
            if ( filter_var($key, FILTER_VALIDATE_INT) !== false ) {
                $tmp[$param] = null;
                continue;
            }

            $tmp[$key] = null;
        }

        $params = $tmp;
        $conditions = $this->getCondition($params);
        foreach ($conditions as $key => $value) {
            $this->andWhere(['not', [$key => $value]]);
        }

        return $this;
    }

    public function andJsonWhere($params)
    {
        $condition = $this->getCondition($params);
        $this->andWhere($condition);

        return $this;
    }

    /**
     * Собирает условие для json
     *
     * @param $params
     * @return array
     * @throws Exception
     */
    public function getCondition($params)
    {
        $condition = [];
        foreach ($params as $key => $value) {
            list($cont, $column) = $this->splitJsonColumn($key);
            $condition["JSON_EXTRACT($cont, '$column')"] = $value;
        }

        return $condition;
    }

    /**
     * Разбивает массив для генерации условия json
     *
     * @param $column
     * @return array
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function splitJsonColumn($column)
    {
        $columns = explode('.', $column);
        $cond    = array_shift($columns);
        if (empty($columns)) {
            throw new Exception("no json columns specified");
        }

        $quotes = false;
        foreach ($columns as $column) {
            if (strpos($column, '-') !== false) {
                $quotes = true;
            }
        }

        foreach ($columns as &$column) {
            if ($quotes){
                $column = '"' . $column . '"';
            }
        }

        array_unshift($columns, '$');
        $jsonCond = implode('.', $columns);

        return [$cond, $jsonCond];
    }
}
