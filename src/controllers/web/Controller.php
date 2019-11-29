<?php
namespace concepture\yii2logic\controllers\web;

use concepture\yii2logic\actions\web\CreateAction;
use concepture\yii2logic\actions\web\DeleteAction;
use concepture\yii2logic\actions\web\IndexAction;
use concepture\yii2logic\actions\web\UpdateAction;
use concepture\yii2logic\actions\web\ViewAction;
use concepture\yii2logic\helpers\ClassHelper;
use concepture\yii2logic\services\Service;
use ReflectionException;
use Yii;
use yii\web\Controller as Base;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Базовый веб контроллер
 *
 * Class Controller
 * @package concepture\yii2logic\controllers\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class Controller extends Base
{
    /**
     * @return array
     */
    protected function getAccessRules()
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => $this->getAccessRules()
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actions()
    {
        return [
            'index' => IndexAction::class,
            'create' => CreateAction::class,
            'update' => UpdateAction::class,
            'view' => ViewAction::class,
            'delete' => DeleteAction::class,
        ];
    }

    /**
     * Возвращает класс формы сущности из сервиса
     *
     * @return string
     * @throws ReflectionException
     */
    public function getFormClass()
    {
        return $this->getService()->getRelatedFormClass();
    }

    /**
     * Возвращает сервис сущности
     *
     * @return Service
     */
    public function getService()
    {
        $name = ClassHelper::getServiceName($this, "Controller");

        return Yii::$app->{$name};
    }

    /**
     * Метод для определния нужно ли просто перезагрузить форму/вьюшку
     *
     * @param string $method
     * @return bool
     */
    public function isReload($method = "post")
    {
        $reload = Yii::$app->request->{$method}('reload');
        if ($reload){

            return true;
        }

        return false;
    }
}
