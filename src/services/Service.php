<?php
namespace concepture\yii2logic\services;


use concepture\yii2logic\helpers\ClassHelper;
use concepture\yii2logic\services\traits\CacheTrait;
use concepture\yii2logic\services\traits\CopyTrait;
use ReflectionException;
use yii\base\Component;
use yii\db\Connection;
use concepture\yii2logic\services\traits\ModifyTrait;
use concepture\yii2logic\services\traits\ReadTrait;
use concepture\yii2logic\services\traits\CatalogTrait;

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
abstract class Service extends Component
{
    use ModifyTrait;
    use ReadTrait;
    use CatalogTrait;
    use CopyTrait;
    use CacheTrait;

    /**
     * Возвращает соединение к БД
     *
     * @return Connection
     * @throws ReflectionException
     */
    public function getDb()
    {
        $modelClass = $this->getRelatedModelClass();

        return $modelClass::getDb();
    }

    /**
     * Возвращает имя таблицы
     *
     * @return string
     * @throws ReflectionException
     */
    public function getTableName()
    {
        $modelClass = $this->getRelatedModelClass();

        return $modelClass::tableName();
    }

    /**
     * Получить класс связанной модели
     *
     * @return string
     * @throws ReflectionException
     */
    public function getRelatedModelClass()
    {
        return ClassHelper::getRelatedClass($this, ["Service" => ""], ["services" => "models"]);
    }

    /**
     * Получить класс связанной формы
     *
     * @return string
     * @throws ReflectionException
     */
    public function getRelatedFormClass()
    {
        return ClassHelper::getRelatedClass($this, ["Service" => "Form"], ["services" => "forms"]);
    }

    /**
     * Получить класс связанной search модели
     * 
     * @return string
     * @throws ReflectionException
     */
    public function getRelatedSearchModelClass()
    {
        return ClassHelper::getRelatedClass($this, ["Service" => "Search"], ["services" => "search"]);
    }
}