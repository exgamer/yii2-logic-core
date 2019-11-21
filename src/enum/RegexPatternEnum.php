<?php

namespace concepture\yii2logic\enum;

/**
 * Перечисление регулярных выражений
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class RegexPatternEnum extends Enum
{
    # стандартный паттерн логов nginx
    const NGINX_LOG_STANDART = '/^([^ ]*) - ([^ ]*) \[(.*)\] \"([^ ]*) ([^ ]*) ([^ ]*)\" (-|[0-9]*) (-|[0-9]*) \"(.+?|-)\" ([^ ]*|-) ([^ ]*|-) ([^ ]*|-) \"(.+?|-)\" \"(.+?|-)\" \"(.+?|-)\"$/';
    # паттерн пути до файла статики legalcdn
    const CDN_STATIC_PATH = '/^\/[0-9]{2}\/[0-9]{2}\/[0-9a-z]+_[a-z0-9]+.[a-z]+$/';
    # абсолютная ссылка
    const ABSOLUTE_URL = '/(?:http|https):\/\/((?:[\w-]+)(?:\.[\w-]+)+)(?:[\w.,@?^=%&amp;:\/~+#-]*[\w@?^=%&amp;\/~+#-])?/';
    /**
     * Только цифры, латинские маленькие буквы и подчеркивания
     */
    const DIGITS_UNDERSCORE_LETTERS = '/^[a-z]\w*$/i';
}