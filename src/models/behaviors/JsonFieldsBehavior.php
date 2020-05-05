<?php
namespace concepture\yii2logic\models\behaviors;

use concepture\yii2logic\helpers\ClassHelper;
use Yii;
use yii\base\Behavior;
use yii\db\ActiveRecord;

/**
 * Поведение для полей являющихся Json строкой
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
//            ActiveRecord::EVENT_BEFORE_INSERT => 'setJson',
//            ActiveRecord::EVENT_AFTER_VALIDATE => 'setJson',
//            ActiveRecord::EVENT_BEFORE_UPDATE => 'setJson',
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

        $this->owner->{$attribute} = json_decode($this->owner->{$attribute}, true) ? json_decode($this->owner->{$attribute}, true) : [];
    }

    /**
     *
     * @param $attribute
     */
    protected function resolveAttribute($attribute, $attributeConfig)
    {
        if (! $attributeConfig){
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

            $pojo = new $pojoClass();
            $pojo->load($value, '');
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
            if (! empty($this->owner->{$attr}) && ! $pojoClass::validateMultiple($this->owner->{$attr})){
                $validationResult = false;
            }
        }

        $validationResult = $this->validateJsonDataUnique($validationResult);

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
     *
     */

    /**
     * @deprecated setJson не используем потому что фреимворк сам конвертит при сохранении (тестируем)
     * Перед сохранением преобразуем объект в json строку
     */
    public function setJson()
    {
        if(empty($this->jsonAttr) ){
            return null;
        }
        foreach ($this->jsonAttr as $attr) {
            if (!is_array($this->owner->{$attr})){
                continue;
            }
            $this->owner->{$attr} = json_encode($this->owner->{$attr});
        }
    }
}