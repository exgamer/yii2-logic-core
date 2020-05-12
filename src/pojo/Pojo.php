<?php

namespace concepture\yii2logic\pojo;

use concepture\yii2logic\forms\Model;
use concepture\yii2logic\helpers\StringHelper;

/**
 * Базовый класс для проверки данных
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
abstract class Pojo extends Model
{
    public $isNewRecord = true;

    /**
     * @inheritDoc
     */
    public function load($data, $formName = null)
    {
        if(
            is_string($data)
            && StringHelper::isJson($data)
        ) {
            $data = StringHelper::jsonToArray($data);
        }

        return parent::load($data, $formName);
    }
}