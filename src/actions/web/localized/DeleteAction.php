<?php
namespace concepture\yii2logic\actions\web\localized;

use concepture\yii2logic\actions\traits\LocalizedTrait;
use yii\db\ActiveRecord;
use ReflectionException;
use concepture\yii2logic\actions\Action;

/**
 * Экшен для удаления сущности с локализацией
 *
 * Class DeleteLocalizedAction
 * @package cconcepture\yii2logic\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class DeleteAction extends Action
{
    use LocalizedTrait;

    public $redirect = 'index';
    public $serviceMethod = 'delete';

    public function run($id, $locale = null)
    {
        $model = $this->getModel($id);
        if (!$model){
            throw new NotFoundHttpException();
        }
        $this->getService()->{$this->serviceMethod}($model);

        return $this->redirect([$this->redirect, 'locale' => $this->getConvertedLocale($locale)]);
    }
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