<?php

namespace concepture\yii2logic\gii\model;

use Yii;
use yii\gii\CodeFile;
use yii\helpers\ArrayHelper;

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
        if (isset($properties['seo_title'])){
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
}
