<?php
namespace concepture\yii2logic\console\migrations;

use yii\db\Migration as Base;

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
        $index_name = implode("_", $columns);
        $this->createIndex('index_'.$index_name,
            '{{%'.$this->getTableName().'}}',
            $columns,
            $unique);
    }

    protected function addUniqueIndex($columns)
    {
        $index_name = implode("_", $columns);
        $this->createIndex('index_unique'.$index_name,
            '{{%'.$this->getTableName().'}}',
            $columns,
            true);
    }

    abstract function getTableName();
}