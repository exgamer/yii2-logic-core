<?php

namespace concepture\yii2logic\validators;

use yii\validators\StringValidator as Base;

/**
 * Валидатор строк
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class StringValidator extends Base
{
    /**
     * @var bool очистка html тегов
     */
    public $strip_tags = false;
    /**
     * @var string
     */
    public $allowable_tags = null;
    /**
     * @var bool
     */
    public $trim = true;
    /**
     * @var bool
     */
    public $lower = true;

    /**
     * @inheritDoc
     */
    public function validateAttribute($model, $attribute)
    {
        $originalValue = null;
        if($this->trim) {
            $model->{$attribute} = trim($model->{$attribute});
        }

        # нижний регистр только для поисковых классов
        if($this->lower && strpos(get_class($model), 'Search') !== false) {
            $model->{$attribute} = mb_strtolower($model->{$attribute});
        }

        if($this->strip_tags) {
            $originalValue = $model->{$attribute};
            $model->{$attribute} = strip_tags($model->{$attribute}, $this->allowable_tags);
        }

        $result = parent::validateAttribute($model, $attribute);
        if(null !== $originalValue) {
            $model->{$attribute} = $originalValue;
        }

        return $result;
    }
}