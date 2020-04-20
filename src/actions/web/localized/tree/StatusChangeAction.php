<?php
namespace concepture\yii2logic\actions\web\localized\tree;

use concepture\yii2logic\actions\traits\LocalizedTrait;
use concepture\yii2logic\helpers\AccessHelper;
use yii\db\ActiveRecord;
use ReflectionException;
use concepture\yii2logic\actions\Action;

/**
 * Class StatusChangeAction
 * @package concepture\yii2logic\actions\web\localized\tree
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class StatusChangeAction extends Action
{
    use LocalizedTrait;

    public $redirect = 'index';
    public $serviceMethod = 'statusChange';

    public function run($id, $status, $locale = null, $parent_id = null)
    {
        $model = $this->getModel($id);
        if (!$model){
            throw new NotFoundHttpException();
        }

        if (! AccessHelper::checkAccess($this->id, ['model' => $model])){
            throw new yii\web\ForbiddenHttpException(Yii::t("core", "You are not the owner"));
        }

        $this->getService()->{$this->serviceMethod}($model, $status);

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
//        $originModelClass::disableLocaleHardSearch();

        return $this->getService()->findById($id);
    }
}