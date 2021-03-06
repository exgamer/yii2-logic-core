<?php
namespace concepture\yii2logic\services;

use concepture\yii2logic\services\interfaces\ReadEventInterface;
use concepture\yii2logic\services\interfaces\ModifyEventInterface;
use concepture\yii2logic\services\traits\HasDbConnectionTrait;
use concepture\yii2logic\services\traits\RepositoryTrait;
use concepture\yii2logic\services\traits\SqlModifyTrait;
use concepture\yii2logic\services\traits\SqlReadTrait;
use concepture\yii2logic\traits\StaticDataTrait;
use Yii;
use concepture\yii2logic\services\traits\CacheTrait;
use concepture\yii2logic\services\traits\CopyTrait;
use yii\base\Component;
use yii\db\Command;
use yii\db\Connection;
use concepture\yii2logic\services\traits\ModifyTrait;
use concepture\yii2logic\services\traits\ReadTrait;
use concepture\yii2logic\services\traits\CatalogTrait;
use yii\helpers\Inflector;

/**
 * Базовый класс сервиса для реализации бизнес логики
 *
 * Для реализации бизнес логики с помощью сервиса
 * сущность должна иметь
 * ActiveRecord
 * Form
 * Search
 * Service
 *
 * Class Service
 * @package concepture\yii2logic\services
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class Service extends Component implements ModifyEventInterface, ReadEventInterface
{
    use ModifyTrait;
    use ReadTrait;
    use SqlModifyTrait;
    use SqlReadTrait;
    use CatalogTrait;
    use CopyTrait;
    use CacheTrait;
    use StaticDataTrait;
    use RepositoryTrait;
    use HasDbConnectionTrait;
}