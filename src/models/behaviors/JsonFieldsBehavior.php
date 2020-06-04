<?php
namespace concepture\yii2logic\models\behaviors;

use common\models\Review;
use concepture\yii2logic\helpers\ClassHelper;
use concepture\yii2logic\helpers\StringHelper;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\Json;
use Exception;

/**
 * Поведение для полей являющихся Json строкой
 *
 *   public function behaviors()
 *   {
 *       return [
 *           'JsonFieldsBehavior' => [
 *               'class' => 'concepture\yii2logic\models\behaviors\JsonFieldsBehavior',
 *               'jsonAttr' => [
 *                   'languages',
 *                   'currencies',
 *                   'restricted_countries',
 *                   'social' => [
 *                       'class' => concepture\yii2logic\pojo\Social::class,
 *                       'uniqueKey' => 'social'
 *                   ],
 *                   'spoilers' => [
 *                       'class' => Spoiler::class,
 *                   ],
 *                   'payment_systems' => [
 *                       'class' => PaymentSystem::class,
 *                   ],
 *               ],
 *           ],
 *       ];
 *   }
 *
 * @author CitizenZet <exgamer@live.ru>
 */
class JsonFieldsBehavior extends Behavior
{
    /**
     * Массив атрибутов которые сохраняются json
     *
     * @var array
     */
    public $jsonAttr = [];

    public function events()
    {
        return [
            /**
             * setJson не используем потому что фреимворк сам конвертит при сохранении (тестируем)
             */
            ActiveRecord::EVENT_BEFORE_INSERT => 'setJson',
//            ActiveRecord::EVENT_AFTER_VALIDATE => 'setJson',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'setJson',
            ActiveRecord::EVENT_AFTER_FIND => 'getJson',
            ActiveRecord::EVENT_AFTER_INSERT=>'getJson',
            ActiveRecord::EVENT_AFTER_UPDATE=>'getJson',
            ActiveRecord::EVENT_BEFORE_VALIDATE =>'validatePojo',
        ];
    }

    public function validatePojo($event)
    {
        $this->getJson();
        $event->isValid = $this->validateJsonData();
    }

    /**
     * После нахождения объекта преобразуем json строки в объект
     */
    public function getJson()
    {
        if(empty($this->jsonAttr)){
            return null;
        }

        foreach ($this->jsonAttr as $key => $value) {
            $attribute = $value;
            $attributeConfig = null;
            if ( filter_var($key, FILTER_VALIDATE_INT) === false ) {
                $attribute = $key;
                $attributeConfig = $value;
            }

            $this->attributeToArray($attribute);
            $this->resolveAttribute($attribute, $attributeConfig);
        }
    }

    /**
     * Конвертирует атрибут в массив
     *
     * @param $attribute
     */
    protected function attributeToArray($attribute)
    {
        if(! is_string($this->owner->{$attribute})){
            return;
        }

        $this->owner->{$attribute} = Json::decode($this->owner->{$attribute}, true) ?? [];
    }

    /**
     *
     * @param $attribute
     */
    protected function resolveAttribute($attribute, $attributeConfig)
    {
        if (! $attributeConfig) {
            return;
        }

        if (! $this->owner->{$attribute}){
            return;
        }

        if (! is_array($this->owner->{$attribute})){
            return;
        }

        $pojoClass = $this->getAttributeConfigData($attributeConfig, 'class');
        $data = $this->owner->{$attribute};
        $pogoData = [];
        foreach ($data as $key => $value){
            if (! is_array($value) ){
                $pogoData[$key] = $value;
                continue;
            }

            $pojo = Yii::createObject($pojoClass);
            $pojo->load($value, '');
            $pojo->isNewRecord = false;
            $pogoData[$key] = $pojo;
        }

        $this->owner->{$attribute} = $pogoData;
    }

    public function getAttributeConfigData($attributeData, $key)
    {
        if (! is_array($attributeData)){
            return $attributeData;
        }

        return $attributeData[$key] ?? null;
    }

