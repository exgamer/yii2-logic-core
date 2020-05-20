<?php
namespace concepture\yii2logic\actions;

use concepture\yii2logic\controllers\web\Controller;
use concepture\yii2logic\services\Service;
use ReflectionException;
use Yii;
use yii\base\Action as Base;
use yii\db\Exception;
use yii\helpers\Url;
use yii\web\ServerErrorHttpException;
use yii\web\BadRequestHttpException;

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
    public $redirectParams = [];
    public $queryParams = [];

    /**
     * Запомнить текущий адрес
     *
     * @throws \Exception
     */
    public function rememberUrl()
    {
        $this->getController()->rememberUrl();
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getReturnUrlKey()
    {
        return $this->getController()->getReturnUrlKey();
    }

    /**
     * мапит параметры запроса в модель
     *
     * @param $model
     */
    protected function setQueryParams($model)
    {
        foreach ($this->queryParams as $param){
            if (! Yii::$app->getRequest()->getQueryParam($param)){
                continue;
            }

            if ($model->hasAttribute($param) || property_exists($model, $param)){
                $model->{$param} = Yii::$app->getRequest()->getQueryParam($param);
            }
        }
    }

    /**
     * Возвращает параметры для редиректа
     *
     * @param $model
     * @return array
     */
    protected function getRedirectParams($model)
    {
        $redirectParams = [$this->redirect];
        foreach ($this->redirectParams as $param){
            if ($model->hasAttribute($param) || property_exists($model, $param)){
                $redirectParams[$param] = $model->{$param};
            }
        }

        return $redirectParams;
    }

    /**
     * Возвращает аргументы переданные в метод run
     *
     * @return array
     * @throws ReflectionException
     */
    protected function getRunArguments()
    {
        return $this->getArguments("run");
    }

    /**
     * Возвращает аргументы перданные в метод
     *
     * @param string $functionName
     * @return array
     * @throws ReflectionException
     */
    protected function getArguments($functionName)
    {
        $reflector = new \ReflectionClass($this);
        $parameters = $reflector->getMethod($functionName)->getParameters();
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
        return $this->controller;
    }

    /**
     * Возвращает сервис
     *
     * @return Service
     * @throws ReflectionException
     * @throws \Exception
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
        return $this->getController()->getForm();
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

    /**
     * @deprecated
     * Метод для определния нужно ли просто перезагрузить форму/вьюшку
     *
     * @param string $method
     * @return bool
     * @throws \Exception
     */
    public function isReload($method = "post")
    {
        return $this->getController()->isReload($method);
    }
}