<?php
namespace concepture\yii2logic\models;

use Throwable;
use yii\db\ActiveRecord as Base;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;

/**
 * Class ActiveRecord
 * @package cconcepture\yii2logic\models
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class ActiveRecord extends Base
{
    /**
     * Creates data provider instance with search query applied
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
     * Аттрибут модели который будет использован для ключа в выпадающих списках
     *
     * @return string
     */
    public static function getListSearchKeyAttribute()
    {
        return null;
    }

    /**
     * Аттрибут модели который будет использован для метки в выпадающих списках
     *
     * @return string
     */
    public static function getListSearchAttribute()
    {
        return null;
    }

    /**
     * @param ActiveQuery $query
     */
    protected function extendQuery(ActiveQuery $query){}

    /**
     * @param ActiveDataProvider $dataProvider
     */
    protected function extendDataProvider(ActiveDataProvider $dataProvider){}

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