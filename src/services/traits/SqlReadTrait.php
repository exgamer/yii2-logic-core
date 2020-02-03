<?php
namespace concepture\yii2logic\services\traits;

use yii\db\Command;
use yii\db\Exception;

/**
 * Trait SqlReadTrait
 * @package concepture\yii2logic\services\traits
 */
trait SqlReadTrait
{
    /**
     * @param $sql
     * @param array $params
     * @return Command
     */
    public function getCommand($sql, $params = [])
    {
        return $this->getDb()->createCommand($sql, $params);
    }

    /**
     * @param $sql
     * @param $params
     * @param int $fetchMode the result fetch mode. Please refer to [PHP manual](https://secure.php.net/manual/en/function.PDOStatement-setFetchMode.php)
     * for valid fetch modes. If this parameter is null, the value set in [[fetchMode]] will be used.
     * @return array
     */
    public function queryAll($sql, $params, $fetchMode = null)
    {
        $command = $this->getCommand($sql, $params);

        return $command->queryAll();
    }

    /**
     * @param $sql
     * @param $params
     * @param int $fetchMode the result fetch mode. Please refer to [PHP manual](https://secure.php.net/manual/en/function.PDOStatement-setFetchMode.php)
     * for valid fetch modes. If this parameter is null, the value set in [[fetchMode]] will be used.
     * @return array
     */
    public function queryOne($sql, $params, $fetchMode = null)
    {
        $command = $this->getCommand($sql, $params);

        return $command->queryOne();
    }
}

