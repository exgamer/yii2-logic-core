<?php

namespace concepture\yii2logic\services\events\modify;

use yii\base\Event;

/**
 * Событие перед сменой статуса
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class BeforeChangeStatusEvent extends Event
{
    public $model;
    public $status;
}