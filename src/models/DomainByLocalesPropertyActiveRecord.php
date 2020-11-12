<?php
namespace concepture\yii2logic\models;

use concepture\yii2logic\models\interfaces\HasDomainByLocalesPropertyInterface;
use concepture\yii2logic\models\traits\v2\property\HasDomainByLocalesPropertyTrait;

/**
 * Class DomainByLocalesPropertyActiveRecord
 * @package concepture\yii2logic\models
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class DomainByLocalesPropertyActiveRecord extends ActiveRecord implements HasDomainByLocalesPropertyInterface
{
    use HasDomainByLocalesPropertyTrait;
}