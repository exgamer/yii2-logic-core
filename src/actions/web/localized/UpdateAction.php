<?php
namespace concepture\yii2logic\actions\web\localized;

use ReflectionException;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use Yii;
use yii\db\Exception;
use concepture\yii2logic\actions\traits\LocalizedTrait;
use concepture\yii2logic\actions\web\UpdateAction as Base;

/**
 * Экшон для обновления сущностей с локализациями
 * Class UpdateLocalizedAction
 * @package concepture\yii2logic\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class UpdateAction extends Base
{
    use LocalizedTrait;

    /**
     * Устанавливаем модели локаль и загружаем локализованные атрибуты
     * @param $model
     * @param $originModel
     */
    protected function processModel($model, $originModel)
    {
        $model->locale = $this->getConvertedLocale();
        $model->setAttributes($originModel->attributes, false);
        $model->setAttributes($originModel->getLocalized(null, true), false);
    }

    protected function extendRedirectParams(&$redirectParams)
    {
        $redirectParams['locale'] = $this->getConvertedLocale();
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