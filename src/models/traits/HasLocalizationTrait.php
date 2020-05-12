<?php
namespace concepture\yii2logic\models\traits;

use concepture\yii2logic\converters\LocaleConverter;
use yii\base\InvalidConfigException;
use yii\data\ActiveDataProvider;
use concepture\yii2logic\db\ActiveQuery;
use Yii;
use yii\helpers\ArrayHelper;

/**
 *
 * Треит для моделей у которых есть таблица с локализированными свойствами
 *
 * Trait HasLocalizationTrait
 * @package concepture\yii2logic\models\traits
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
trait HasLocalizationTrait
{
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

        $propModelClass = static::getLocalizationModelClass();
        $propModel = Yii::createObject($propModelClass);
        $column = $propModel->getTableSchema()->getColumn($attribute);
        if ($column) {
            return $column->dbType;
        }

        throw new \yii\db\Exception("table or property table not have field " . $attribute);
    }

    /**
     * переменная для установки языка модели
     * по умолчнию в запрос будет посдтавлен язык приложения
     * для изменения нужно изменить значение $current_locale
     *
     * Пример:
     *   $originModelClass = $this->getService()->getRelatedModelClass();
     *   $originModelClass::setLocale($locale)
     *   $originModel = $originModelClass::find()->where(['id' => $id])->one();
     *
     * @var mixed
     */
    public static $current_locale;

    /**
     * Возвращает конвертер для локали
     * с помощью этого можно управлять типом данных аттрибута локаль сущности
     *
     * @return string
     */
    public static function getLocaleConverterClass()
    {
        return LocaleConverter::class;
    }

    /**
     * Установка локали
     *
     * @param $locale
     */
    public static function setLocale($locale)
    {
        static::$current_locale = $locale;
    }

    /**
     * Возвращает текущий язык модели
     * локаль для запросов нужно плучать имеено с помощью этого метода
     * т.к. он учитывает правила конвертации
     *
     * @return string
     */
    public static function currentLocale()
    {
        $localeConverterClass = static::getLocaleConverterClass();
        if (static::$current_locale === null){
            return $localeConverterClass::key(Yii::$app->language);
        }

        return $localeConverterClass::key(static::$current_locale);
    }


    /**
     * Возвращает массив с существующими локализациями
     * @param bool $flip
     * @return array
     */
    public function locales($flip = false)
    {
        $localeConverterClass = static::getLocaleConverterClass();
        $l = [];
        if (isset($this->localizations)){
            foreach ($this->localizations as $locale){
                $l[$localeConverterClass::key($locale->{static::uniqueField()})] = $localeConverterClass::value($locale->{static::uniqueField()});
            }
        }
        if ($flip){

            return array_flip($l);
        }

        return $l;
    }

    /**
     * Возвращает альяс для локализации
     *
     * @return string
     */
    public static function localizationAlias()
    {
        return "p";
    }

    /**
     * Переопределяем find чтобы подцепить локализации
     *
     * @return ActiveQuery
     */
    public static function find()
    {
        $query = Yii::createObject(ActiveQuery::class, [get_called_class()]);
        $m = static::getLocalizationModelClass();
        $query->select([static::tableName(). ".*", static::localizationAlias() . '.*']);
        $query->innerJoin($m::tableName() . " ". static::localizationAlias(), static::localizationAlias() . '.entity_id = '. static::tableName().'.id');
        $query->andWhere([static::localizationAlias() . '.' . static::uniqueField() => static::currentLocale()]);

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
        $locModelClass = static::getLocalizationModelClass();
        $locModel = new $locModelClass();
        $locAttributes = $locModel->attributes();
        $locAttributes = array_flip($locAttributes);
        unset ($locAttributes['entity_id']) ;
        $locAttributes = array_flip($locAttributes);

        return ArrayHelper::merge($attributes, $locAttributes);
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
            $locModelClass = static::getLocalizationModelClass();
            $locModel = new $locModelClass();
            $locAttributes = $locModel->attributes();
            $attributes = array_diff($attributes, $locAttributes);
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
            $locModelClass = static::getLocalizationModelClass();
            $locModel = new $locModelClass();
            $locAttributes = $locModel->attributes();
            $attributeNames = array_diff($attributeNames, $locAttributes);
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
     * Возвращает текущую локализацию
     *
     * @return ActiveQuery
     */
    public function getLocalization()
    {
        $locClass = static::getLocalizationModelClass();
        return $this->hasOne($locClass::className(), ['entity_id' => 'id'])
            ->alias('p')
            ->andOnCondition(['p.' . static::uniqueField() => static::currentLocale()]);
    }

    /**
     * Все локализации
     *
     * @return ActiveQuery
     */
    public function getLocalizations()
    {
        $locClass = static::getLocalizationModelClass();

        return $this->hasMany($locClass, ['entity_id' => 'id']);
    }

    /**
     * метод должен вызываться в afterDelete модели для сохранения локализаций
     *
     *       public function afterSave($insert, $changedAttributes)
     *       {
     *           $this->saveLocalizations();
     *
     *           return parent::afterSave($insert, $changedAttributes);
     *       }
     *
     * @return bool
     */
    public function saveLocalizations()
    {
        $locClass = static::getLocalizationModelClass();
        $localization = $locClass::find()->where([static::uniqueField() => $this->{static::uniqueField()}, 'entity_id' => $this->id])->one();
        if (! $localization){
            $localization = Yii::createObject($locClass);
            $localization->entity_id = $this->id;
        }

        foreach ($localization->attributes() as $attribute){
            if (in_array($attribute, ['id', 'entity_id'])){
                continue;
            }

            if ($localization->{$attribute} == $this->{$attribute}){
                continue;
            }

            $localization->{$attribute} = $this->{$attribute};
        }

        if(! $localization->save()) {
            throw new \Exception("localization not saved");
        }

        return true;
    }

    /**
     * метод должен вызываться в beforeDelete модели для удаления локализаций
     *
     *   public function beforeDelete()
     *   {
     *       $this->deleteLocalizations();
     *       return parent::beforeDelete();
     *   }
     *
     */
    public function deleteLocalizations()
    {
        $locClass = static::getLocalizationModelClass();
        $locClass::deleteAll([
            'entity_id' => $this->id
        ]);
    }

    /**
     * Возвращает массив с локализациями
     *
     * Модель локализации
     * @param $localization
     *
     * если false возвращает значения из текущей модели
     * если true возвращает значения из связи с локализацией
     * @param bool $fromLocalized
     * @return array
     */
    public function getLocalized($localization = null, $fromLocalized = false)
    {
        if (! $localization){
            $localization = $this->localization;
        }

        $data = [];
        if (!$localization){

            return $data;
        }

        foreach ($localization->attributes as $f => $v){
            if (in_array($f, ['id', 'entity_id'])){
                continue;
            }

            if ($fromLocalized){
                $data[$f] = $v;

                continue;
            }
            $data[$f] = $this->{$f};
        }

        return $data;
    }

    /**
     * метод для получения модели с локализациями
     * модель локализации должна иметь такое же имя с постфиксом Localization
     *
     * @return string
     */
    public static function getLocalizationModelClass()
    {
        $class = static::class;
        $class = str_replace("search", "models", $class);
        $class = str_replace("Search", "", $class);

        return $class."Localization";
    }

    /**
     * Добавляет в DataProvider сортировку по локализованным атрибутам
     *
     * @param ActiveDataProvider $dataProvider
     * @param $attribute
     */
    protected function addSortByLocalizationAttribute(ActiveDataProvider $dataProvider, $attribute)
    {
        $this->addSortByRelatedAttribute($dataProvider, "p", $attribute);
    }

    /**
     * @return string
     */
    public static function uniqueField()
    {
        return 'locale';
    }
}