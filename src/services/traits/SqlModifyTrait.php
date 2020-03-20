<?php
namespace concepture\yii2logic\services\traits;

use yii\db\Command;
use yii\db\Exception;

/**
 * Trait SqlModifyTrait
 * @package concepture\yii2logic\services\traits
 */
trait SqlModifyTrait
{
    /**
     * @param $sql
     * @param $params
     * @return boolean
     * @throws Exception
     */
    public function execute($sql, $params)
    {
        $command = $this->createCommand($sql, $params);

        return $command->execute();
    }
}

