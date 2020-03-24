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
        $property = Yii::createObject($propertyClass);
        $result = [];
        foreach ($property->attributes() as $attribute){
            if (in_array($attribute, static::excludedPropertyFields())){
                continue;
            }

            if (in_array($attribute, static::excludedPropertyDefaultValues())){
                $result[] = static::propertyAlias() . "." . $attribute;
                continue;
            }

            $result[] = new Expression("CASE 
                               WHEN {$propertyAlias}.{$attribute} IS NULL
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
        $query = Yii::createObject(ActiveQuery::class, [get_called_class()]);
        $m = static::getPropertyModelClass();
        $selectArray = static::constructPropertySelect();
        $selectArray[] = static::tableName(). ".*";
        $query->select($selectArray);
        /**
         * Выборка свойств для текущего uniqueField
         */
        $uniVal = static::uniqueFieldValue();
        if (! is_array($uniVal)){
            $uniVal = [$uniVal];
        }

        $cleanVals = [];
        foreach ($uniVal as $val){
            $cleanVals[] = (int) $val;
        }

        $propertyJoin = static::getPropertyJoin();
        $query->{$propertyJoin}($m::tableName() . " ". static::propertyAlias(),
            static::propertyAlias() . '.entity_id = '. static::tableName().'.id AND '
            . static::propertyAlias() . '.' . static::uniqueField() .' IN ('. implode(",", $uniVal) .")");
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
        $propertyModel = Yii::createObject($propertyModelClass);
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
     *  Перед сохранением удаляем из атрибутов свойства
     *
     * @param bool $runValidation
     * @param null $attributes
     * @return bool
     * @throws \Throwable
     */
    public function insert($runValidation = true, $attributes = null)
    {
        if ($runValidation && !$this->validate($attributes)) {
            Yii::info('Model not inserted due to validation error.', __METHOD__);
            return false;
        }

        if (! $attributes) {
            $attributes = $this->attributes();
            $propertyModelClass = static::getPropertyModelClass();
            $propertyModel = Yii::createObject($propertyModelClass);
            $propertyAttributes = $propertyModel->attributes();
            $attributes = array_diff($attributes, $propertyAttributes);
        }

        if (!$this->isTransactional(self::OP_INSERT)) {
            return $this->insertInternal($attributes);
        }

        $transaction = static::getDb()->beginTransaction();
        try {
            $result = $this->insertInternal($attributes);
            if ($result === false) {
                $transaction->rollBack();
            } else {
                $transaction->commit();
            }

            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
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
        if ($runValidation && !$this->validate($attributeNames)) {
            return false;
        }

        if (! $attributeNames) {
            $attributeNames = $this->attributes();
            $propertyModelClass = static::getPropertyModelClass();
            $propertyModel = Yii::createObject($propertyModelClass);
            $propertyAttributes = $propertyModel->attributes();
            $attributeNames = array_diff($attributeNames, $propertyAttributes);
        }

        if (!$this->isTransactional(self::OP_UPDATE)) {
            return $this->updateInternal($attributeNames);
        }

        $transaction = static::getDb()->beginTransaction();
        try {
            $result = $this->updateInternal($attributeNames);
            if ($result === false) {
                $transaction->rollBack();
            } else {
                $transaction->commit();
            }

            return $result;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
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
        $propertyM = Yii::createObject($propertyClass);
        if (! $this->{$uniqueField}) {
            $this->{$uniqueField} = static::uniqueFieldValue();
        }

        $property = $propertyM::find()->where([$uniqueField => $this->{$uniqueField}, 'entity_id' => $this->id])->one();
        if (! $property){
            $property = Yii::createObject($propertyClass);
            $property->entity_id = $this->id;
            if ($insert){
                $property->default = 1;
            }

            if (! $property->{$uniqueField}) {
                $property->{$uniqueField} = static::uniqueFieldValue();
            }
        }else {
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

        if(!$property->save()){
            throw new Exception("property not saved");
        }

        return true;
    }

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
            $uAttr = static::uniqueField();
            $this->{$uAttr} = null;
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