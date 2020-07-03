<?php

namespace concepture\yii2logic\controllers\console;

use concepture\yii2logic\console\traits\OutputTrait;

/**
 * Базовый класс для конcольных команд
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
abstract class ConsoleCommand extends \yii\console\Controller
{
    use OutputTrait;
}

