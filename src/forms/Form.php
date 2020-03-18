<?php
namespace concepture\yii2logic\forms;

use common\pojo\Social;
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
        $this->resolveJsonData($model);
    }

    /**
     *
     * @see yii\base\Model::load()
     */
    public function load($data, $formName = null)
    {
        $result = parent::load($data, $formName);
        if (! $result){
            return $result;
        }

        $this->afterLoad($data);

        return $result;
    }

    /**
     * Действия с формой после загрузки в нее данных
     * используется в UpdateAction
     * @param null $data
     */
    public function afterLoad($data)
    {
        $this->jsonDataLoad($data);
    }

    public function beforeValidate()
    {
        $validationResult = $this->validateJsonData();
        if (! $validationResult){
            return $validationResult;
        }

        return parent::beforeValidate();
    }

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

    /**
     * Для работы с json атрибутами формы
     * Создаем pojo модель для описания структуры данных
     * на форме используем DynamicForm::widget([
     *
     */

    /**
     * Возвращает атрибуты которые являются json данными
     *
     * [
     *   'attribute' => Pojo::class
     * ]
     *
     * @return array
     */
    public function jsonAttributes()
    {
        return [];
    }

    /**
     * Конвертирует json данные в pojo
     * @param $model
     */
    protected function resolveJsonData($model)
    {
        if (! $model){
            return;
        }

        $jsonAttrs = $this->jsonAttributes();
        foreach ($jsonAttrs as $attr => $pojoClass){
            $pojoClass = $this->getPojoData($pojoClass, 'class');
            $data = [];
            if ($model->{$attr}) {
                $model->{$attr} = json_decode($model->{$attr}, true);
                if ($model->{$attr} !== false) {
                    $data[ClassHelper::getShortClassName($pojoClass)] = $model->{$attr};
                }
            }

            $this->jsonDataCheck($data, $pojoClass, $attr,  true);
        }
    }

    /**
     * @param $data
     * @param $pojoClass
     * @param null $attribute
     * @param bool $loadData
     */
    protected function jsonDataCheck($data, $pojoClass, $attribute = null, $loadData = false)
    {
        $className = ClassHelper::getShortClassName($pojoClass);
        if (! $attribute){
            $attribute = strtolower($className);
        }

        if (! $this->{$attribute} || ! is_array($this->{$attribute})){
            $this->{$attribute} = [];
        }

        if ($loadData == false){
            $this->{$attribute} = [];
        }

        if (! isset($data[$className])) {
            return;
        }

        foreach ($data[$className] as $key => $post){
            $pojo = new $pojoClass();
            if ($loadData){
                $pojo->load($post, '');
            }

            $this->{$attribute}[$key] = $pojo;
        }
    }

    /**
     * load json данных
     *
     * @param $data
     */
    protected function jsonDataLoad($data)
    {
        $jsonAttrs = $this->jsonAttributes();
        foreach ($jsonAttrs as $attr => $pojoClass){
            $pojoClass = $this->getPojoData($pojoClass, 'class');
            $this->jsonDataCheck($data, $pojoClass);
            $className = ClassHelper::getShortClassName($pojoClass);
            $pojoClass::loadMultiple($this->{$attr}, $data, $className);
        }
    }

    /**
     * Валидация json данных
     *
     * @return bool
     */
    public function validateJsonData()
    {
        $validationResult = true;
        $jsonAttrs = $this->jsonAttributes();
        foreach ($jsonAttrs as $attr => $pojoClass){
            $pojoClass = $this->getPojoData($pojoClass, 'class');
            if (! empty($this->{$attr}) && ! $pojoClass::validateMultiple($this->{$attr}) && ! Yii::$app->request->post('refresh-form')){
                $validationResult = false;
            }
        }

        $validationResult = $this->validateJsonDataUnique();

        return $validationResult;
    }

    /**
     * Проверка json данных на уникальность
     *
     * @return bool
     */
    public function validateJsonDataUnique()
    {
        $validationResult = true;
        $jsonAttrs = $this->jsonAttributes();
        foreach ($jsonAttrs as $attr => $pojoClass){
            $uniqueKey = $this->getPojoData($pojoClass, 'uniqueKey' , true);
            if (! $uniqueKey){
                continue;
            }

            if (! is_array($uniqueKey)){
                $uniqueKey = [$uniqueKey];
            }

            $d = [];
            foreach ($this->{$attr} as $model){
                $key = '';
                foreach ($uniqueKey as $uAttr){
                    $key .= $model->{$uAttr};
                }

                if (isset($d[$key])){
                    $message = Yii::t('yii', '{attribute} "{value}" has already been taken.');
                    $message = str_replace('{attribute}', implode('-', $uniqueKey), $message);
                    $message = str_replace('{value}', $key, $message);
                    $model->addError($uniqueKey[0], $message);
                    $validationResult = false;
                }

                $d[$key] = $key;
            }
        }

        return $validationResult;
    }

    protected function getPojoData($pojoData, $key, $getOnlyKey = false)
    {
        if ($getOnlyKey){
            return   $pojoData[$key] ?? null;
        }

        if (! is_array($pojoData)){
            return $pojoData;
        }

        if (! isset($pojoData[$key])){
            throw new \Exception("no {$key} data");
        }

        return $pojoData[$key];
    }
}