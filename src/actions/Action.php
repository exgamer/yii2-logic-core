<?php
namespace concepture\yii2logic\actions;

use concepture\yii2logic\controllers\web\Controller;
use Yii;
use yii\base\Action as Base;
use yii\db\Exception;
use yii\web\ServerErrorHttpException;

/**
 *
 * @author CitizenZet
 */
abstract class Action extends Base
{
    public $view;
    public $redirect;
    public $serviceMethod;

    public function redirect($url, $statusCode = 302)
    {
        return $this->getController()->redirect($url, $statusCode);
    }

    public function render($view, $params = [])
    {

        return $this->getController()->render($view, $params);
    }

    protected function getController()
    {
        if (!$this->controller instanceof Controller){
            throw new \Exception("Controller must be an instance of ". Controller::class);
        }

        return $this->controller;
    }

    protected function getService()
    {
        return $this->getController()->getService();
    }

    protected function getForm()
    {
        $formClass = $this->getController()->getFormClass();

        return new $formClass();
    }

    protected function getModelClass()
    {

        return $this->getService()->getRelatedModelClass();
    }

    protected function getSearchClass()
    {

        return $this->getService()->getRelatedSearchModelClass();
    }
}