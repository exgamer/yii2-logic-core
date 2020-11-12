####Мини гайд по использованию сущностей которые имеют разный контент в зависимости от уникального поля на примере
####сущности разделенной по домену

# В этом случае данне будут собираться в зависимости от текущего domain_id

# Для работы с сущностью имеющей свойства
    - создается основная таблица например bookmaker
    - создается таблица со свойствами bookmaker_property (постфикс _property обязателен)
    - для таблицы со свойствами нужно создать простую модель BookmakerProperty(правила необязательны класс будет пустой) 
    - таблица со свойствами должна обязательно содержать поля (entity_id, deafult и поле по которому будет определяться уникальность в этмо случае domain_id) 


1. Создаем модель AR унаследованную от *concepture\yii2logic\models\ActiveRecord* и подключаем треит concepture\yii2logic\models\traits\v2\property\HasDomainPropertyTrait
   
   Создаем модель для таблицы со свойствами BookmakerProperty


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


## Сущности с несколькими уникальными полями в проперти
### Пример создание сущности с уникальностью по домену и языку для каждого домена

1. В таблицу property добавляем поля domain_id и locale_id
2. Первичный ключ (entity_id, domain_id, locale_id)
3. В модель добавляем

```php

<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2handbook\converters\LocaleConverter;
use concepture\yii2logic\models\traits\v2\property\HasDomainByLocalesPropertyTrait;
use concepture\yii2handbook\models\traits\DomainTrait;
use concepture\yii2user\models\traits\UserTrait;
use concepture\yii2handbook\models\traits\TagsTrait;
use kamaelkz\yii2cdnuploader\traits\ModelTrait;
use common\validators\HtmlContentFilter;

/**
 * Модель постов
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class Post extends ActiveRecord
{
    use HasDomainByLocalesPropertyTrait;
}




```

3. В сервис при создании добавляем установку локали указанного домена

    Если подключить к сервису PropertyReadTrait можно делать запросы на чтение через :

    $properties =  $this->readProperty()->getAllByCondition(function (ActiveQuery $query) use ($user, $domainIds) {
        $query->andWhere(['entity_id' => $user->id, 'domain_id' => $domainIds]);
        $query->asArray();
    });
    
    Если подключить к сервису PropertyModifyTrait можно делать запросы на модификацию через :
                
    $this->userService()->modifyProperty()->insert([
        'entity_id' => 16,
        'domain_id' => 10,
        'username' => 'test',
    ]);
```php

<?php

namespace common\services;

use common\models\PostCategoryProperty;
use common\models\PostProperty;
use common\services\traits\RedirectSupportTrait;
use Yii;
use yii\db\ActiveQuery;
use common\components\likes\traits\LikesServiceTrait;
use concepture\yii2handbook\services\interfaces\SitemapServiceInterface;
use concepture\yii2article\models\Post;
use concepture\yii2article\services\PostService as Base;
use concepture\yii2handbook\services\interfaces\UrlHistoryInterface;
use concepture\yii2logic\forms\Model;
use concepture\yii2handbook\services\traits\SitemapSupportTrait;
use common\components\copy_property\interfaces\CopyPropertyInterface;
use common\components\copy_property\services\traits\CopyPropertyTrait;
use yii\db\Expression;
use yii\db\Query;

/**
 * Сервис для поста
 * переопределяет \concepture\yii2article\services\PostService в common/config/definitions.php
 *
 * Class PostService
 * @package common\services
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class PostService extends Base implements UrlHistoryInterface, SitemapServiceInterface , CopyPropertyInterface
{
    use concepture\yii2handbook\services\traits\ModifySupportTrait;
    use PropertyReadTrait;
    use PropertyModifyTrait;


    protected function beforeCreate(Model $form)
    {
        parent::beforeCreate($form);
        // установка текущего домена
        $this->setCurrentDomain($form);
        //установка локали указанного домена обязательно после утсановки domain_id
        $this->setCurrentDomainLocale($form);
    }


}





```