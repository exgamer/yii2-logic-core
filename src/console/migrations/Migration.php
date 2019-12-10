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

    public function down()
    {
        $this->dropTable('{{%'.$this->getTableName().'}}');
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
        $key_name = implode("_", $columns)."_pk";
        $this->addPrimaryKey( $key_name, '{{%'.$this->getTableName().'}}', $columns);
    }

    protected function addIndex($columns, $unique = false)
    {
        $index_name = 'ind_'. implode("_", $columns);
        $this->createIndex($index_name,
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
        if ($index_name === null) {
            $index_name = 'uni_' . implode("_", $columns);
        }
        $this->createIndex($index_name,
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