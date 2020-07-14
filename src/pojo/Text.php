<?php

namespace concepture\yii2logic\pojo;

/**
 * Модель для данных строкой
 *
 * Class Social
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class Text extends Pojo
{
    public $content;

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [
                [
                    'content',
                ],
                'required'
            ],
            [
                [
                    'content',
                ],
                'string'
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'content' => \Yii::t('core','Текст'),
        ];
    }
}