<?php

namespace concepture\yii2logic\services\events\modify;

use yii\base\Event;

/**
 * Событие после смены статуса
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class AfterChangeStatusEvent extends Event
{
    public $model;
    public $status;
}