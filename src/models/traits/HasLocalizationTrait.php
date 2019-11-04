<?php
namespace concepture\yii2logic\models\traits;

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
     *   $originModelClass::$current_locale = $locale;
     *   $originModel = $originModelClass::find()->where(['id' => $id])->one();
     *
     * @var string
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
     * Возвращает текущий язык модели
     *
     * @return string
     */
    public static function currentLocale()
    {
        if (static::$current_locale === null){
            return Yii::$app->language;
        }

        return static::$current_locale;
    }

    /**
     * Возвращает массив с существующими локализациями
     * @return array
     */
    public function locales()
    {
        $l = [];
        if (isset($this->localizations)){
            foreach ($this->localizations as $locale){
                $l[]= $locale->locale;
            }
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
        $query = parent::find();
        $query->with('localizations');
        $query->joinWith([
                'localization' => function ($q) {
                    $callable = static::$search_by_locale_callable;
                    if (is_callable($callable)){
                        call_user_func($callable, $q, "p");
                    }
                }
            ]
            ,
            true,
            "JOIN");

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
        $localization = $locClass::find()->where(['locale' => static::currentLocale(), 'entity_id' => $this->id])->one();
        if ($localization){
            $localization->load($this->getLocalized($localization), "");
            if(!$localization->save()){
                throw new Exception("localization not saved");
            }

            return false;
        }

        $localization = new $locClass();
        $localization->load($this->getLocalized($localization), '');
        $localization->entity_id = $this->id;
        if(!$localization->save()){
            throw new Exception("localization not saved");
        }

        return true;
    }

    /**
     * метод должен вызываться в afterDelete модели для удаления локализаций
     *
     *   public function afterDelete()
     *   {
     *       $this->deleteLocalizations();
     *       return parent::afterDelete();
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
            $this->load($this->getLocalized($this->localization, true), "");
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
    public function getLocalized($localization, $fromLocalized = false)
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
}