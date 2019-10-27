<?php
namespace concepture\yii2logic\forms;

use concepture\yii2core\models\ActiveRecord;
use ReflectionClass;
use ReflectionException;
use yii\base\Model;
use Yii;
use yii\helpers\Json;
use yii\db\ActiveQuery;
use yii\db\Connection;

/**
 * Class Form
 * @package cconcepture\yii2logic\forms
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class Form extends Model
{
    /**
     * правила модели
     * @return array
     * @throws ReflectionException
     */
    public function rules()
    {
        $modelClass = static::getModelClass();
        $model = new $modelClass();

        return array_merge($this->formRules(), $model->rules());
    }

    /**
     * @return array
     * @throws ReflectionException
     */
    public function attributeLabels()
    {
        $modelClass = static::getModelClass();
        $model = new $modelClass();

        return array_merge($model->attributeLabels(), $this->formAttributeLabels());
    }

    /**
     * form rules
     * @return array
     */
    public function formRules()
    {
        return [];
    }

    /**
     * @return array
     */
    public function formAttributeLabels()
    {
        return [];
    }

    /**
     * возвращает класс модели
     *
     * @return string;
     * @throws ReflectionException
     */
    public function getModelClass()
    {
        $reflection = new ReflectionClass($this);
        $name = $reflection->getShortName();
        $name = str_replace("Form", "", $name);
        $nameSpace = $reflection->getNamespaceName();
        $nameSpace = str_replace("forms", "models", $nameSpace);

        return  $nameSpace."\\".$name;
    }

    /**
     * @return Connection
     * @throws ReflectionException
     */
    public static function getDb()
    {
        $modelClass =  static::getModelClass();

        return $modelClass::getDb();
    }

    /**
     * @return ActiveQuery
     * @throws ReflectionException
     */
    public static function find()
    {
        $modelClass =  static::getModelClass();

        return $modelClass::find();
    }
}