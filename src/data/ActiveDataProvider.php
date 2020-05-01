<?php
namespace concepture\yii2logic\data;

use concepture\yii2logic\db\ActiveQuery;
use yii\data\ActiveDataProvider as Base;
use yii\db\ActiveQueryInterface;
use yii\db\QueryInterface;

/**
 * Class ActiveDataProvider
 * @package concepture\yii2logic\data
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class ActiveDataProvider extends Base
{
    /**
     * для возможности делать запрос  через Command
     * @var bool
     */
    public $asArray = false;
    /**
     * {@inheritdoc}
     */
    protected function prepareModels()
    {
        if (!$this->query instanceof QueryInterface) {
            throw new InvalidConfigException('The "query" property must be an instance of a class that implements the QueryInterface e.g. yii\db\Query or its subclasses.');
        }

        $query = clone $this->query;
        if (($pagination = $this->getPagination()) !== false) {
            $pagination->totalCount = $this->getTotalCount();
            if ($pagination->totalCount === 0) {
                return [];
            }
            $query->limit($pagination->getLimit())->offset($pagination->getOffset());
        }
        if (($sort = $this->getSort()) !== false) {
            $query->addOrderBy($sort->getOrders());
        }

        if (! $this->asArray){
            return $query->all($this->db);
        }

        if (! $query instanceof ActiveQuery){
            throw new InvalidConfigException('The "query" property must be an instance of a class concepture\yii2logic\db\ActiveQuery.');
        }

        return $query->queryAllAsArray();
    }
}