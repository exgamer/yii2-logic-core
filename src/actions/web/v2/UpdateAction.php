<?php

namespace concepture\yii2logic\actions\web\v2;

use concepture\yii2logic\actors\actions\UpdateActionActor;
use Yii;
use concepture\yii2logic\actions\Action;
use kamaelkz\yii2admin\v1\helpers\RequestHelper;
use ReflectionException;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use concepture\yii2logic\enum\ScenarioEnum;

/**
 * Экшен для обновления сущности
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class UpdateAction extends Action
{
    /**
     * @var string
     */
    public $view = 'update';
    /**
     * @var string
     */
    public $redirect = 'index';
    /**
     * @var string
     */
    public $serviceMethod = 'update';
    /**
     * @var string
     */
    public $scenario = ScenarioEnum::UPDATE;
    /**
     * @var null|\Closure
     */
    public $originModelNotFoundCallback = null;

    /**
     * @param $id
     *
     * @return mixed
     * @throws ReflectionException
     */
    public function run($id)
    {
        $actor = Yii::createObject([
            'class' => UpdateActionActor::class,
            'id' => $id,
            'view' => $this->view,
            'redirect' => $this->redirect,
            'scenario' => $this->scenario,
            'controller' => $this->controller,
            'service' => $this->getService(),
            'serviceMethod' => $this->serviceMethod,
        ]);

        return $actor->run();
    }
}