<?php
namespace concepture\yii2logic\actions\web\v2;

use concepture\yii2logic\actions\Action;
use ReflectionException;
use yii\web\NotFoundHttpException;
use yii\db\ActiveRecord;

/**
 * Экшен для просмотра сущности
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class ViewAction extends Action
{
    /**
     * @var string
     */
    public $view = 'view';

    /**
     * @param $id
     *
     * @return string HTML
     */
    public function run($id)
    {
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