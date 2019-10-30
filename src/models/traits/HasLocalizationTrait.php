<?php
namespace concepture\yii2logic\models\traits;

use yii\db\ActiveQuery;

/**
 * @deprecated UNDER DEVELOPMENT
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
     * Переопределяем find чтобы подцепить локализации
     *
     * @param string $type
     * @return ActiveQuery
     */
    public static function find($type = "joinWith")
    {
        $query = parent::find();
        $query->with('localizations');
        static::searchByLocalized($query, null, $type);

        return $query;
    }

    /**
     * Метод для расширения поиска по локализациям
     *
     * @param $query
     * @param null $callable
     * @param string $type
     */
    public static function searchByLocalized($query, $callable = null, $type = "joinWith")
    {
        $query->{$type}([
            'localization' => function ($q) use ($callable) {
                $q->on = null;
                $propModelClass = static::getLocalizationModelClass();
                $q->from($propModelClass::tableName()." p");
                $q->andWhere(['p.locale' => static::getLocalizationLocale()]);
                if (is_callable($callable)){
                    call_user_func($callable, $q);
                }
            }
        ]);
    }

//    public function getLocalization()
//    {
//        $locClass = static::getLocalizationModelClass();
//        return $this->hasOne($locClass::className(), ['entity_id' => 'id'])
//            ->andOnCondition([$locClass::tableName().'.locale' => static::getLocalizationLocale()]);
//    }

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

