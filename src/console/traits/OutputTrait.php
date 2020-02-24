<?php

namespace concepture\yii2logic\console\traits;

use yii\helpers\Console;

/**
 *
 * Trait OutputTrait
 * @package concepture\yii2logic\traits
 */
trait OutputTrait
{
    public static function getColors()
    {
        return array(
            'black' => '0;30', 'dark_gray' => '1;30', 'blue' => '0;34',
            'light_blue' => '1;34', 'green' => '0;32', 'light_green' => '1;32',
            'cyan' => '0;36', 'light_cyan' => '1;36', 'red' => '0;31',
            'light_red' => '1;31', 'purple' => '0;35', 'light_purple' => '1;35',
            'brown' => '0;33', 'yellow' => '1;33', 'light_gray' => '0;37',
            'white' => '1;37',
        );
    }

    /**
     * Успешнноое сообщение с переводом строки
     *
     * @param string $text
     * @param string $color
     * @return string
     */
    public static function outputSuccess($text , $color = 'green')
    {
        $color = isset(static::getColors()[$color]) ? static::getColors()[$color] : \yii\helpers\Console::FG_GREEN;

        echo static::aFormat($text . PHP_EOL,  $color);
    }

    /**
     * Сбой с переводом строки
     *
     * @param string $text
     * @param string $color
     * @return string
     */
    public static function outputDone($text, $color = 'red')
    {
        $color = isset(static::getColors()[$color]) ? static::getColors()[$color] : \yii\helpers\Console::FG_RED;

        echo static::aFormat($text . PHP_EOL,  $color);
    }

    public static function aFormat($string)
    {
        if (static::isColorEnabled()) {
            $args = func_get_args();
            array_shift($args);
            $string = Console::ansiFormat($string, $args);
        }

        return $string;
    }

    public static function isColorEnabled($stream = \STDOUT)
    {
        return Console::streamSupportsAnsiColors($stream);
    }
}