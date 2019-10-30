<?php
namespace concepture\yii2logic\models\traits;

use yii\db\ActiveQuery;

/**
 *
 * Треит для моделей у которых есть таблица с деревом
 *
 * Trait HasLocalizationTrait
 * @package concepture\yii2logic\models\traits
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
trait HasLocalizationTrait
{
    /**
     * аттрибут локали c с учетом которого будет получаться текущая локализация
     *
     * @var string
     */
    private $_current_locale = "ru";

    /**
     * Возвращает текущую локаль модели
     *
     * @return string
     */
    public function getCurrentLocale()
    {
        return $this->_current_locale;
    }

    /**
     * Устанавливает текущую локаль модели
     *
     * @param string $current_locale
     */
    public function setCurrentLocale($current_locale)
    {
        $this->_current_locale = $current_locale;
    }

    /**
     * Переопределяем find чтобы подцепить локализации
     *
     * @param string $type
     * @param string $localizedAlias
     * @return ActiveQuery
     */
    public static function find($type = "joinWith", $localizedAlias = "p")
    {
        $query = parent::find();
        $query->with('localizations');
        static::searchByLocalized($query, null, $type, $localizedAlias);

        return $query;
    }

    /**
     * Метод для расширения поиска по локализациям
     *
     * @param $query
     * @param null $callable
     * @param string $type
     * @param string $localizedAlias
     */
    public static function searchByLocalized($query, $callable = null, $type = "joinWith", $localizedAlias = "p")
    {
        $query->{$type}([
            'localization' => function ($q) use ($callable, $localizedAlias) {
                $q->on = null;
                $propModelClass = static::getLocalizationModelClass();
                $q->from($propModelClass::tableName()." {$localizedAlias}");
                $q->andWhere(["{$localizedAlias}.locale" => static::getLocalizationLocale()]);
                if (is_callable($callable)){
                    call_user_func($callable, $q, $localizedAlias);
                }
            }
        ]);
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
            ->andOnCondition([$locClass::tableName().'.locale' => $this->getCurrentLocale()]);
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
     *           $this->saveProps();
     *
     *           return parent::afterSave($insert, $changedAttributes);
     *       }
     *
     * @return bool
     */
    public function saveLocalizations()
    {
        $locClass = static::getLocalizationModelClass();
        $localization = $locClass::find()->where(['locale' => $this->getCurrentLocale(), 'entity_id' => $this->id])->one();
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

        return $class."Localization";
    }
}

