<?php
namespace concepture\yii2logic\models;

/**
 * Модель дерева
 * @property integer(bigint) parent_id - идентификатор родительского узла
 * @property integer(bigint) child_id - идентификатор дочернего узла
 * @property integer level - уровень
 * @property integer is_root - метка обозначающая вершину дерева
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class TreeActiveRecord extends ActiveRecord
{
    public static function primaryKey()
    {
        return ['parent_id','child_id','level'];
    }

    public function rules()
    {
        return [
            [
                [
                    'parent_id',
                    'child_id',
                    'level'
                ],
                'required'
            ],
            [
                [
                    'parent_id',
                    'child_id',
                    'level',
                    'is_root'
                ],
                'integer'
            ]
        ];
    }

}