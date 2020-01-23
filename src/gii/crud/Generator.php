<?php

namespace concepture\yii2logic\gii\crud;



use Yii;
use concepture\yii2logic\helpers\ClassHelper;
use concepture\yii2logic\helpers\StringHelper;
use yii\db\BaseActiveRecord;
use yii\gii\CodeFile;
use yii\helpers\Inflector;
use yii\web\Controller;

class Generator extends \yii\gii\generators\crud\Generator
{
    public $baseControllerClass = 'concepture\yii2logic\controllers\web\Controller';
    public $controllerClass = 'backend\controllers\AccountOperationController' ;
    public $enableI18N = true;
    public $enablePjax = true;
    public $searchModelClass =  null;
    public $messageCategory = 'yii2admin';

    public function hasSeoProperty($properties)
    {
        $properties = array_flip($properties);
        if (isset($properties['seo_title'])
            && isset($properties['seo_h1'])
            && isset($properties['seo_name'])
            && isset($properties['seo_description'])
            && isset($properties['seo_keywords'])
        ){
            return true;
        }

        return false;
    }

    public function isSeoProperty($property)
    {
        if (in_array($property, ['seo_title', 'seo_name', 'seo_h1' , 'seo_description' , 'seo_keywords'])){
            return true;
        }

        return false;
    }

    public function generate()
    {
        if ($this->modelClass) {
            $modelClassName = ClassHelper::getShortClassName($this->modelClass);
            $this->controllerClass = "backend\controllers" . '\\' . $modelClassName . "Controller";
            $this->viewPath = "@backend\\views\\" . Inflector::slug(Inflector::camel2id($modelClassName));
        }
        $controllerFile = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->controllerClass, '\\')) . '.php');

        $files = [
            new CodeFile($controllerFile, $this->render('controller.php')),
        ];

        if (!empty($this->searchModelClass)) {
            $searchModel = Yii::getAlias('@' . str_replace('\\', '/', ltrim($this->searchModelClass, '\\') . '.php'));
            $files[] = new CodeFile($searchModel, $this->render('search.php'));
        }

        $viewPath = $this->getViewPath();
        $templatePath = $this->getTemplatePath() . '/views';
        foreach (scandir($templatePath) as $file) {
            if (is_file($templatePath . '/' . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
                $files[] = new CodeFile("$viewPath/$file", $this->render("views/$file"));
            }
        }

        return $files;
    }
}
