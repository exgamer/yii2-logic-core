<?php

namespace concepture\yii2logic\actions\web\v2\domain;

use concepture\yii2logic\actors\actions\domain\CreateActionActor;
use Yii;
use concepture\yii2logic\actions\Action;
use kamaelkz\yii2admin\v1\helpers\RequestHelper;
use concepture\yii2logic\enum\ScenarioEnum;

/**
 * Экшен для создания сущности с доменом
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
     * @deprecated
     * @var bool
     */
    public $domainByLocale = false;

    /**
     * @param integer $domain_id
     * @param integer $edited_domain_id
     *
     * @param null $locale_id
     * @return string HTML
     * @throws \ReflectionException
     */
    public function run($domain_id = null, $edited_domain_id = null, $locale_id = null)
    {
        $actor = Yii::createObject([
            'class' => CreateActionActor::class,
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