<?php
namespace concepture\yii2logic\actions\web;

use concepture\yii2logic\actions\traits\LocalizedTrait;
use yii\db\ActiveRecord;
use ReflectionException;

/**
 * Экшен для удаления сущности с локализацией
 *
 * Class DeleteLocalizedAction
 * @package cconcepture\yii2logic\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class DeleteLocalizedAction extends DeleteAction
{
    use LocalizedTrait;

    /**
     * Возвращает локализованную сущность с учетом локали если текущей локализации нет атрибуты будут пустые
     *
     *
     * @param $id
     * @return ActiveRecord
     * @throws ReflectionException
     */
    protected function getModel($id)
    {
        $originModelClass = $this->getService()->getRelatedModelClass();
        $originModelClass::$current_locale = $this->getConvertedLocale();
        $originModelClass::$by_locale_hard_search = false;

        return $this->getService()->findById($id);
    }
}