<?php
namespace concepture\yii2logic\forms;

use concepture\yii2core\models\ActiveRecord;
use ReflectionClass;
use ReflectionException;
use Yii;
use yii\base\InvalidArgumentException;
use yii\helpers\Json;
use yii\db\ActiveQuery;
use yii\db\Connection;

/**
 * Базовая форма сущности связанной с  моделью AR
 *
 * Class Form
 * @package cconcepture\yii2logic\forms
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class Form extends Model
{
    /**
     * возвращает массив содержащий правила связанной модели и текущей формы
     * @return array
     * @throws ReflectionException
     */
    public function rules()
    {
        $modelClass = $this->getModelClass();
        $model = new $modelClass();

        return array_merge($this->formRules(), $model->rules());
    }

    /**
     * возвращает массив содержащий метки аттрибутов связанной модели и текущей формы
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
     * метод для определения правил валидации для формы
     *
     * form rules
     * @return array
     */
    public function formRules()
    {
        return [];
    }

    /**
     * метод для определения меток аттрибутов для формы
     *
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
    public static function getModelClass()
    {
        $me = new static();
        $reflection = new ReflectionClass($me);
        $name = $reflection->getShortName();
        $name = str_replace("Form", "", $name);
        $nameSpace = $reflection->getNamespaceName();
        $nameSpace = str_replace("forms", "models", $nameSpace);

        return  $nameSpace."\\".$name;
    }

    /**
     * Возвращает соединение к БД модели
     * реализовано для возможности использования валидаторов котрые требуют обращения к БД
     *
     * @return Connection
     * @throws ReflectionException
     */
    public static function getDb()
    {
        $modelClass =  static::getModelClass();

        return $modelClass::getDb();
    }

    /**
     * Возвращает ActiveQuery из модели
     * реализовано для возможности использования валидаторов котрые требуют обращения к БД
     *
     * @return ActiveQuery
     * @throws ReflectionException
     */
    public static function find()
    {
        $modelClass =  static::getModelClass();

        return $modelClass::find();
    }

    /**
     * Метод переопределен для возможнсти подстановки в форму связанной модели для валидации при редактировании
     * чтобы корректно работала валидация где нужен id сущности без указания ее в форме
     *
     * @param null $attributeNames
     * @param bool $clearErrors
     * @param null $model
     * @return bool
     */
    public function validate($attributeNames = null, $clearErrors = true, $model = null)
    {
        if ($clearErrors) {
            $this->clearErrors();
            if ($model){
                $model->clearErrors();
            }
        }

        if (!$this->beforeValidate()) {
            return false;
        }

        $scenarios = $this->scenarios();
        $scenario = $this->getScenario();
        if (!isset($scenarios[$scenario])) {
            throw new InvalidArgumentException("Unknown scenario: $scenario");
        }

        if ($attributeNames === null) {
            $attributeNames = $this->activeAttributes();
        }

        $attributeNames = (array)$attributeNames;
        $validationModel = $this;
        if ($model){
            $validationModel = $model;
        }
        foreach ($this->getActiveValidators() as $validator) {
            $validator->validateAttributes($validationModel, $attributeNames);
        }
        if ($validationModel->hasErrors()){
            $this->addErrors($validationModel->getErrors());
        }
        $this->afterValidate();

        return !$this->hasErrors();
    }
}