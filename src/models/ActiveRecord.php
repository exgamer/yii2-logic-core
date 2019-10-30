<?php
namespace concepture\yii2logic\models;

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
    /**
     * Возвращает DataProvider с учетом параметров
     *
     * Пример использования из concepture\yii2logic\services\traits\ReadTrait.php
     *
     *   public function getDataProvider($queryParams = [])
     *   {
     *       $searchClass = $this->getRelatedSearchModelClass();
     *       $searchModel = new $searchClass();
     *
     *       return $searchModel->search($queryParams);
     *   }
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = static::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            $query->where('0=1');
            return $dataProvider;
        }
        $this->extendQuery($query);
        $this->extendDataProvider($dataProvider);

        return $dataProvider;
    }

    /**
     * Метод для расширения ActiveQuery
     * используетсяв Search модели
     *
     * Пример
     *   protected function extendQuery(ActiveQuery $query)
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
    protected function extendQuery(ActiveQuery $query){}

    /**
     * Метод для расширения DataProvider
     * используетсяв Search модели
     *
     * Пример
     *   protected function extendDataProvider(ActiveDataProvider $dataProvider)
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
    protected function extendDataProvider(ActiveDataProvider $dataProvider){}

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
     * @return string
     */
    public static function getListSearchAttribute()
    {
        return null;
    }

    /**
     * @param bool $runValidation
     * @param null $attributeNames
     * @return mixed
     * @throws Throwable
     */
    public function save($runValidation = true, $attributeNames = null)
    {
        $transaction = $this->getDb()->beginTransaction();
        try {
            $r = parent::save($runValidation, $attributeNames);
            $transaction->commit();

            return $r;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }
}