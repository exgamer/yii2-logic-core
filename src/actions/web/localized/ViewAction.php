<?php
namespace concepture\yii2logic\actions\web\localized;

use ReflectionException;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use Yii;
use yii\db\Exception;
use concepture\yii2logic\actions\traits\LocalizedTrait;
use concepture\yii2logic\actions\web\ViewAction as Base;

/**
 * Экшон для просмотра локализованных сущностей
 *
 * Class ViewLocalizedAction
 * @package concepture\yii2logic\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class ViewAction extends Base
{
    use LocalizedTrait;

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
        $originModelClass::$current_locale = $this->getConvertedLocale();

        return $this->getService()->findById($id);
    }
}