<?php

namespace concepture\yii2logic\services\events\modify;

use yii\base\Event;

/**
 * Событие после любой модификацией данных
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class AfterModifyEvent extends Event
{
    public $form;
    public $model;
    public $is_new_record;
    public $modifyData;
}