<?php

namespace concepture\yii2logic\gii\model;

use Yii;
use yii\base\NotSupportedException;
use yii\db\Schema;
use yii\gii\CodeFile;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

class Generator extends \yii\gii\generators\model\Generator
{
    public $messageCategory = 'common';
    public $generateRelations = self::RELATIONS_NONE;
    public $enableI18N = true;
    public $ns = 'common\models';
    public $baseClass = 'concepture\yii2logic\models\ActiveRecord';

    public $generateForm = true;
    public $generateSearch = true;
    public $generateService = true;

    public $formNs;
    public $searchNs;
    public $serviceNs;

    /**
     * {@inheritdoc}
     */
    public function requiredTemplates()
    {
        $tmp = parent::requiredTemplates();

        return ArrayHelper::merge($tmp,[
            'form.php',
            'search.php',
            'service.php',
        ]);
    }

    public function hasStatusProperty($properties)
    {
        if (isset($properties['status'])){
            return true;
        }

        return false;
    }

    public function hasIsDeletedProperty($properties)
    {
        if (isset($properties['is_deleted'])){
            return true;
        }

        return false;
    }

    public function hasSeoProperty($properties)
    {
        if (isset($properties['seo_title'])
            && isset($properties['seo_h1'])
            && isset($properties['seo_description'])
            && isset($properties['seo_keywords'])
        ){
            return true;
        }

        return false;
    }

    public function isSeoProperty($property)
    {
        if (in_array($property, ['seo_title', 'seo_h1' , 'seo_description' , 'seo_keywords'])){
            return true;
        }

        return false;
    }

    public function generate()
    {
        $files = [];
        $relations = $this->generateRelations();
        $db = $this->getDbConnection();
        foreach ($this->getTableNames() as $tableName) {
            // model :
            $modelClassName = $this->generateClassName($tableName);
            $queryClassName = ($this->generateQuery) ? $this->generateQueryClassName($modelClassName) : false;
            $tableSchema = $db->getTableSchema($tableName);
            $params = [
                'tableName' => $tableName,
                'className' => $modelClassName,
                'queryClassName' => $queryClassName,
                'tableSchema' => $tableSchema,
                'properties' => $this->generateProperties($tableSchema),
                'labels' => $this->generateLabels($tableSchema),
                'rules' => $this->generateRules($tableSchema),
                'relations' => isset($relations[$tableName]) ? $relations[$tableName] : [],
            ];
            $files[] = new CodeFile(
                Yii::getAlias('@' . str_replace('\\', '/', $this->ns)) . '/' . $modelClassName . '.php',
                $this->render('model.php', $params)
            );

            if ($this->generateForm) {
                $formName = $modelClassName."Form";
                $formNs = str_replace("models", 'forms', $this->ns);
                $params['formNs'] = $formNs;
                $params['formName'] = $formName;
                $params['formProperties'] = $this->getFormProperties($params['properties']);
                $params['formBaseClass'] = 'kamaelkz\yii2admin\v1\forms\BaseForm';

                $files[] = new CodeFile(
                    Yii::getAlias('@' . str_replace('\\', '/', $formNs)) . '/' . $formName . '.php',
                    $this->render('form.php', $params)
                );
            }

            if ($this->generateSearch) {
                $searchName = $modelClassName."Search";
                $searchNs = str_replace("models", 'search', $this->ns);
                $params['searchNs'] = $searchNs;
                $params['searchName'] = $searchName;
                $params['searchBaseClass'] = $this->ns . '\\' . $modelClassName;

                $files[] = new CodeFile(
                    Yii::getAlias('@' . str_replace('\\', '/', $searchNs)) . '/' . $searchName . '.php',
                    $this->render('search.php', $params)
                );
            }

            if ($this->generateService) {
                $serviceName = $modelClassName."Service";
                $serviceNs = str_replace("models", 'services', $this->ns);
                $params['serviceNs'] = $serviceNs;
                $params['serviceName'] = $serviceName;
                $params['serviceBaseClass'] = 'concepture\yii2logic\services\Service';

                $files[] = new CodeFile(
                    Yii::getAlias('@' . str_replace('\\', '/', $serviceNs)) . '/' . $serviceName . '.php',
                    $this->render('service.php', $params)
                );
            }
        }

        return $files;
    }

    public function getFormProperties($properties)
    {
        $r = [];
        foreach ($properties as $property => $data){
            if (in_array($property,['id', 'created_at', 'updated_at', 'is_deleted'])) {
                continue;
            }
            $r[$property] = $data;
        }

        return $r;
    }

