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
 * @TODO Тестим
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
     * Исключает уникальные валидаторы, кастомные валидаторы которые есть только в модели
     *
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
                /**
                 * Если валидатор анонимка
                 */
                if ($type instanceof \Closure) {
                    $yes = true;
                }

                /**
                 * Если валидатор метод
                 */
                if ($this->hasMethod($type)) {
                    $yes = true;
                }

                /**
                 * если валидатор стандартный и это не уникальность
                 */
                if (isset($built[$type]) && $type != 'unique') {
                    $yes = true;
                }

                /**
                 * Если валидатор не является исключением
                 */
                if (class_exists($type) && ! in_array($type, $this->excludedValidators)) {
                    $yes = true;
                }

                if ($yes) {
                    $result[] = $rule;
                }
            }
        }

        return array_merge($this->formRules(), $result);
    }

    /**
     * возвращает массив содержащий метки аттрибутов связанной модели и текущей формы
     * @return array
     * @throws ReflectionException
     */
    public function attributeLabels()
    {
        $model = static::getModel();

        return array_merge($this->formAttributeLabels(), $model->attributeLabels());
    }

    public function behaviors()
    {
        $model = static::getModel();

        return array_merge($this->formBehaviors(), $model->behaviors()
        );
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

        $this->afterLoad($data, $formName);

        return $result;
    }

    /**
     * Действия с формой после загрузки в нее данных
     * используется в UpdateAction
     * @param null $data
     * @param null $formName
     */
    public function afterLoad($data, $formName = null)
    {
        $this->jsonDataResolve($data, $formName);
        $this->pojoDataLoad($data);
    }

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
     * Метод для зачистки json поля в случае когда в load не пришло данных для атрибута, что значит что пользователь удалил их
     *
     * @param $data
     * @param null $formName
     * @return bool
     * @throws InvalidConfigException
     */
    public function jsonDataResolve($data, $formName = null)
    {
        $scope = $formName === null ? $this->formName() : $formName;
        $model = static::getModel();
        if (! ClassHelper::getBehavior($model, JsonFieldsBehavior::class)){
            return;
        }

        if ($scope !== '' && isset($data[$scope])) {
            $data = $data[$scope];
        }

        $jsonAttrs = $model->getJsonAttributes();
        foreach ($jsonAttrs as $attr) {
            if (! isset($data[$attr])) {
                $this->{$attr} = [];
            }
        }

        return true;
    }

    /**
     * load pojo данных
     *
     * @param $data
     */
    protected function pojoDataLoad($data)
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

            $this->{$attr} = $value;
            $pogoData = [];
            foreach ($this->{$attr} as $key => $value) {
                if (! is_array($value) ){
                    continue;
                }

                $pojo = Yii::createObject($pojoClass);
                $pojo->load($value, '');
                $pogoData[$key] = $pojo;
            }

            if (count($pogoData) == 1 && reset($pogoData)->isAllRequiredEmpty()) {
                $this->{$attr} = [];
                continue;
            }

            if ($pogoData) {
                $this->{$attr} = $pogoData;
            }
        }
    }
}
