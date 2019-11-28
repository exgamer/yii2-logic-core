<?php
namespace concepture\yii2logic\models;

/**
 * Модель линкушки
 * @property integer(bigint) entity_id - идентификатор основной сущности
 * @property integer(bigint) linked_id - идентификатор привязанной сущности
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class LinkActiveRecord extends ActiveRecord
{
    public static function primaryKey()
    {
        return ['entity_id','linked_id'];
    }

    public function rules()
    {
        return [
            [
                [
                    'entity_id',
                    'linked_id'
                ],
                'required'
            ],
            [
                [
                    'entity_id',
                    'linked_id'
                ],
                'integer'
            ]
        ];
    }
}