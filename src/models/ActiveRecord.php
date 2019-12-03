<?php
namespace concepture\yii2logic\models;

use concepture\yii2logic\actions\traits\ModelScenarioTrait;
use concepture\yii2logic\models\traits\NonPhysicalDeleteTrait;
use concepture\yii2logic\models\traits\SearchTrait;
use Exception;
use Throwable;
use yii\db\ActiveRecord as Base;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * Базовая модель для сущности
 *
 * Class ActiveRecord
 * @package cconcepture\yii2logic\models
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class ActiveRecord extends Base
{
    use ModelScenarioTrait;
    use NonPhysicalDeleteTrait;
    use SearchTrait;

    /**
     * Врубаем транзакции по уолчнию для всех случаев модификации данных для сценария default
     * Для использования в стандартном методе AR   ::isTransactional($operation)
     *
     * @return array
     */
    public function transactions()
    {
        return [
            'default' => self::OP_ALL
        ];
    }

    /**
     * Добавляет в Дата провайдер сортировку по атрибуту связанной таблицы
     *
     * @param ActiveDataProvider $dataProvider
     * @param $attribute
     */
    protected function addSortByRelatedAttribute(ActiveDataProvider $dataProvider, $tableAlias, $attribute)
    {
        $dataProvider->sort->attributes[$attribute] = [
            'asc' => ["{$tableAlias}.{$attribute}" => SORT_ASC],
            'desc' => ["{$tableAlias}.{$attribute}" => SORT_DESC],
        ];
    }

    /**
     * Метод для расширения ActiveQuery
     * определяется в Search модели
     *
     * применяетсяв методе getDataProvider в concepture\yii2logic\services\traits\ReadTrait.php
     *
     *
     * Пример
     *   public function extendQuery(ActiveQuery $query)
     *   {
     *         $query->andFilterWhere([
     *            'id' => $this->id
     *         ]);
     *
     *         $query->andFilterWhere(['like', 'username', $this->username]);
     *   }
     *
     *
     * @param ActiveQuery $query
     */
    public function extendQuery(ActiveQuery $query){}

    /**
     * Метод для расширения DataProvider
     * используетсяв Search модели
     *
     * Пример
     *   public function extendDataProvider(ActiveDataProvider $dataProvider)
     *   {
     *       $dataProvider->sort->attributes['username'] = [
     *           'asc' => [User::tableName().'.username' => SORT_ASC],
     *           'desc' => [User::tableName().'.username' => SORT_DESC],
     *       ];
     *       $dataProvider->sort->attributes['caption'] = [
     *           'asc' => [UserRoleHandbook::tableName().'.caption' => SORT_ASC],
     *           'desc' => [UserRoleHandbook::tableName().'.caption' => SORT_DESC],
     *       ];
     *   }
     *
     * @param ActiveDataProvider $dataProvider
     */
    public function extendDataProvider(ActiveDataProvider $dataProvider){}

    /**
     * Аттрибут модели который будет использован для ключа в выпадающих списках
     * используетсяв Search модели
     *
     *
     * Пример
     *       public static function getListSearchKeyAttribute()
     *       {
     *           return 'id';
     *       }
     *
     *
     * @return string
     */
    public static function getListSearchKeyAttribute()
    {
        return null;
    }

    /**
     * Аттрибут модели который будет использован для метки в выпадающих списках
     * используетсяв Search модели
     *
     *   public static function getListSearchAttribute()
     *   {
     *       return 'username';
     *   }
     *
     *   таким способом можно вызывать методы модели
     *   например если в модели сделать метод  getLabel()
     *   если метод getListSearchAttribute вернет 'label'
     *   при работе методов сервисного треита CatalogTrait
     *   будет вызван метод getLabel() модели
     *
     * @return string
     */
    public static function getListSearchAttribute()
    {
        return null;
    }

    /**
     * Метод для тог очтобы можно было установить метку для сущности
     * @return string
     */
    public static function label()
    {
        return static::class;
    }

    /**
     * Метод для тог очтобы можно было установить метку для сущности
     * @return string
     */
    public function toString()
    {
        $name = static::getListSearchAttribute();
        if ($name){
            return $this->{$name};
        }
        
        return null;
    }
}