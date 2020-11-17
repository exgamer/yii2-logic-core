<?php
namespace concepture\yii2logic\actions\web\v2\domain;

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
     * @param $domain_id
     * @return string HTML
     * @throws ReflectionException
     */
    public function run($id, $domain_id)
    {
        $model = $this->getModel($id);
        if (!$model){
            throw new NotFoundHttpException();
        }

        return $this->render($this->view, [
            'model' => $model,
            'domain_id' => $domain_id,
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