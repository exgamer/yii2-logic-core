<?php

namespace concepture\yii2logic\models\traits\v2\property;

use concepture\yii2logic\db\HasPropertyActiveQuery;
use Exception;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use Yii;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\db\ActiveQuery;

/**
 * Треит для работы с данными у которых свойства хранятся в другой таблице
 *
 * Trait HasPropertyTrait
 * @package concepture\yii2logic\models\traits
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
trait HasPropertyTrait
{
    public function beforeSave($insert)
    {
        if (! $insert && $this->isAnyAttributeChanged()) {
            $this->setUpdatedAt();
        }

        return parent::beforeSave($insert);
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->saveProperty($insert, $changedAttributes);

        return parent::afterSave($insert, $changedAttributes);
    }

    public function beforeDelete()
    {
        $this->deleteProperties();

        return parent::beforeDelete();
    }

    /**
     * Возвращает тип данных атрибута из базы с учетом пропертей
     * @param $attribute
     * @return mixed
     * @throws InvalidConfigException
     * @throws \yii\db\Exception
     */
    public function getAttrDbType($attribute)
    {
        $column = $this->getTableSchema()->getColumn($attribute);
        if ($column) {
            return $column->dbType;
        }

        $propModelClass = static::getPropertyModelClass();
        $propModel = Yii::createObject($propModelClass);
        $column = $propModel->getTableSchema()->getColumn($attribute);
        if ($column) {
            return $column->dbType;
        }

        throw new \yii\db\Exception("table or property table not have field " . $attribute);
    }

    /**
     * Возвращает
     *
     * @return string
     */
    public static function linkedEntityIdFieldName()
    {
        return 'entity_id';
    }

    /**
     * Возвращает названия полей свойств, которые будут исключены при маппинге данных из основной модели
     *
     * @return array
     * @throws Exception
     */
    public static function excludedPropertyFields()
    {
        return ArrayHelper::merge([
            'id',
            static::linkedEntityIdFieldName(),
            'default',
        ], static::getUniqueFieldAsArray());
    }

    /**
     * Возвращает названия полей свойств, которые будут исключены при маппинге дефлотных данных из основной модели
     *
     * @return array
     * @throws Exception
     */
    public static function excludedPropertyDefaultValues()
    {
        return [];
    }

    /**
     * Загрузить проперти атрибуты в переданную модель
     *
     * @param $model
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function loadPropertyValuesToModel($model)
    {
        $propertyClass = static::getPropertyModelClass();
        $property = Yii::createObject($propertyClass);
        foreach ($property->attributes() as $attribute) {
            if (in_array($attribute, static::excludedPropertyFields())) {
                continue;
            }

            if (in_array($attribute, static::excludedPropertyDefaultValues())) {
                continue;
            }

            $model->{$attribute} = $this->{$attribute};
        }
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
     * Возвращает уникальное поле как массив
     *
     * @return array|string
     * @throws Exception
     */
    public static function getUniqueFieldAsArray()
    {
        $uniqueField = static::uniqueField();
        if (! is_array($uniqueField)) {
            $uniqueField = [$uniqueField];
        }

        return $uniqueField;
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
     * Возвращает дефолтное значение уникального поля проперти
     *
     * @param string|null $attribute
     * @return array|mixed|string
     * @throws Exception
     */
    public static function getUniqueFieldValue($attribute = null)
    {
        $val = static::uniqueFieldValue();
        if (! is_array($val)) {
            return $val;
        }

        if (! $attribute) {
            throw new Exception("attribute parameter not passed");
        }

        if (! isset($val[$attribute])) {
            return null;
        }

        return $val[$attribute];
    }

    /**
     * @return array|mixed
     * @throws Exception
     */
    public static function getUniqueData()
    {
        $val = static::uniqueField();
        if (is_array($val)) {
            return static::uniqueFieldValue();
        }

        return [
            static::uniqueField() => static::uniqueFieldValue()
        ];
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

    public static function defaultPropertyAlias()
    {
        return "d";
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
        $defaultPropertyAlias = static::defaultPropertyAlias();
        $propertyAlias = static::propertyAlias();
        $propertyClass = static::getPropertyModelClass();
        $property = Yii::createObject($propertyClass);
        $result = [];
        foreach ($property->attributes() as $attribute) {
            if (in_array($attribute, static::excludedPropertyFields())) {
                continue;
            }

            if (in_array($attribute, static::excludedPropertyDefaultValues())) {
                $result[] = static::propertyAlias() . "." . $attribute;
                continue;
            }

            $result[] = new Expression("CASE 
                               WHEN {$propertyAlias}.{$attribute} IS NULL
                                   THEN {$defaultPropertyAlias}.{$attribute}
                                   ELSE
                                       {$propertyAlias}.{$attribute}
                                       END as {$attribute}");
        }

        $uniqueFields = static::getUniqueFieldAsArray();
        foreach ($uniqueFields as $field) {
            //Добавялем в выборку uniqueField
            $result[] = static::propertyAlias() . "." . $field;
        }


        return $result;
    }

    /**
     * Определяет будет ли в выборке только сущности с текущим уникальным полем
     * или если leftJoin то будут выбраны все записи и подставлены дефолтные значения
     *
     * @return string
     */
    protected static function getPropertyJoin()
    {
        return 'innerJoin';
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
        /**
         * @var ActiveQuery $query
         */
        $query = Yii::createObject(HasPropertyActiveQuery::class, [get_called_class()]);
        $m = static::getPropertyModelClass();
        $selectArray = static::constructPropertySelect();
        $selectArray[] = static::tableName() . ".*";
        $query->select($selectArray);
        //Выборка свойств для текущего uniqueField
        $uniVal = static::uniqueFieldValue();
        static::setPropertyJoinQuery($query, $uniVal);
        $defaultPropertyAlias = static::defaultPropertyAlias();
        /**
         * Выборка дефолтных свойств
         */
        $query->leftJoin($m::tableName() . " {$defaultPropertyAlias}", "{$defaultPropertyAlias}." . static::linkedEntityIdFieldName() . " = " . static::tableName() . ".id AND {$defaultPropertyAlias}.default = 1");
        static::extendFind($query);

        return $query;
    }

    /**
     * Дополнить $query без переопределния find()
     * @param ActiveQuery $query
     */
    public static function extendFind(ActiveQuery $query)
    {

    }

    /**
     * Установка join для выбора проперти
     *
     * @param $query
     * @param $uniqueValue
     * @throws Exception
     */
    public static function setPropertyJoinQuery($query, $uniqueValue)
    {
        $fields = static::getUniqueFieldAsArray();
        //если уникальных полей больше 1 то параметр $uniqueValue должен быть ассоциативным массивом
        if (count($fields) > 1 && ! ArrayHelper::isAssociative($uniqueValue)) {
            throw new Exception("model has multiple unique property fields. You must pass uniqueValue param as associative array");
        }

        // если уникальное поле 1 то делаем $uniqueValue ассоциативным массивом
        if (count($fields) == 1) {
            $tmp[$fields[0]] = $uniqueValue;
            $uniqueValue = $tmp;
        }

        $tmp = [];
        foreach ($uniqueValue as $attr => $value) {
            if (! is_array($value)){
                $value = [$value];
            }

            $tmp[$attr] = $value;
        }

        $uniqueValue = $tmp;
        $propertyJoin = static::getPropertyJoin();
        $m = static::getPropertyModelClass();
        if ($query->join) {
            foreach ($query->join as $key => $join) {
                if ($join[1] == $m::tableName() . " ". static::propertyAlias()) {

                    unset($query->join[$key]);
                    break;
                }
            }
        }

        //сбор массива для запроса
        $queryArray = [];
        foreach ($uniqueValue as $attr => $value) {
            $cleanVals = [];
            foreach ($value as $val){
                $cleanVals[] = (int) $val;
            }

            $queryArray[] = static::propertyAlias() . '.' . $attr . ' IN (' . implode(",", $cleanVals) . ")";
        }

        $query->{$propertyJoin}($m::tableName() . " ". static::propertyAlias(),
            static::propertyAlias() . '.' . static::linkedEntityIdFieldName() . ' = '. static::tableName().'.id AND '
            . implode(' AND ', $queryArray));
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
        $propertyModel = Yii::createObject($propertyModelClass);
        $propertyAttributes = $propertyModel->attributes();
        $propertyAttributes = array_flip($propertyAttributes);
        foreach (static::excludedPropertyFields() as $field){
            if (in_array($field, static::getUniqueFieldAsArray())) {
                continue;
            }

            unset ($propertyAttributes[$field]) ;
        }

        $propertyAttributes = array_flip($propertyAttributes);

        return ArrayHelper::merge($attributes, $propertyAttributes);
    }

    /**
     *  Перед сохранением удаляем из атрибутов свойства
     *
     * @param bool $runValidation
     * @param null $attributes
     * @return bool
     * @throws \Throwable
     */
    public function insert($runValidation = true, $attributes = null)
    {
        if (! $attributes) {
            $attributes = $this->attributes();
            $propertyModelClass = static::getPropertyModelClass();
            $propertyModel = Yii::createObject($propertyModelClass);
            $propertyAttributes = $propertyModel->attributes();
            $attributes = array_diff($attributes, $propertyAttributes);
        }

        return parent::insert($runValidation, $attributes);
    }

    /**
     *  Перед сохранением удаляем из атрибутов свойства
     *
     * @param bool $runValidation
     * @param null $attributeNames
     * @return bool
     * @throws \Throwable
     */
    public function update($runValidation = true, $attributeNames = null)
    {
        if (! $attributeNames) {
            $attributeNames = $this->attributes();
            $propertyModelClass = static::getPropertyModelClass();
            $propertyModel = Yii::createObject($propertyModelClass);
            $propertyAttributes = $propertyModel->attributes();
            $attributeNames = array_diff($attributeNames, $propertyAttributes);
        }

        return parent::update($runValidation, $attributeNames);
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
        $uniqueFields = static::getUniqueFieldAsArray();
        $propertyClass = static::getPropertyModelClass();
        $propertyM = Yii::createObject($propertyClass);
        $propertyCondition = [];
        foreach ($uniqueFields as $field) {
            if (! $this->{$field}) {
                $this->{$field} = static::getUniqueFieldValue($field);
            }

            if (! $propertyM->hasAttribute($field)) {
                throw  new Exception('property table must have `' . $field . '` field');
            }

            $propertyCondition[$field] = $this->{$field};
        }

        $entityIdFieldName = static::linkedEntityIdFieldName();
        if (! $propertyM->hasAttribute($entityIdFieldName)) {
            throw  new Exception('property table must have `' .$entityIdFieldName. '` field');
        }

        $propertyCondition[$entityIdFieldName] = $this->id;
        $property = $propertyM::find()->where($propertyCondition)->one();
        if (! $property){
            $property = Yii::createObject($propertyClass);
            $property->{$entityIdFieldName} = $this->id;
            if ($insert){
                if (! $property->hasAttribute("default")) {
                    throw  new Exception('property table must have `default` field');
                }

                $property->default = 1;
            }

            foreach ($uniqueFields as $field) {
                if (! $property->{$field}) {
                    $property->{$field} = $this->{$field};
                }
            }
        }else {
            if ($this->isAnyAttributeChanged()) {
                $property->setUpdatedAt();
            }

            if ($property->hasAttribute("is_deleted")) {
                $property->is_deleted = 0;
            }
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

        if(! $property->save()){
            throw new Exception("property not saved");
        }

        $this->afterPropertySave($property);

        return true;
    }

    public function afterPropertySave($property){}

    /**
     * Зачищает все атрибуты кроме исключений excludedPropertyFields
     *
     * @throws Exception
     */
    public function clearPropertyAttributes($clearUniqueAttribute = false, $exclude = [])
    {
        $propertyClass = static::getPropertyModelClass();
        $propertyModel = Yii::createObject($propertyClass);
        $attributes = $propertyModel->attributes();
        foreach ($attributes as $attribute){
            if (in_array($attribute, static::excludedPropertyFields())){
                continue;
            }

            if (in_array($attribute, $exclude)){
                continue;
            }

            $this->{$attribute} = null;
        }

        if ($clearUniqueAttribute){
            $uAttrs = static::getUniqueFieldAsArray();
            foreach ($uAttrs as $uAttr) {
                $this->{$uAttr} = null;
            }
        }
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
        $entityIdFieldName = static::linkedEntityIdFieldName();
        $propertyClass::deleteAll([
            $entityIdFieldName => $this->id
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
        $condition = [];
        $data = static::getUniqueData();
        foreach ($data as $field => $val) {
            $condition[static::propertyAlias() . '.' . $field] = $val;
        }

        return $this->hasOne($propertyClass::className(), [static::linkedEntityIdFieldName() => 'id'])
            ->alias(static::propertyAlias())
            ->andOnCondition($condition);
    }

    /**
     * Все локализации
     *
     * @return ActiveQuery
     */
    public function getProperties()
    {
        $propertyClass = static::getPropertyModelClass();
        $query =  $this->hasMany($propertyClass, [static::linkedEntityIdFieldName() => 'id']);
        $query->where = [];
        $field = static::uniqueField();
        if (! is_array($field)) {
            $query->indexBy(static::uniqueField());
        }

        return $query;
    }

    /**
     * Возвращает unique id всех properties
     * @return array
     * @throws Exception
     */
    public function getPropertiesUniqueValues($attribute = null)
    {
        $fields = static::getUniqueFieldAsArray();
        $properties = [];
        if (isset($this->properties)){
            $properties = $this->properties;
        }

        if (empty($properties)){
            return $properties;
        }

        $data = [];
        foreach ($properties as $property) {
            foreach ($fields as $field) {
                $data[$field][] = $property->{$field};
            }
        }

        $tmp = [];
        foreach ($data as $attr => $val) {
            $tmp[$attr] = array_unique($val);
        }

        if (count($tmp) > 1) {
            if ($attribute){
                return $tmp[$attribute];
            }

            return $tmp;
        }

        return array_shift($tmp);
    }

    /**
     * Возвращает properties по id сущности
     *
     * @param integer|integer[]|callable|[] $id
     * @param callable|array $condition
     * @return mixed
     */
    public static function findProperties($id, $condition = null)
    {
        $propertyClass = static::getPropertyModelClass();

        $query = $propertyClass::find();

        if (filter_var($id, FILTER_VALIDATE_INT) !== false || (is_array($id) && ! ArrayHelper::isAssociative($id))) {
            $query->andWhere([static::linkedEntityIdFieldName() => $id]);
        }elseif (is_callable($id)){
            call_user_func($id, $query);
        }elseif (is_array($id)){
            $query->andWhere($id);
        }

        if (is_callable($condition)){
            call_user_func($condition, $query);
        }

        if (is_array($condition)){
            foreach ($condition as $name => $value){
                $query->andWhere([$name => $value]);
            }
        }

        return $query->all();
    }
}