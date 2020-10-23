<?php

namespace concepture\yii2logic\services\interfaces;

use ReflectionException;
use yii\db\Command;
use yii\db\Connection;

/**
 * Interface HasDbConnectionInterface
 * @package concepture\yii2logic\services\interfaces
 */
interface HasDbConnectionInterface
{
    /**
     * @param $sql
     * @param array $params
     * @return Command
     * @throws ReflectionException
     */
    public function createCommand($sql = null, $params = []);

    /**
     * Возвращает соединение к БД
     *
     * @return Connection
     * @throws ReflectionException
     */
    public function getDb();

    public function getDbType();;

    public function isPostgres();
}