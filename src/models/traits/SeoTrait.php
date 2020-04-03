<?php

namespace concepture\yii2logic\models\traits;

use Yii;
use concepture\yii2logic\validators\SeoNameValidator;
use concepture\yii2logic\validators\TranslitValidator;

/**
 * Трейт для расширения seo  моделей
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
trait SeoTrait
{
    /**
     * Правила валидации по умолчанию
     *
     * @return array
     */
    public function seoRules()
    {
        return [
                    [
                        [
                            'seo_title',
                            'seo_description',
                            'seo_keywords',
                        ],
                        'string',
                        'max'=>175
                    ],
                    [
                        [
                            'seo_name',
                        ],
                        SeoNameValidator::class
                    ],
                    [
                        [
                            'seo_name',
                        ],
                        TranslitValidator::class,
                        'source' => $this->getSeoTranslationSource(),
                    ],
        ];
    }

    /**
     * Метки атрибутов
     *
     * @return array
     */
    public function seoAttributeLabels()
    {
        return [
            'seo_name' => Yii::t('core','SEO имя'),
            'seo_title' => Yii::t('core','Title'),
            'seo_description' => Yii::t('core','Description'),
            'seo_keywords' => Yii::t('core','Keywords'),
        ];
    }

    /**
     * @return string
     */
    protected function getSeoTranslationSource()
    {
        return 'header';
    }
}