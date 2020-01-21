<?php

namespace concepture\yii2logic\services\interfaces;

use concepture\yii2logic\models\ActiveRecord;

/**
 * Интерфейс модификации одной колонки
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
interface UpdateColumnInterface
{
    const EVENT_BEFORE_COLUMN_UPDATE = 'beforeColumnUpdate';
    const EVENT_AFTER_COLUMN_UPDATE = 'afterColumnUpdate';

    /**
     * @param ActiveRecord $model
     * @param string $column
     * @param mixed $value
     * @return boolean
     */
    public function beforeUpdateColumn(ActiveRecord $model, $column, $value);

    /**
     * @param ActiveRecord $model
     * @param string $column
     * @param mixed $value
     * @return boolean
     */
    public function updateColumn(ActiveRecord $model, $column, $value);
}