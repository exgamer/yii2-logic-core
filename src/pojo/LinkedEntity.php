<?php

namespace concepture\yii2logic\pojo;

/**
 * Class LinkedEntity
 * @package common\pojo
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class LinkedEntity extends Pojo
{
    public $link_id;
    public $name;
    public $status;
    public $sort;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'link_id',
                    'status',
                    'sort',
                ],
                'integer',
            ],
            [
                [
                    'name',
                ],
                'string',
            ],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'name' => \Yii::t('common','Наименование'),
            'status' => \Yii::t('common','Статус'),
            'sort' => \Yii::t('common','Сортировка'),
        ];
    }
}