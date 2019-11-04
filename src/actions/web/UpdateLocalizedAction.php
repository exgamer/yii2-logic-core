<?php
namespace concepture\yii2logic\actions\web;

use ReflectionException;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use Yii;
use yii\db\Exception;
use concepture\yii2logic\actions\traits\LocalizedTrait;

/**
 * Экшон для обновления сущностей с локализациями
 * Class UpdateLocalizedAction
 * @package concepture\yii2logic\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class UpdateLocalizedAction extends UpdateAction
{
    use LocalizedTrait;

    /**
     * Устанавливаем модели локаль и загружаем локализованные атрибуты
     * @param $model
     * @param $originModel
     */
    protected function processModel($model, $originModel)
    {
        $model->locale = $this->getLocale();
        $model->setAttributes($originModel->attributes, false);
        $model->setAttributes($originModel->getLocalized(null, true), false);
    }

    protected function extendRedirectParams(&$redirectParams)
    {
        $redirectParams['locale'] = $this->getLocale();
    }

    /**
     * Возвращает локализованную сущность с учетом локали
     *
     * @param $id
     * @return ActiveRecord
     * @throws ReflectionException
     */
    protected function getModel($id)
    {
        $originModelClass = $this->getService()->getRelatedModelClass();
        $originModelClass::$current_locale = $this->getLocale();

        return $this->getService()->findById($id);
    }
}