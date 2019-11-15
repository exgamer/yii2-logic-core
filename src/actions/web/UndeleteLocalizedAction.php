<?php
namespace concepture\yii2logic\actions\web;

use concepture\yii2logic\actions\traits\LocalizedTrait;
use yii\db\ActiveRecord;
use ReflectionException;

/**
 * Экшен для восстановления нефизически удаленной сущности с локализацией
 *
 * Class UndeleteLocalizedAction
 * @package cconcepture\yii2logic\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class UndeleteLocalizedAction extends UndeleteAction
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