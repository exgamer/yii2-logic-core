<?php

namespace concepture\yii2logic\enum;

use Yii;

/**
 * Основные базовые полномочия
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class PermissionEnum extends Enum
{
    const ADMIN = "ADMIN";
    const EDITOR = "EDITOR";
    const READER = "READER";
    /**
     * Это полномочие имеет доступ только к свом записям
     * _STAFF
     */
    const STAFF = "STAFF";
    /**
     * Это полномочие имеет доступ на редактирование к указанным доменам
     */
    const DOMAINEDITOR = "DOMAINEDITOR";
    /**
     * Это полномочие имеет доступ на чтение к указанным доменам
     */
    const DOMAIN = "DOMAIN";
}