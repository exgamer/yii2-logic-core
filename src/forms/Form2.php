<?php
namespace concepture\yii2logic\forms;

use common\pojo\Social;
use concepture\yii2logic\models\behaviors\JsonFieldsBehavior;
use concepture\yii2logic\validators\DomainBasedUniqueValidator;
use concepture\yii2logic\validators\UniqueLocalizedValidator;
use concepture\yii2logic\validators\v2\UniquePropertyValidator;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use concepture\yii2logic\helpers\ClassHelper;
use ReflectionException;
use Yii;
use yii\base\InvalidArgumentException;
use yii\helpers\Json;
use yii\db\ActiveQuery;
use yii\db\Connection;
use yii\validators\InlineValidator;
use yii\validators\Validator;

/**
 * @deprecated
 * @TODO Переделанная форма нужно потестить и потом заменить ей основную если все в порядке
 *
 * Базовая форма сущности связанной с  моделью AR
 *
 * Class Form
 * @package concepture\yii2logic\forms
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class Form extends Model
{
    /**
     * Валидаторы которые будут исключены при валидации формы
     * для валидаторов которые используют БД
     * @var string[]
     */
    public $excludedValidators = [
        UniquePropertyValidator::class,
        DomainBasedUniqueValidator::class,
        UniqueLocalizedValidator::class,
        \concepture\yii2logic\validators\UniquePropertyValidator::class
    ];

    /**
     * возвращает массив содержащий правила связанной модели и текущей формы
     * @return array
     * @throws ReflectionException
     */
    public function rules()
    {
        $model = static::getModel();
        $modelRules = $model->rules();
        $result = [];
        $built = Validator::$builtInValidators;
        foreach ($modelRules as $rule) {
            if (is_array($rule) && isset($rule[0], $rule[1])) {
                $type = $rule[1];
                $yes = false;
                if ($type instanceof \Closure) {
                    $yes = true;
                }
                if ($this->hasMethod($type)) {
                    $yes = true;
                }
                if (isset($built[$type]) && $type != 'unique') {
                    $yes = true;
                }
                if (class_exists($type) && ! in_array($type, $this->excludedValidators)) {
                    $yes = true;
                }

                if ($yes) {
                    $result[] = $rule;
                }
            }
        }

        return array_merge($result, $this->formRules());
    }

    /**
     * возвращает массив содержащий метки аттрибутов связанной модели и текущей формы
     * @return array
     * @throws ReflectionException
     */
    public function attributeLabels()
    {
        $model = static::getModel();

        return array_merge($model->attributeLabels(), $this->formAttributeLabels());
    }

    public function behaviors()
    {
        $model = static::getModel();

        return array_merge($model->behaviors(), $this->formBehaviors());
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
     * метод для определения поведения формы
     * @return array
     */
    public function formBehaviors()
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

    public static function getModel()
    {
        $modelClass =  static::getModelClass();

        return Yii::createObject($modelClass);
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
        $model = static::getModel();

        return $model::getDb();
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
        $model = static::getModel();

        return $model::find();
    }

    /**
     * @todo это поддержка кастомных валидаторов
     * Метод переопределен для возможнсти подстановки в форму связанной модели для валидации при редактировании
     * чтобы корректно работала валидация где нужен id сущности без указания ее в форме
     *
     * @param null $attributeNames
     * @param bool $clearErrors
     * @param null $model
     * @return bool
     */
//    public function validate($attributeNames = null, $clearErrors = true, $model = null)
//    {
//        if ($clearErrors) {
//            $this->clearErrors();
//            if ($model){
//                $model->clearErrors();
//            }
//        }
//
//        if (!$this->beforeValidate()) {
//            return false;
//        }
//
//        $scenarios = $this->scenarios();
//        $scenario = $this->getScenario();
//        if (!isset($scenarios[$scenario])) {
//            throw new InvalidArgumentException("Unknown scenario: $scenario");
//        }
//
//        if ($attributeNames === null) {
//            $attributeNames = $this->activeAttributes();
//        }
//
//        $attributeNames = (array)$attributeNames;
//        foreach ($this->getActiveValidators() as $validator) {
//            if (! $model) {
//                $validator->validateAttributes($this, $attributeNames);
//                continue;
//            }
//
//            /**
//             * @todo костылище, для обруливания ситуации когда в форме есть атрибуты которых нет в модели, и при валидации выкидвает ошибку
//             * @todo придумать что нибудь
//             * @todo если ошибка значит пробуем валидировать форму
//             */
//            try{
//                $validator->validateAttributes($model, $attributeNames);
//            }catch (\Exception $ex){
//                $validator->validateAttributes($this, $attributeNames);
//            }
//        }
//        if ($model && $model->hasErrors()){
//            $this->addErrors($model->getErrors());
//        }
//        $this->afterValidate();
//
//        return !$this->hasErrors();
//    }

    /**
     * метод для заполнения формы кастомными данными из модели
     * например используется для заполнения данных в UpdateAction
     *
     * @param ActiveRecord $model
     */
    public function customizeForm(ActiveRecord $model = null){}

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

//    /**
//     * переопределен для возможности запроса данных из связанной модели формы при перезагрузке формы
//     *
//     * @param $method
//     * @param $parameters
//     * @return mixed
//     * @throws ReflectionException
//     */
//    public function __call($method, $parameters)
//    {
//        $model = static::getModel();
//        $model->load($this->attributes, '');
//        if (! method_exists($this, $method)){
//            return call_user_func_array([$model, $method], $parameters);
//        }
//
//        parent::__call($method, $parameters);
//    }

    /**
     * Метод для тог очтобы можно было установить метку для сущности
     * @return string
     * @throws ReflectionException
     */
    public static function label()
    {
        $model = static::getModel();

        return $model::label();
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
     * load json данных
     *
     * @param $data
     */
    protected function jsonDataLoad($data)
    {
        $model = static::getModel();
        if (! ClassHelper::getBehavior($model, JsonFieldsBehavior::class)){
            return;
        }

        $jsonAttrs = $model->getPojoAttributes();
        foreach ($jsonAttrs as $attr => $pojoClass) {
            $pojoClass = $model->getAttributeConfigData($pojoClass, 'class');
            $className = ClassHelper::getShortClassName($pojoClass);
            $value = [];
            if (isset($data[$className])) {
                $value = $data[$className];
            } else if (isset($data[$attr])) {
                $value = $data[$attr];
            }

            if (empty($value)) {
                continue;
            }

            $this->{$attr} = $value;
            $pogoData = [];
            foreach ($this->{$attr} as $key => $value){
                if (! is_array($value) ){
                    continue;
                }

                $pojo = Yii::createObject($pojoClass);
                $pojo->load($value, '');
                $pogoData[$key] = $pojo;
            }

            if (count($pogoData) == 1 && $pogoData[0]->isAllRequiredEmpty()) {
                $this->{$attr} = [];
                continue;
            }

            if ($pogoData) {
                $this->{$attr} = $pogoData;
            }
        }
    }
}
