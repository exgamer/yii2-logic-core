<?php
namespace concepture\yii2logic\services;

use concepture\yii2logic\services\traits\LinkTrait;

/**
 * базовый сервис для линкушек
 *
 * Class LinkService
 * @package concepture\yii2logic\services
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class LinkService extends Service
{
    use LinkTrait;
}
