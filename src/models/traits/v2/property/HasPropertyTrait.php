<?php
namespace concepture\yii2logic\models\traits\v2\property;

use Exception;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * Треит для работы с данными у которых свойства хранятся в другой таблице
 *
 * Trait HasPropertyTrait
 * @package concepture\yii2logic\models\traits
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
trait HasPropertyTrait
{
    /**
     * Возвращает названия полей свойств, которые будут исключены при маппинге данных из основной модели
     *
     * @return array
     * @throws Exception
     */
    public static function excludedPropertyFields()
    {
        return [
            'id',
            'entity_id',
            'default',
            static::uniqueField(),
        ];
    }

    /**
     * Возвращает название поля по которому будет разделение свойств
     *
     * @return string
     * @throws Exception
     */
    public static function uniqueField()
    {
        throw new Exception("please set unique field name");
    }

    /**
     * Возвращает значение поля по которому будет разделение свойств
     *
     * @return mixed
     * @throws Exception
     */
    public static function uniqueFieldValue()
    {
        throw new Exception("please set unique field value");
    }

    /**
     * Возвращает альяс для свойства
     *
     * @return string
     */
    public static function propertyAlias()
    {
        return "p";
    }

    /**
     * Возвращает массив с select для свойств
     * с помощью этого для незполненных свойств текущей записи будет заполнена из записи где default = 1
     *
     * @return array
     * @throws Exception
     */
    public static function constructPropertySelect()
    {
        $propertyAlias = static::propertyAlias();
        $propertyClass = static::getPropertyModelClass();
        $property = new $propertyClass();
        $result = [];
        foreach ($property->attributes() as $attribute){
            if (in_array($attribute, static::excludedPropertyFields())){
                continue;
            }

            $result[] = new Expression("CASE {$propertyAlias}.{$attribute}
                               WHEN null
                                   THEN d.{$attribute}
                                   ELSE
                                       {$propertyAlias}.{$attribute}
                                       END as {$attribute}");
        }

        /**
         * Добавялем в выборку uniqueField
         */
        $result[] = static::propertyAlias() . "." . static::uniqueField();

        return $result;
    }

    /**
     * Переопределяем find чтобы подцепить свойства
     *
     * @return ActiveQuery
     * @throws InvalidConfigException
     * @throws Exception
     */
    public static function find()
    {
        $query = Yii::createObject(ActiveQuery::class, [get_called_class()]);
        $m = static::getPropertyModelClass();
        $selectArray = static::constructPropertySelect();
        $selectArray[] = static::tableName(). ".*";
        $query->select($selectArray);
        /**
         * Выборка свойств для текущего uniqueField
         */
        $query->innerJoin($m::tableName() . " ". static::propertyAlias(), static::propertyAlias() . '.entity_id = '. static::tableName().'.id AND ' . static::propertyAlias() . '.' . static::uniqueField() .' = '. static::uniqueFieldValue());
        /**
         * Выборка дефолтных свойств
         */
        $query->leftJoin($m::tableName() . " d", 'd.entity_id = '. static::tableName().'.id AND d.default = 1');

        return $query;
    }

    /**
     * Возвращает оригинальный find()
     *
     * @return ActiveQuery
     */
    public static function clearFind()
    {
        return parent::find();
    }

    /**
     * Подставляем в атрибуты поля из свойств
     * @return array
     * @throws Exception
     */
    public function attributes()
    {
        $attributes = parent::attributes();
        $propertyModelClass = static::getPropertyModelClass();
        $propertyModel = new $propertyModelClass();
        $propertyAttributes = $propertyModel->attributes();
        $propertyAttributes = array_flip($propertyAttributes);
        foreach (static::excludedPropertyFields() as $field){
            if ($field == static::uniqueField()){
                continue;
            }

            unset ($propertyAttributes[$field]) ;
        }

        $propertyAttributes = array_flip($propertyAttributes);

        return ArrayHelper::merge($attributes, $propertyAttributes);
    }

    /**
     * Перед сохранением удаляем из атрибутов свойства
     *
     * @param bool $runValidation
     * @param null $attributeNames
     * @return mixed
     * @throws Exception
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        if (! $attributeNames) {
            $attributeNames = $this->attributes();
            $propertyModelClass = static::getPropertyModelClass();
            $propertyModel = new $propertyModelClass();
            $propertyAttributes = $propertyModel->attributes();
            $attributeNames = array_diff($attributeNames, $propertyAttributes);
        }

        return parent::save($runValidation, $attributeNames);
    }

    /**
     * метод должен вызываться в afterDelete модели для сохранения свойств
     *
     *       public function afterSave($insert, $changedAttributes)
     *       {
     *           $this->saveProperty($insert, $changedAttributes);
     *
     *           return parent::afterSave($insert, $changedAttributes);
     *       }
     *
     * @param $insert
     * @param $changedAttributes
     * @return bool
     * @throws Exception
     */
    public function saveProperty($insert, $changedAttributes)
    {
        $uniqueField = static::uniqueField();
        $propertyClass = static::getPropertyModelClass();
        $property = $propertyClass::find()->where([$uniqueField => $this->{$uniqueField}, 'entity_id' => $this->id])->one();
        if (! $property){
            $property = new $propertyClass();
            $property->entity_id = $this->id;
            if ($insert){
                $property->default = 1;
            }

            $property->{$uniqueField} = static::uniqueFieldValue();
        }

        foreach ($property->attributes() as $attribute){
            if (in_array($attribute, static::excludedPropertyFields())){
                continue;
            }

            if ($property->{$attribute} == $this->{$attribute}){
                continue;
            }

            $property->{$attribute} = $this->{$attribute};
        }

        if(!$property->save()){
            throw new Exception("property not saved");
        }

        return true;
    }

    /**
     * метод должен вызываться в beforeDelete модели для удаления свойств
     *
     *   public function beforeDelete()
     *   {
     *       $this->deleteProperties();
     *       return parent::beforeDelete();
     *   }
     *
     */
    public function deleteProperties()
    {
        $propertyClass = static::getPropertyModelClass();
        $propertyClass::deleteAll([
            'entity_id' => $this->id
        ]);
    }

    /**
     * метод для получения модели с свойствами для доменов
     * модель локализации должна иметь такое же имя с постфиксом Domain
     *
     * @return string
     */
    public static function getPropertyModelClass()
    {
        $class = static::class;
        $class = str_replace("search", "models", $class);
        $class = str_replace("Search", "", $class);

        return $class."Property";
    }

    /**
     * Добавляет в DataProvider сортировку по доменным атрибутам
     *
     * @param ActiveDataProvider $dataProvider
     * @param $attribute
     */
    protected function addSortByPropertyAttribute(ActiveDataProvider $dataProvider, $attribute)
    {
        $this->addSortByRelatedAttribute($dataProvider, static::propertyAlias(), $attribute);
    }

    /**
     * Возвращает текущий домен
     *
     * @return ActiveQuery
     * @throws Exception
     */
    public function getProperty()
    {
        $propertyClass = static::getPropertyModelClass();
        return $this->hasOne($propertyClass::className(), ['entity_id' => 'id'])
            ->alias(static::propertyAlias())
            ->andOnCondition([static::propertyAlias() . '.' . static::uniqueField() => static::uniqueFieldValue()]);
    }

    /**
     * Все локализации
     *
     * @return ActiveQuery
     */
    public function getProperties()
    {
        $propertyClass = static::getPropertyModelClass();

        return $this->hasMany($propertyClass, ['entity_id' => 'id']);
    }
}