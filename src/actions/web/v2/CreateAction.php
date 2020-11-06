<?php

namespace concepture\yii2logic\actions\web\v2;

use concepture\yii2logic\actors\actions\CreateActionActor;
use ReflectionException;
use Yii;
use concepture\yii2logic\actions\Action;
use kamaelkz\yii2admin\v1\helpers\RequestHelper;
use concepture\yii2logic\enum\ScenarioEnum;

/**
 * Экшен для создания сущности
 *
 * Class CreateAction
 * @package cconcepture\yii2logic\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class CreateAction extends Action
{
    /**
     * @var string
     */
    public $view = 'create';
    /**
     * @var string
     */
    public $redirect = 'index';
    /**
     * @var string
     */
    public $serviceMethod = 'create';
    /**
     * @var string
     */
    public $scenario = ScenarioEnum::INSERT;

    /**
     * @return staing HTML
     * @throws ReflectionException
     */
    public function run()
    {
        $actor = Yii::createObject([
            'class' => CreateActionActor::class,
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