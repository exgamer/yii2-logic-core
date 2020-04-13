<?php

namespace concepture\yii2logic\db;

use Yii;
use concepture\yii2logic\enum\IsDeletedEnum;
use concepture\yii2logic\enum\StatusEnum;
use concepture\yii2logic\helpers\ClassHelper;
use concepture\yii2logic\models\traits\HasLocalizationTrait;
use concepture\yii2logic\models\traits\v2\property\HasDomainPropertyTrait;
use concepture\yii2logic\models\traits\v2\property\HasLocalePropertyTrait;
use yii\db\ActiveQuery as Base;

class ActiveQuery extends Base
{
    /**
     * только активные
     */
    public function active()
    {
        $model = Yii::createObject($this->modelClass);
        $alias = "";
        $traits = ClassHelper::getTraits($model);
        if (in_array(HasDomainPropertyTrait::class, $traits) ||
            in_array(HasLocalePropertyTrait::class, $traits)){
            $propModelClass = $model::getPropertyModelClass();
            $propModel = Yii::createObject($propModelClass);
            if ($propModel->hasAttribute('status')){
                $alias = $model::propertyAlias() . ".";
            }
        }

        if (in_array(HasLocalizationTrait::class, $traits)){
            $propModelClass = $model::getLocalizationModelClass();
            $propModel = Yii::createObject($propModelClass);
            if ($propModel->hasAttribute('status')){
                $alias = $model::localizationAlias() . ".";
            }
        }

        if ($model->hasAttribute('status')) {
            $this->andWhere([$alias . 'status' => StatusEnum::ACTIVE]);
        }
    }

    /**
     * только неудаленные
     */
    public function notDeleted()
    {
        $model = Yii::createObject($this->modelClass);
        $alias = "";
        $traits = ClassHelper::getTraits($model);
        if (in_array(HasDomainPropertyTrait::class, $traits) ||
            in_array(HasLocalePropertyTrait::class, $traits)){
            $propModelClass = $model::getPropertyModelClass();
            $propModel = Yii::createObject($propModelClass);
            if ($propModel->hasAttribute('is_deleted')){
                $alias = $model::propertyAlias() . ".";
            }
        }

        if (in_array(HasLocalizationTrait::class, $traits)){
            $propModelClass = $model::getLocalizationModelClass();
            $propModel = Yii::createObject($propModelClass);
            if ($propModel->hasAttribute('is_deleted')){
                $alias = $model::localizationAlias() . ".";
            }
        }

        if ($model->hasAttribute('is_deleted')) {
            $this->andWhere([$alias . 'is_deleted' => IsDeletedEnum::NOT_DELETED]);
        }
    }
}