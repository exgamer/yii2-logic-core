<?php

namespace concepture\yii2logic\services;

use concepture\yii2logic\services\traits\PropertyModifyTrait;
use concepture\yii2logic\services\traits\PropertyReadTrait;

/**
 * Базовый сервис для сущностей с пропертями
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
abstract class HasPropertyService extends Service
{
    use PropertyReadTrait,
        PropertyModifyTrait;
}