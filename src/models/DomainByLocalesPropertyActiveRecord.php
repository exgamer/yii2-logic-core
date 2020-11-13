<?php
namespace concepture\yii2logic\models;

use concepture\yii2logic\models\interfaces\HasDomainByLocalesPropertyInterface;
use concepture\yii2logic\models\traits\v2\property\HasDomainByLocalesPropertyTrait;

/**
 * ActiveRecord дял сущностей которые используют property и у которых разбиение идет по domain_id и locale_id
 *
 * Class DomainByLocalesPropertyActiveRecord
 * @package concepture\yii2logic\models
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class DomainByLocalesPropertyActiveRecord extends ActiveRecord implements HasDomainByLocalesPropertyInterface
{
    use HasDomainByLocalesPropertyTrait;
}