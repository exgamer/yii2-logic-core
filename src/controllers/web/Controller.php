<?php
namespace concepture\yii2logic\controllers\web;

use concepture\yii2logic\actions\web\CreateAction;
use concepture\yii2logic\actions\web\DeleteAction;
use concepture\yii2logic\actions\web\IndexAction;
use concepture\yii2logic\actions\web\UpdateAction;
use concepture\yii2logic\actions\web\ViewAction;
use Yii;
use yii\web\Controller as Base;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use ReflectionClass;

/**
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
     * @return string
     */
    public function getFormClass()
    {
        return $this->getService()->getRelatedFormClass();
    }

    public function getService()
    {
        $reflection = new ReflectionClass($this);
        $name = $reflection->getShortName();
        $name = str_replace("Controller", "", $name);
        $name = $name."Service";
        $name = lcfirst($name);

        return Yii::$app->{$name};
    }
}
