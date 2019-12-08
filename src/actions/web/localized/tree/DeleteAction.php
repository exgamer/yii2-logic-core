<?php
namespace concepture\yii2logic\actions\web\localized\tree;

use concepture\yii2logic\actions\traits\LocalizedTrait;
use yii\db\ActiveRecord;
use ReflectionException;
use concepture\yii2logic\actions\Action;

/**
 * Class DeleteAction
 * @package concepture\yii2logic\actions\web\localized
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class DeleteAction extends Action
{
    use LocalizedTrait;

    public $redirect = 'index';
    public $serviceMethod = 'delete';

    public function run($id, $locale = null, $parent_id = null)
    {
        $model = $this->getModel($id);
        if (!$model){
            throw new NotFoundHttpException();
        }
        $this->getService()->{$this->serviceMethod}($model);

        return $this->redirect([$this->redirect, 'locale' => $this->getConvertedLocale($locale), 'parent_id' => $parent_id]);
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
        $originModelClass::setLocale($this->getLocale());
        $originModelClass::$by_locale_hard_search = false;

        return $this->getService()->findById($id);
    }
}