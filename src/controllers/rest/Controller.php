<?php
namespace concepture\yii2logic\controllers\rest;

use concepture\yii2logic\helpers\ClassHelper;
use concepture\yii2logic\services\Service;
use Yii;
use yii\filters\Cors;
use yii\rest\Controller as Base;

/**
 * Class Controller
 * @package concepture\yii2logic\controllers\rest
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class Controller extends Base
{
    public function actions() {

        $actions = parent::actions();
        unset($actions['create']);
        unset($actions['update']);
        unset($actions['delete']);
        unset($actions['index']);
        unset($actions['view']);
        $actions['index'] = [
            'class' => 'concepture\yii2logic\actions\rest\IndexAction',
        ];
        /**
         * @TODO реализовать остальные рест экшоны
         */
        $actions['view'] = [
            'class' => 'concepture\yii2logic\actions\rest\ViewAction',
        ];
        $actions['create'] = [
            'class' => 'concepture\yii2logic\actions\rest\CreateAction',
        ];
        $actions['update'] = [
            'class' => 'concepture\yii2logic\actions\rest\UpdateAction',
        ];
        $actions['delete'] = [
            'class' => 'concepture\yii2logic\actions\rest\DeleteAction',
        ];

        return $actions;
    }

    /**
     * @inheritdoc
     */
    protected function verbs()
    {
        return [
            'index' => ['GET', 'HEAD'],
            'view' => ['GET', 'HEAD'],
            'create' => ['POST'],
            'update' => ['PUT', 'PATCH'],
            'delete' => ['DELETE'],
        ];
    }

    public function behaviors()
    {
        $b = parent::behaviors();
        $b['corsFilter'] = [
            'class' => Cors::class,
        ];

        return $b;
    }

    public function init()
    {
        parent::init();
        Yii::$app->user->enableSession = false;
    }

    /**
     * Возвращает класс формы сущности
     *
     * @return mixed
     * @throws ReflectionException
     */
    public function getForm()
    {
        $formClass = $this->getFormClass();

        return Yii::createObject($formClass);
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
}