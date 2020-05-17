<?php
namespace concepture\yii2logic\services\repositories;

use concepture\yii2logic\services\Service;
use yii\base\Component;
use yii\db\Exception;

/**
 * Базовый класс репозитория
 *
 * Class Repository
 * @package concepture\yii2logic\services
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class Repository extends Component
{
    /**
     * @var Service
     */
    public $service;

    /**
     * @return Service
     */
    public function getService()
    {
        return $this->service;
    }

    /**
     * @param $sql
     * @param $params
     * @param int $fetchMode the result fetch mode. Please refer to [PHP manual](https://secure.php.net/manual/en/function.PDOStatement-setFetchMode.php)
     * for valid fetch modes. If this parameter is null, the value set in [[fetchMode]] will be used.
     * @return array
     */
    public function queryAll($sql, $params= [], $fetchMode = null)
    {
        return $this->getService()->queryAll($sql, $params, $fetchMode);
    }

    /**
     * @param $sql
     * @param $params
     * @param int $fetchMode the result fetch mode. Please refer to [PHP manual](https://secure.php.net/manual/en/function.PDOStatement-setFetchMode.php)
     * for valid fetch modes. If this parameter is null, the value set in [[fetchMode]] will be used.
     * @return array
     */
    public function queryOne($sql, $params= [], $fetchMode = null)
    {
        return $this->getService()->queryOne($sql, $params, $fetchMode);
    }

    /**
     * @param $sql
     * @param $params
     * @return boolean
     * @throws Exception
     */
    public function execute($sql, $params= [])
    {
        return $this->getService()->execute($sql, $params);
    }

    /**
     * @param $sql
     * @param array $params
     * @return Command
     * @throws ReflectionException
     */
    public function createCommand($sql = null, $params = [])
    {
        return $this->getService()->createCommand($sql, $params);
    }

    public function getDbType()
    {
        return $this->getService()->getDbType();
    }

    public function isMysql()
    {
        return $this->getService()->isMysql();
    }

    public function isPostgres()
    {
        return $this->getService()->isPostgres();
    }

    /**
     * Возвращает имя таблицы
     *
     * @return string
     * @throws ReflectionException
     */
    public function getTableName()
    {
        return $this->getService()->getTableName();
    }

    /**
     * Получить класс связанной модели
     *
     * @return string
     * @throws ReflectionException
     */
    public function getRelatedModelClass()
    {
        return $this->getService()->getRelatedModelClass();
    }

    /**
     * Получить класс связанной формы
     *
     * @return string
     * @throws ReflectionException
     */
    public function getRelatedFormClass()
    {
        return $this->getService()->getRelatedFormClass();
    }

    /**
     * Получить новый обьект модели
     *
     * @return ActiveRecord
     * @throws ReflectionException
     */
    public function getRelatedModel()
    {
        return $this->getService()->getRelatedModel();
    }

    /**
     * Получить новый обьект формы
     *
     * @return Form
     * @throws ReflectionException
     */
    public function getRelatedForm()
    {
        return $this->getService()->getRelatedForm();
    }
}