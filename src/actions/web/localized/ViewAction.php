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
 * @deprecated
 *
 * Экшон для просмотра локализованных сущностей
 *
 * Class ViewLocalizedAction
 * @package concepture\yii2logic\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class ViewAction extends Base
{
    use LocalizedTrait;

    public $view = 'view';

    public function run($id, $locale = null)
    {
        $modelClass = $this->getModelClass();
        $modelClass::setLocale($locale);
        $model = $this->getModel($id);
        if (!$model){
            throw new NotFoundHttpException();
        }

        return $this->render($this->view, [
            'model' => $model,
        ]);
    }

    /**
     * Возвращает модель для редактирования
     *
     * @param $id
     * @return ActiveRecord
     * @throws ReflectionException
     * @throws ReflectionException
     */
    protected function getModel($id)
    {
        return $this->getService()->findById($id);
    }
}