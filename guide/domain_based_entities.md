####Мини гайд по использованию сущностей которые имеют разный контент в зависимости от уникального поля на примере
####сущности разделенной по домену

# В этом случае данне будут собираться в зависимости от текущего domain_id


1. Создаем модель AR унаследованную от *concepture\yii2logic\models\ActiveRecord* и подключаем треит concepture\yii2logic\models\traits\v2\property\HasDomainPropertyTrait
   и реализовать поведения afterSave, beforeDelete


```php

<?php

namespace common\models;

use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2logic\models\traits\v2\property\HasDomainPropertyTrait;
use concepture\yii2logic\validators\SeoNameValidator;
use concepture\yii2logic\validators\v2\UniquePropertyValidator;
use kamaelkz\yii2cdnuploader\traits\ModelTrait;
use concepture\yii2logic\validators\MD5Validator;
use concepture\yii2logic\models\traits\StatusTrait;
use concepture\yii2logic\models\traits\IsDeletedTrait;
use concepture\yii2logic\validators\TranslitValidator;

/**
 * Class Bookmaker
 *
 * @package common\models
 * @author Poletaev Eugene <evgstn7@gmail.com>
 *
 * @property integer $id
 * @property integer $domain_id
 * @property string $name
 * @property string $content
 * @property string $seo_name
 * @property string $seo_name_md5_hash
 * @property string $seo_h1
 * @property string $seo_title
 * @property string $seo_description
 * @property string $seo_keywords
 * @property string $logo
 * @property string $logo_small
 * @property string $website
 * @property integer $bonus_sum
 * @property string $bonus_text
 * @property integer $weight
 * @property string $pros
 * @property string $cons
 * @property integer $status
 * @property string $created_at
 * @property string $updated_at
 * @property integer $is_deleted
 */
class Bookmaker extends ActiveRecord
{
    public $allow_physical_delete = false;

    use ModelTrait;
    use StatusTrait;
    use IsDeletedTrait;
    use HasDomainPropertyTrait;

    /**
     * @return string
     */
    public static function label(): string
    {
        return \Yii::t('common', 'Букмекер');
    }

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return '{{bookmaker}}';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'domain_id',
                    'bonus_sum',
                    'weight',
                    'status',
                    'is_deleted',
                ],
                'integer',
            ],
            [
                [
                    'name',
                    'seo_name',
                    'seo_name_md5_hash',
                    'seo_h1',
                    'seo_title',
                    'seo_description',
                    'seo_keywords',
                    'bonus_text',
                    'pros',
                    'cons',
                    'content',
                    'logo',
                    'logo_small',
                    'bonus_image',
                    'bonus_anons',
                    'website',
                ],
                'string',
            ],
            [
                [
                    'seo_name_md5_hash',
                ],
                'string',
                'max' => 32,
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
                'source' => 'name'
            ],
            [
                [
                    'seo_name_md5_hash',
                ],
                MD5Validator::class,
                'source' => 'seo_name',
            ],
            [
                [
                    'seo_name'
                ],
                UniquePropertyValidator::class,
                'propertyFields' => ['seo_name', 'domain_id']
            ],
            [
                [
                    'name',
                    'seo_title',
                    'seo_description',
                    'seo_keywords',
                ],
                'string',
                'max' => 255,
            ],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'id' => \Yii::t('common','#'),
            'domain_id' => \Yii::t('common','Домен'),
            'name' => \Yii::t('common', 'Наименование'),
            'content' => \Yii::t('common', 'Контент'),
            'seo_name' => \Yii::t('common', 'SEO имя'),
            'seo_h1' => \Yii::t('common', 'H1'),
            'seo_title' => \Yii::t('common', 'title'),
            'seo_description' => \Yii::t('common', 'description'),
            'seo_keywords' => \Yii::t('common', 'keywords'),
            'logo' => \Yii::t('common', 'Логотип'),
            'logo_small' => \Yii::t('common', 'Логотип (маленький)'),
            'website' => \Yii::t('common', 'Веб-сайт'),
            'bonus_sum' => \Yii::t('common', 'Сумма бонуса'),
            'bonus_text' => \Yii::t('common', 'Текст бонуса'),
            'bonus_image' => \Yii::t('common', 'Изображение бонуса'),
            'bonus_anons' => \Yii::t('common', 'Анонс бонуса'),
            'weight' => \Yii::t('common', 'Вес'),
            'pros' => \Yii::t('common', 'Плюсы'),
            'cons' => \Yii::t('common', 'Минусы'),
            'status' => \Yii::t('common', 'Статус'),
            'created_at' => \Yii::t('common', 'Дата создания'),
            'updated_at' => \Yii::t('common', 'Дата обновления'),
            'is_deleted' => \Yii::t('common', 'Удален'),
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->saveProperty($insert, $changedAttributes);

        return parent::afterSave($insert, $changedAttributes);
    }

    public function beforeDelete()
    {
        $this->deleteProperties();

        return parent::beforeDelete();
    }
}



```


2. Для организации поиска создаем search модель унаследованную от модели AR к примеру *Bookmaker*
    
```php

<?php

namespace common\search;

use yii\db\ActiveQuery;
use common\models\Bookmaker;
use yii\data\ActiveDataProvider;

/**
 * Class BookmakerSearch
 *
 * @package common\search
 * @author Poletaev Eugene <evgstn7@gmail.com>
 */
class BookmakerSearch extends Bookmaker
{
    /**
     * {@inheritdoc}
     * @return array
     */
    public function rules()
    {
        return [
            [
                [
                    'id',
                    'status',
                    'is_deleted',
                    'domain_id',
                ],
                'integer',
            ],
            [
                [
                    'name',
                    'seo_name',
                ],
                'safe',
            ],
        ];
    }

    /**
     * {@inheritdoc}
     * @param ActiveQuery $query
     */
    public function extendQuery(ActiveQuery $query)
    {
        $query->andFilterWhere([
            static::tableName().'.id' => $this->id,
            'status' => $this->status,
            'is_deleted' => $this->is_deleted,
        ]);

        $query->andFilterWhere(['like', static::propertyAlias() . ".seo_name", $this->seo_name]);
        $query->andFilterWhere(['like', static::propertyAlias() . ".name", $this->name]);
    }

    /**
     * {@inheritdoc}
     * @param ActiveDataProvider $dataProvider
     */
    public function extendDataProvider(ActiveDataProvider $dataProvider)
    {
        $this->addSortByPropertyAttribute($dataProvider, 'seo_name');
        $this->addSortByPropertyAttribute($dataProvider, 'name');
    }

    public static function getListSearchKeyAttribute()
    {
        return 'id';
    }

    public static function getListSearchAttribute()
    {
        return 'name';
    }
}



```    