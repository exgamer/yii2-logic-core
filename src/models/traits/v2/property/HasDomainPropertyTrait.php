<?php
namespace concepture\yii2logic\models\traits\v2\property;

use concepture\yii2logic\helpers\ClassHelper;
use concepture\yii2logic\models\traits\HasLocalizationTrait;
use Yii;

/**
 * Trait HasDomainPropertyTrait
 * @package common\models\traits
 */
trait HasDomainPropertyTrait
{
    use HasPropertyTrait;

    /**
     * Возвращает название поля по которому будет разделение свойств
     *
     * @return string
     */
    public static function uniqueField()
    {
        return "domain_id";
    }

    /**
     * Возвращает значение поля по которому будет разделение свойств
     *
     * @return mixed
     */
    public static function uniqueFieldValue()
    {
        return Yii::$app->domainService->getCurrentDomainId();
    }

    /**
     * Переопределено для автоподстановки domain_id в релейшн
     * @inheritDoc
     */
    protected function createRelationQuery($class, $link, $multiple)
    {
        $query = parent::createRelationQuery($class, $link, $multiple);
        $relationModel = Yii::createObject($class);
        $domainAlias = "";
        $traits = ClassHelper::getTraits($relationModel);
        if (in_array(HasDomainPropertyTrait::class, $traits) ||
            in_array(HasLocalePropertyTrait::class, $traits)){
            $propModelClass = $relationModel::getPropertyModelClass();
            $propModel = Yii::createObject($propModelClass);
            if ($propModel->hasAttribute('domain_id')){
                $domainAlias = $relationModel::propertyAlias() . ".";
            }
        }
        if (in_array(HasLocalizationTrait::class, $traits)){
            $propModelClass = $relationModel::getLocalizationModelClass();
            $propModel = Yii::createObject($propModelClass);
            if ($propModel->hasAttribute('domain_id')){
                $domainAlias = $relationModel::propertyAlias() . ".";
            }
        }
        if ($relationModel->hasAttribute('domain_id')) {
            $where[$domainAlias . 'domain_id'] = $this->domain_id;
        }
        if (!empty($where)) {
            $query->andWhere($where);
        }
        return $query;
    }
}