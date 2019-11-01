<?php
namespace concepture\yii2logic\actions;

use concepture\yii2logic\controllers\web\Controller;
use concepture\yii2logic\services\Service;
use ReflectionException;
use Yii;
use yii\base\Action as Base;
use yii\db\Exception;
use yii\web\ServerErrorHttpException;

/**
 * Базовый экшен
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class Action extends Base
{
    public $view;
    public $redirect;
    public $serviceMethod;

    protected function getArguments()
    {
        $reflector = new \ReflectionClass(__CLASS__);
        $parameters = $reflector->getMethod("run")->getParameters();
        $args = array();
        foreach($parameters as $parameter)
        {
            $args[$parameter->name] = ${$parameter->name};
        }

        return $args;
    }

    /**
     * редирект
     *
     * @param $url
     * @param int $statusCode
     * @return mixed
     * @throws \Exception
     */
    public function redirect($url, $statusCode = 302)
    {
        return $this->getController()->redirect($url, $statusCode);
    }

    /**
     * рендер вьюшки
     *
     * @param $view
     * @param array $params
     * @return mixed
     * @throws \Exception
     */
    public function render($view, $params = [])
    {

        return $this->getController()->render($view, $params);
    }

    /**
     * Возвращает контроллер
     *
     * @return Controller
     * @throws \Exception
     */
    protected function getController()
    {
        if (!$this->controller instanceof Controller){
            throw new \Exception("Controller must be an instance of ". Controller::class);
        }

        return $this->controller;
    }

    /**
     * Возвращает сервис
     *
     * @return Service
     * @throws ReflectionException
     */
    protected function getService()
    {
        return $this->getController()->getService();
    }

    /**
     * Возвращает класс формы сущности
     *
     * @return mixed
     * @throws ReflectionException
     */
    protected function getForm()
    {
        $formClass = $this->getController()->getFormClass();

        return new $formClass();
    }

    /**
     * Возвращает класс модели сущности
     *
     * @return string
     * @throws ReflectionException
     */
    protected function getModelClass()
    {

        return $this->getService()->getRelatedModelClass();
    }

    /**
     * Возвращает класс search модели сущности
     *
     * @return string
     * @throws ReflectionException
     */
    protected function getSearchClass()
    {

        return $this->getService()->getRelatedSearchModelClass();
    }
}