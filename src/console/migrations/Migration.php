<?php
namespace concepture\yii2logic\console\migrations;

use yii\db\Migration as Base;

/**
 * Базовая мигргация
 *
 * Class Migration
 * @package concepture\yii2logic\console\migrations
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class Migration extends Base
{
    protected $tableName;

    protected function isMysql()
    {
        if ($this->getDbType() == 'mysql'){
            return true;
        }

        return false;
    }

    protected function isPostgres()
    {
        if ($this->getDbType() == 'pgsql'){
            return true;
        }

        return false;
    }

    protected function getDbType()
    {
        return Yii::$app->db->getDriverName();
    }

    protected function getTableOptions()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            // http://stackoverflow.com/questions/766809/whats-the-difference-between-utf8-general-ci-and-utf8-unicode-ci
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        return $tableOptions;
    }

    protected function addTable($columns)
    {
        $this->createTable('{{%'.$this->getTableName().'}}', $columns, $this->getTableOptions());
    }

    protected function addPK($columns, $unique = false)
    {
        $tableName = $this->getTableName();
        $key_name = implode("_", $columns)."_pk". "_" . $tableName;
        $this->addPrimaryKey( $key_name, '{{%'.$this->getTableName().'}}', $columns);
    }

    protected function addIndex($columns, $unique = false)
    {
        $tableName = $this->getTableName();
        $index_name = 'ind_'. implode("_", $columns);
        $this->createIndex($index_name. "_" . $tableName,
            '{{%'.$this->getTableName().'}}',
            $columns,
            $unique);
    }

    protected function removeIndex($name)
    {
        $this->dropIndex($name, $this->getTableName());
    }

    protected function addUniqueIndex($columns, $index_name = null)
    {
        $tableName = $this->getTableName();
        if ($index_name === null) {
            $index_name = 'uni_' . implode("_", $columns);
        }
        $this->createIndex($index_name. "_" . $tableName,
            '{{%'.$this->getTableName().'}}',
            $columns,
            true);
    }

    protected function addForeign($column, $refTable, $refColumn, $delete = null, $update = null)
    {
        $tableName = $this->getTableName();
        $name = "fk_{$tableName}_{$column}_{$refTable}_{$refColumn}";
        $this->addForeignKey($name, $tableName, $column, $refTable, $refColumn, $delete, $update);
    }

    protected function createColumn($column, $type)
    {
        $tableName = $this->getTableName();
        $this->addColumn($tableName, $column, $type);
    }

    protected function removeColumn($column)
    {
        $tableName = $this->getTableName();
        $this->dropColumn($tableName, $column);
    }

    abstract function getTableName();
}