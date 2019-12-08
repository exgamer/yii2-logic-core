<?php
namespace concepture\yii2logic\models\traits;

use concepture\yii2logic\converters\LocaleConverter;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use Yii;

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
     * Возвращает анонимку для расширения выборки по локализации
     *
     *   static::$search_by_locale_callable = function($q, $localizedAlias){
     *       $q->andFilterWhere(['like', "{$localizedAlias}.seo_name", $this->seo_name]);
     *       $q->andFilterWhere(['like', "{$localizedAlias}.title", $this->title]);
     *   };
     *
     * @var callable|null
     */
    public static $search_by_locale_callable;

    /**
     * true означает что поиск будет вестись жестко по языку
     * false означает что будет искаться запись, но язык необязателен
     * @var bool
     */
    public static $by_locale_hard_search = true;

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
     * Включает поиск будет вестись жестко по языку
     */
    public static function enableLocaleHardSearch()
    {
        static::$by_locale_hard_search = true;
    }

    /**
     * Отключает поиск будет вестись жестко по языку
     */
    public static function disableLocaleHardSearch()
    {
        static::$by_locale_hard_search = false;
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
     * Поиск по локализациям
     *
     * @param $callable
     */
    public static function searchByLocalization($callable)
    {
        static::$search_by_locale_callable = $callable;
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
                $l[$localeConverterClass::key($locale->locale)] = $localeConverterClass::value($locale->locale);
            }
        }
        if ($flip){

            return array_flip($l);
        }

        return $l;
    }

    /**
     * Переопределяем find чтобы подцепить локализации
     *
     * @return ActiveQuery
     */
    public static function find()
    {
//        $query = Yii::createObject(LocalizedActiveQuery::className(), [get_called_class()]);
        $query = Yii::createObject(ActiveQuery::className(), [get_called_class()]);
        $query->with('localizations');
        $joinType = "with";
        /**
         * Если true то поиск ведем жеско по языку и доп условиям
         */
        if (static::$by_locale_hard_search) {
            $joinType = "innerJoinWith";
        }
        $query->{$joinType}([
            'localization' => function ($q) {
                $callable = static::$search_by_locale_callable;
                if (is_callable($callable)) {
                    call_user_func($callable, $q, "p");
                }
            }
        ]);


        return $query;
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
            ->andOnCondition(['p.locale' => static::currentLocale()]);
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
        $localization = $locClass::find()->where(['locale' => $this->locale, 'entity_id' => $this->id])->one();
        if (! $localization){
            $localization = new $locClass();
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

        if(!$localization->save()){
            throw new Exception("localization not saved");
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
     * метод должен вызываться в afterFind модели для удаления локализаций
     *
     *   public function afterFind()
     *   {
     *       $this->setLocalizations();
     *       return parent::afterFind();
     *   }
     *
     */
    public function setLocalizations()
    {
        if (isset($this->localization)){
            $localizationData = $this->getLocalized($this->localization, true);
            foreach ($localizationData as $attribute => $value){
                $this->{$attribute} = $value;
            }
        }
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
}