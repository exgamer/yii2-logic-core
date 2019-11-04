<?php
namespace concepture\yii2logic\actions\web;

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
        $originModelClass::$current_locale = $this->getLocale();
        $originModelClass::$by_locale_hard_search = false;

        return $this->getService()->findById($id);
    }
}