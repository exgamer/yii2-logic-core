<?php
namespace concepture\yii2logic\services\traits;

use concepture\yii2logic\forms\Form;
use concepture\yii2logic\forms\Model;
use concepture\yii2logic\helpers\ClassHelper;
use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2logic\services\Service;
use ReflectionException;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Command;
use yii\db\Connection;

/**
 * Trait HasDbConnetionTrait
 * @package concepture\yii2logic\services\traits
 */
trait HasDbConnetionTrait
{
    /**
     * @param $sql
     * @param array $params
     * @return Command
     * @throws ReflectionException
     */
    public function createCommand($sql = null, $params = [])
    {
        return $this->getDb()->createCommand($sql, $params);
    }

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

    public function getDbType()
    {
        return $this->getDb()->getDriverName();
    }

    public function isMysql()
    {
        if ($this->getDbType() == 'mysql'){
            return true;
        }

        return false;
    }

    public function isPostgres()
    {
        if ($this->getDbType() == 'pgsql'){
            return true;
        }

        return false;
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

        return trim($modelClass::tableName(), '{}%');
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

    /**
     * Получить новый обьект модели
     *
     * @return ActiveRecord
     * @throws ReflectionException
     */
    public function getRelatedModel()
    {
        $class = $this->getRelatedModelClass();

        return Yii::createObject($class);
    }

    /**
     * Получить новый обьект формы
     *
     * @return Form
     * @throws ReflectionException
     */
    public function getRelatedForm()
    {
        $class = $this->getRelatedFormClass();

        return Yii::createObject($class);
    }

    /**
     * Получить новый обьект серч формы
     *
     * @return ActiveRecord
     * @throws ReflectionException
     */
    public function getRelatedSearchModel()
    {
        $class = $this->getRelatedSearchModelClass();

        return Yii::createObject($class);
    }

    /**
     * @param $model
     * @return Service
     */
    protected function getEntityService($model)
    {
        $serviceName = ClassHelper::getServiceName($model);

        return  Yii::$app->{$serviceName};
    }

    /**
     * @param $tableName
     * @return Service
     */
    protected function getServiceByEntityTable($tableName)
    {
        $serviceName =  ClassHelper::getServiceByEntityTable($tableName);
        if (! Yii::$app->has($serviceName)){
            return null;
        }

        return  Yii::$app->{$serviceName};
    }
}

