<?php

namespace concepture\yii2logic\enum;

/**
 * Справочник статусов HTTP протокола
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class HttpCodeEnum extends Enum
{
    const OK = 200;
    const CREATED = 201;
    const BAD_REQUEST = 400;
    const UNAUTHORIZED = 401;
    const FORBIDDEN = 403;
    const NOT_FOUND = 404;
    const METHOD_NOT_ALLOWED = 405;
    const UNPOCESSABLE_ENTITY = 422;
}