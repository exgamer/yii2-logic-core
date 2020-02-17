<?php
namespace concepture\yii2logic\forms;

use yii\db\ActiveRecord;
use concepture\yii2logic\helpers\ClassHelper;
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
 * @package concepture\yii2logic\forms
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
        $model = Yii::createObject($modelClass);

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
        $model = Yii::createObject($modelClass);

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
        $me = Yii::createObject(static::class);

        return ClassHelper::getRelatedClass($me, ["Form" => ""], ["forms" => "models"]);
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
            /**
             * @todo костылище, для обруливания ситуации когда в форме есть атрибуты которых нет в модели, и при валидации выкидвает ошибку
             * @todo придумать что нибудь
             */
            try{
                $validator->validateAttributes($validationModel, $attributeNames);
            }catch (\Exception $ex){

            }
        }
        if ($validationModel->hasErrors()){
            $this->addErrors($validationModel->getErrors());
        }
        $this->afterValidate();

        return !$this->hasErrors();
    }

    /**
     * метод для заполнения формы кастомными данными из модели
     * например используется для заполнения данных в UpdateAction
     *
     * @param ActiveRecord $model
     */
    public function customizeForm(ActiveRecord $model = null)
    {

    }

    /**
     *
     * @see yii\base\Model::load()
     */
    public function load($data, $formName = null)
    {
        $result = parent::load($data, $formName);
        $this->afterLoad($data);

        return $result;
    }

    /**
     * Действия с формой после загрузки в нее данных
     * используется в UpdateAction
     * @param null $data
     */
    public function afterLoad($data){}

    /**
     * переопределен для возможности запроса данных из связанной модели формы при перезагрузке формы
     *
     * @param $method
     * @param $parameters
     * @return mixed
     * @throws ReflectionException
     */
    public function __call($method, $parameters)
    {
        $modelClass = static::getModelClass();
        $model = Yii::createObject($modelClass);
        $model->load($this->attributes, '');
        if (! method_exists($this, $method)){
            return call_user_func_array([$model, $method], $parameters);
        }

        parent::__call($method, $parameters);
    }

    /**
     * Метод для тог очтобы можно было установить метку для сущности
     * @return string
     * @throws ReflectionException
     */
    public static function label()
    {
        $modelClass = static::getModelClass();

        return $modelClass::label();
    }

    public function getErrors($attribute = null)
    {
        $errors = parent::getErrors($attribute);
        foreach ($errors as &$error){
            $error = array_unique($error);
        }

        return $errors;
    }
}