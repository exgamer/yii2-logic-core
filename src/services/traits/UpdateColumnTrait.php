<?php

namespace concepture\yii2logic\services\traits;

use yii\base\Event;
use yii\base\InvalidConfigException;
use concepture\yii2logic\models\ActiveRecord;

/**
 * Методы сервиса для модифиации одной колонки
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
trait UpdateColumnTrait
{
    /**
     * @param ActiveRecord $model
     * @param string $column
     * @param mixed $value
     */
    public function updateColumn(ActiveRecord $model, $column, $value)
    {
        $event = new Event();
        $event->data = [
            'model' => $model,
            'column' => $column,
            'value' => $value,
        ];
        $this->trigger(static::EVENT_BEFORE_COLUMN_UPDATE, $event);
        $this->beforeUpdateColumn($model, $column, $value);
        $class = get_class($model);
        if(! isset($model->{$column})) {
            throw new InvalidConfigException("Column `{$column}` in class {$class} not found.");;
        }

        $model->{$column} = $value;
        $model->save(false);
        $this->trigger(static::EVENT_AFTER_COLUMN_UPDATE, $event);
    }

    /**
     * @param ActiveRecord $model
     * @param string $column
     * @param mixed $value
     */
    public function beforeUpdateColumn(ActiveRecord $model, $column, $value) {}
}

