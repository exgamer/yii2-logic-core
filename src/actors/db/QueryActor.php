<?php
namespace concepture\yii2logic\actors\db;

use concepture\yii2logic\db\HasPropertyActiveQuery;
use Yii;
use concepture\yii2logic\actors\Actor;
use yii\base\Component;

/**
 * Class QueryActor
 * @package concepture\yii2logic\actors\db
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class QueryActor extends Actor
{
    /** @var HasPropertyActiveQuery*/
    public $query;
}