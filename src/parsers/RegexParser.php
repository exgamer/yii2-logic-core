<?php

namespace concepture\yii2logic\parsers;

/**
 * Парсер регулярных выражений
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class RegexParser
{
    /**
     * Разбирает строку $data по паттерну
     *
     * @param string $data
     * @param null|string $type (patternTypes)
     * @return bool|array
     * @throws \Exception
     */
    public function parse(string $data, $pattern)
    {
        preg_match($pattern, $data, $matches);
        if(! $matches) {
            return false;
        }

        if(count($matches) == 1 && isset($matches[0])) {
            return $matches[0];
        }

        return $matches;
    }
}