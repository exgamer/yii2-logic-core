<?php

namespace concepture\yii2logic\controllers\web;

use ReflectionException;
use Yii;
use yii\web\Controller as Base;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;
use yii\web\Response;
use concepture\yii2logic\helpers\ClassHelper;
use concepture\yii2logic\services\Service;
# обычные действия
use concepture\yii2logic\actions\web\v2\CreateAction;
use concepture\yii2logic\actions\web\v2\DeleteAction;
use concepture\yii2logic\actions\web\v2\IndexAction;
use concepture\yii2logic\actions\web\v2\UpdateAction;
use concepture\yii2logic\actions\web\v2\ViewAction;
# локализованные действия
use concepture\yii2logic\actions\web\localized\CreateAction as LocalizedCreateAction;
use concepture\yii2logic\actions\web\localized\UpdateAction as LocalizedUpdateAction;
use concepture\yii2logic\actions\web\localized\DeleteAction as LocalizedDeleteAction;
use concepture\yii2logic\actions\web\localized\IndexAction  as LocalizedIndexAction;
use concepture\yii2logic\actions\web\localized\ViewAction as LocalizedViewAction;
# деревья
use concepture\yii2logic\actions\web\tree\CreateAction as TreeCreateAction;
use concepture\yii2logic\actions\web\tree\UpdateAction as TreeUpdateAction;
use concepture\yii2logic\actions\web\tree\DeleteAction as TreeDeleteAction;
use concepture\yii2logic\actions\web\tree\IndexAction as TreeIndexAction;
use concepture\yii2logic\actions\web\ViewAction as TreeViewAction;
# локализованные деревья
use concepture\yii2logic\actions\web\localized\tree\CreateAction as TreeLocalizedCreateAction;
use concepture\yii2logic\actions\web\localized\tree\UpdateAction as TreeLocalizedUpdateAction;
use concepture\yii2logic\actions\web\localized\tree\DeleteAction as TreeLocalizedDeleteAction;
use concepture\yii2logic\actions\web\localized\tree\IndexAction as TreeLocalizedIndexAction;
use concepture\yii2logic\actions\web\localized\ViewAction as TreeLocalizedViewAction;

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
     * @var boolean
     */
    public $localized = false;

    /**
     * @var boolean
     */
    public $tree = false;

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

    /**
     * @inheritDoc
     */
    public function actions()
    {
        if(! $this->localized && ! $this->tree) {
            return $this->getDefaultActions();
        }

        if($this->localized && ! $this->tree) {
            return $this->getLocalizedActions();
        }

        if(! $this->localized && $this->tree) {
            return $this->getTreeActions();
        }

        if($this->localized && $this->tree) {
            return $this->getTrreLocalizedActions();
        }

        throw new \Exception('Default actions is not found.');
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

    /**
     * Возвращает ответ в формате JSON
     *
     * @param array $data
     * @return mixed
     */
    public function responseJson(array $payload)
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return $payload;
    }

    /**
     * @deprecated
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

    /**
     * Подключаемые действия
     *
     * @return array
     */
    protected function getDefaultActions()
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
     * Подключаемые действия с локалицацией
     *
     * @return array
     */
    protected function getLocalizedActions()
    {
        return [
            'index' => LocalizedIndexAction::class,
            'create' => LocalizedCreateAction::class,
            'update' => LocalizedUpdateAction::class,
            'view' => LocalizedViewAction::class,
            'delete' => LocalizedDeleteAction::class,
        ];
    }

    /**
     * Подключаемые действия деревьев
     *
     * @return array
     */
    protected function getTreeActions()
    {
        return [
            'index' => TreeIndexAction::class,
            'create' => TreeCreateAction::class,
            'update' => TreeUpdateAction::class,
            'view' => TreeViewAction::class,
            'delete' => TreeDeleteAction::class,
        ];
    }

    /**
     * Подключаемые действия деревьев с локалицацией
     *
     * @return array
     */
    protected function getTrreLocalizedActions()
    {
        return [
            'index' => TreeLocalizedIndexAction::class,
            'create' => TreeLocalizedCreateAction::class,
            'update' => TreeLocalizedUpdateAction::class,
            'view' => TreeLocalizedViewAction::class,
            'delete' => TreeLocalizedDeleteAction::class,
        ];
    }
}
