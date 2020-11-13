<?php
namespace concepture\yii2logic\actors\actions;

use concepture\yii2logic\actors\Actor;
use concepture\yii2logic\controllers\web\Controller;
use concepture\yii2logic\services\Service;
use yii\base\Component;

/**
 * Class ActionActor
 * @package concepture\yii2logic\actors
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class ActionActor extends Actor
{
    public $controller;
    public $service;
    public $serviceMethod;
    public $viewParams = [];

    /**
     * @return array
     */
    public function getViewParams(): array
    {
        return $this->viewParams;
    }

    /**
     * @param $key
     * @param $value
     */
    public function setViewParam($key, $value)
    {
        $this->viewParams[$key] = $value;
    }

    /**
     * @return string
     */
    public function getServiceMethod(): string
    {
        return $this->serviceMethod;
    }

    /**
     * @return Service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @return Controller
     */
    public function getController()
    {
        return $this->controller;
    }
}