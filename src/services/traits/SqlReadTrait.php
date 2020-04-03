<?php
namespace concepture\yii2logic\services\traits;

/**
 * Trait SqlReadTrait
 * @package concepture\yii2logic\services\traits
 */
trait SqlReadTrait
{
    /**
     * @param $sql
     * @param $params
     * @param int $fetchMode the result fetch mode. Please refer to [PHP manual](https://secure.php.net/manual/en/function.PDOStatement-setFetchMode.php)
     * for valid fetch modes. If this parameter is null, the value set in [[fetchMode]] will be used.
     * @return array
     */
    public function queryAll($sql, $params, $fetchMode = null)
    {
        $command = $this->createCommand($sql, $params);

        return $command->queryAll($fetchMode);
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
        $command = $this->createCommand($sql, $params);

        return $command->queryOne($fetchMode);
    }
}