    public function getPojoAttributes()
    {
        $behavior = ClassHelper::getBehavior($this->owner, JsonFieldsBehavior::class);
        $attrs = $behavior['jsonAttr'] ?? [];
        $pojoAttrs = [];
        foreach ($attrs as $key => $value){
            if ( filter_var($key, FILTER_VALIDATE_INT) === false ) {
                $pojoAttrs[$key] = $value;
            }
        }

        return $pojoAttrs;
    }

    public function getPojoModels($attribute)
    {
        $pojoAttributes = $this->getPojoAttributes();
        if (! isset($pojoAttributes[$attribute])){
            throw new Exception($attribute . " is no pojo data");
        }

        if ($this->owner->{$attribute}){
            return $this->owner->{$attribute};
        }

        $pojoClass = $this->getAttributeConfigData($pojoAttributes[$attribute], 'class');
        $pojo = Yii::createObject($pojoClass);
        $pojo->isNewRecord = true;

        return [$pojo];
    }

    /**
     * Валидация
     */
    /**
     * Валидация json данных
     *
     * @return bool
     */
    public function validateJsonData()
    {
        $validationResult = true;
        $jsonAttrs = $this->getPojoAttributes();
        foreach ($jsonAttrs as $attr => $pojoClass){
            $pojoClass = $this->getAttributeConfigData($pojoClass, 'class');
            $pojoObject = Yii::createObject($pojoClass);
            if (! empty($this->owner->{$attr}) && ! $pojoObject::validateMultiple($this->owner->{$attr})){
                $validationResult = false;
            }
        }

        $validationResult = $this->validateJsonDataUnique($validationResult);

        // Для того чтобы ошибки валидации pojo можно было получить через owner->getErrors()
        // TODO: Понаблюдать
        foreach ($jsonAttrs as $attr => $pojoClass) {
            if (!empty($this->owner->{$attr}) && is_array($this->owner->{$attr})) {
                foreach ($this->owner->{$attr} as $model) {
                    if ($error = $model->getFirstError($attr)) {
                        $this->owner->addError($attr, $error);
                    }
                }
            }
        }

        return $validationResult;
    }

    /**
     * Проверка json данных на уникальность
     *
     * @return bool
     */
    public function validateJsonDataUnique($validationResult = true)
    {
        $jsonAttrs = $this->getPojoAttributes();
        foreach ($jsonAttrs as $attr => $pojoClass){
            $uniqueKey = $this->getAttributeConfigData($pojoClass, 'uniqueKey');
            if (! $uniqueKey){
                continue;
            }

            if (! is_array($uniqueKey)){
                $uniqueKey = [$uniqueKey];
            }

            $d = [];
            if (is_array($this->owner->{$attr})) {
                foreach ($this->owner->{$attr} as $model) {
                    $key = '';
                    foreach ($uniqueKey as $uAttr) {
                        $key .= $model->{$uAttr};
                    }

                    if (isset($d[$key])) {
                        $message = Yii::t('yii', '{attribute} "{value}" has already been taken.');
                        $message = str_replace('{attribute}', implode('-', $uniqueKey), $message);
                        $message = str_replace('{value}', $key, $message);
                        $model->addError($uniqueKey[0], $message);
                        $validationResult = false;
                    }

                    $d[$key] = $key;
                }
            }
        }

        return $validationResult;
    }

    /**
     * Перед сохранением преобразуем объект в json строку только если тип поля текстовый
     * для json полей конвертится само
     */
    public function setJson()
    {
        if(empty($this->jsonAttr) ) {
            return null;
        }

        foreach ($this->jsonAttr as $key => $config) {
            $attr = $key;
            if (! is_array($config) ) {
                $attr = $config;
            }

            $dbType = $this->owner->getAttrDbType($attr);
            if (in_array($dbType, ['json', 'jsonb'])) {
                continue;
            }

            if (! is_array($this->owner->{$attr})) {
                continue;
            }

            $items = $this->owner->{$attr};
            foreach ($items as &$item) {
                if(is_string($item) && StringHelper::isJson($item)) {
                    $item = Json::decode($item);
                }
            }

            $this->owner->{$attr} = Json::encode($items);
        }
    }
}