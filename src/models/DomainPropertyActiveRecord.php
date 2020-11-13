<?php
namespace concepture\yii2logic\models;

use concepture\yii2logic\models\interfaces\HasDomainPropertyInterface;
use concepture\yii2logic\models\interfaces\HasPropertyInterface;
use concepture\yii2logic\models\traits\v2\property\HasDomainByLocalesPropertyTrait;
use concepture\yii2logic\models\traits\v2\property\HasDomainPropertyTrait;

/**
 * Class PropertyActiveRecord
 * @package concepture\yii2logic\models
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class DomainPropertyActiveRecord extends ActiveRecord implements HasDomainPropertyInterface
{
    use HasDomainPropertyTrait;
}