    /**
     * Переопределно только чтобы исключить сео атрибуты
     *
     * @param $table
     * @return array
     */
    public function generateRules($table)
    {
        $types = [];
        $lengths = [];
        foreach ($table->columns as $column) {
            if ($column->autoIncrement) {
                continue;
            }
            if ($this->isSeoProperty($column->name)){
                continue;
            }
            if (!$column->allowNull && $column->defaultValue === null) {
                $types['required'][] = $column->name;
            }
            switch ($column->type) {
                case Schema::TYPE_SMALLINT:
                case Schema::TYPE_INTEGER:
                case Schema::TYPE_BIGINT:
                case Schema::TYPE_TINYINT:
                    $types['integer'][] = $column->name;
                    break;
                case Schema::TYPE_BOOLEAN:
                    $types['boolean'][] = $column->name;
                    break;
                case Schema::TYPE_FLOAT:
                case Schema::TYPE_DOUBLE:
                case Schema::TYPE_DECIMAL:
                case Schema::TYPE_MONEY:
                    $types['number'][] = $column->name;
                    break;
                case Schema::TYPE_DATE:
                case Schema::TYPE_TIME:
                case Schema::TYPE_DATETIME:
                case Schema::TYPE_TIMESTAMP:
                case Schema::TYPE_JSON:
                    $types['safe'][] = $column->name;
                    break;
                default: // strings
                    if ($column->size > 0) {
                        $lengths[$column->size][] = $column->name;
                    } else {
                        $types['string'][] = $column->name;
                    }
            }
        }
        $rules = [];
        $driverName = $this->getDbDriverName();
        foreach ($types as $type => $columns) {
            if ($driverName === 'pgsql' && $type === 'integer') {
                $rules[] = "[['" . implode("', '", $columns) . "'], 'default', 'value' => null]";
            }
            $rules[] = "[['" . implode("', '", $columns) . "'], '$type']";
        }
        foreach ($lengths as $length => $columns) {
            $rules[] = "[['" . implode("', '", $columns) . "'], 'string', 'max' => $length]";
        }

        $db = $this->getDbConnection();

        // Unique indexes rules
        try {
            $uniqueIndexes = array_merge($db->getSchema()->findUniqueIndexes($table), [$table->primaryKey]);
            $uniqueIndexes = array_unique($uniqueIndexes, SORT_REGULAR);
            foreach ($uniqueIndexes as $uniqueColumns) {
                // Avoid validating auto incremental columns
                if (!$this->isColumnAutoIncremental($table, $uniqueColumns)) {
                    $attributesCount = count($uniqueColumns);

                    if ($attributesCount === 1) {
                        $rules[] = "[['" . $uniqueColumns[0] . "'], 'unique']";
                    } elseif ($attributesCount > 1) {
                        $columnsList = implode("', '", $uniqueColumns);
                        $rules[] = "[['$columnsList'], 'unique', 'targetAttribute' => ['$columnsList']]";
                    }
                }
            }
        } catch (NotSupportedException $e) {
            // doesn't support unique indexes information...do nothing
        }

        // Exist rules for foreign keys
        foreach ($table->foreignKeys as $refs) {
            $refTable = $refs[0];
            $refTableSchema = $db->getTableSchema($refTable);
            if ($refTableSchema === null) {
                // Foreign key could point to non-existing table: https://github.com/yiisoft/yii2-gii/issues/34
                continue;
            }
            $refClassName = $this->generateClassName($refTable);
            unset($refs[0]);
            $attributes = implode("', '", array_keys($refs));
            $targetAttributes = [];
            foreach ($refs as $key => $value) {
                $targetAttributes[] = "'$key' => '$value'";
            }
            $targetAttributes = implode(', ', $targetAttributes);
            $rules[] = "[['$attributes'], 'exist', 'skipOnError' => true, 'targetClass' => $refClassName::className(), 'targetAttribute' => [$targetAttributes]]";
        }

        return $rules;
    }

    /**
     * Переопределно только чтобы исключить сео атрибуты
     * @param $table
     * @return array
     */
    public function generateLabels($table)
    {
        $labels = [];
        foreach ($table->columns as $column) {
            if ($this->isSeoProperty($column->name)){
                continue;
            }
            if ($this->generateLabelsFromComments && !empty($column->comment)) {
                $labels[$column->name] = $column->comment;
            } elseif (!strcasecmp($column->name, 'id')) {
                $labels[$column->name] = 'ID';
            } else {
                $label = Inflector::camel2words($column->name);
                if (!empty($label) && substr_compare($label, ' id', -3, 3, true) === 0) {
                    $label = substr($label, 0, -3) . ' ID';
                }
                $labels[$column->name] = $label;
            }
        }

        return $labels;
    }
}
