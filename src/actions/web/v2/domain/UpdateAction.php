<?php

namespace concepture\yii2logic\actions\web\v2\domain;

use concepture\yii2logic\actors\actions\domain\UpdateActionActor;
use Yii;
use concepture\yii2logic\actions\Action;
use kamaelkz\yii2admin\v1\helpers\RequestHelper;
use ReflectionException;
use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use concepture\yii2logic\enum\ScenarioEnum;
use yii\db\ActiveQuery;

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
     * @deprecated
     * @var null|\Closure
     */
    public $originModelNotFoundCallback = null;

    /**
     * @param integer $id
     * @param integer $domain_id
     * @param integer $edited_domain_id
     *
     * @param null $locale_id
     * @return string HTML
     * @throws ReflectionException
     */
    public function run($id, $domain_id, $edited_domain_id = null, $locale_id = null)
    {
        $actor = Yii::createObject([
            'class' => UpdateActionActor::class,
            'id' => $id,
            'domain_id' => $domain_id,
            'edited_domain_id' => $edited_domain_id,
            'locale_id' => $locale_id,
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