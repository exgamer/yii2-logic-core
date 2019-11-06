####Мини гайд по использованию моделей с локализированными свойствами

Локализованные модели по умолчанию будут возвращать дефолтный язык приложения.
Для изменения выборки по языку нужно сделать следующее:

```php
        Post::$current_locale = "ru";
        Post::find()->where(['id' => $id])->one();

```


1. Создаем модель AR унаследованную от *concepture\yii2logic\models\ActiveRecord* и подключаем треит concepture\yii2logic\models\traits\HasLocalizationTrait
   и реализовать поведения afterSave, afterDelete, afterFind

```php

<?php
namespace concepture\yii2static\models;

use concepture\yii2user\models\User;
use concepture\yii2logic\validators\UniquePropertyValidator;
use Yii;
use concepture\yii2logic\models\ActiveRecord;
use concepture\yii2logic\validators\TranslitValidator;
use concepture\yii2logic\models\traits\HasLocalizationTrait;
use concepture\yii2logic\models\traits\StatusTrait;

/**
 * StaticBlock model
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $locale
 * @property string $title
 * @property string $content
 * @property string $seo_name
 * @property string $seo_title
 * @property string $seo_description
 * @property string $seo_keywords
 * @property integer $status
 * @property datetime $created_at
 * @property datetime $updated_at
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class StaticBlock extends ActiveRecord
{
    use HasLocalizationTrait;
    use StatusTrait;

    public $locale;
    public $title;
    public $content;
    public $seo_name;
    public $seo_title;
    public $seo_description;
    public $seo_keywords;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{static_block}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'status',
                    'user_id'
                ],
                'integer'
            ],
            [
                [
                    'locale'
                ],
                'string',
                'max'=>2
            ],
            [
                [
                    'content'
                ],
                'string'
            ],
            [
                [
                    'title',
                    'seo_name',
                ],
                'string',
                'max'=>1024
            ],
            [
                [
                    'seo_name',
                ],
                TranslitValidator::className(),
                'source' => 'title'
            ],
            [
                [
                    'seo_name',
                ],
                UniquePropertyValidator::class
            ],
            [
                [
                    'seo_title',
                    'seo_description',
                    'seo_keywords',
                ],
                'string',
                'max'=>175
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('static','#'),
            'user_id' => Yii::t('static','Пользователь'),
            'status' => Yii::t('static','Статус'),
            'locale' => Yii::t('static','Язык'),
            'title' => Yii::t('static','Название'),
            'content' => Yii::t('static','Контент'),
            'seo_name' => Yii::t('static','SEO название'),
            'seo_title' => Yii::t('static','SEO title'),
            'seo_description' => Yii::t('static','SEO description'),
            'seo_keywords' => Yii::t('static','SEO keywords'),
            'created_at' => Yii::t('static','Дата создания'),
            'updated_at' => Yii::t('static','Дата обновления'),
        ];
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->saveLocalizations();

        return parent::afterSave($insert, $changedAttributes);
    }

    public function afterDelete()
    {
       $this->deleteLocalizations();

       return parent::afterDelete();
    }

    public function afterFind()
    {
        $this->setLocalizations();

       return parent::afterFind();
    }
}



```

2. Создаем форму унаследованную от *concepture\yii2logic\forms\Form*

```php
<?php
namespace concepture\yii2static\forms;


use concepture\yii2logic\forms\Form;
use Yii;

/**
 * Class StaticBlockForm
 * @package concepture\yii2static\forms
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class StaticBlockForm extends Form
{
    public $user_id;
    public $locale = "ru";
    public $title;
    public $content;
    public $seo_name;
    public $seo_title;
    public $seo_description;
    public $seo_keywords;
    public $status = 0;

    /**
     * @see Form::formRules()
     */
    public function formRules()
    {
        return [
            [
                [
                    'title',
                    'content',
                    'locale',
                ],
                'required'
            ],
        ];
    }
}

```

3. Для организации поиска создаем search модель унаследованную от модели AR к примеру *StaticBlock*
    поиск по локализованным атрибутом осуществляется с помощью метода searchByLocalized, где $localizedAlias - альяс таблицы с переводами
    если нужна сортировка по локализованным атрибутам реалзиуем метод extendDataProvider и вызываем addSortByLocalizationAttribute с атрибутом во 2 аргументе

```php

<?php

namespace concepture\yii2static\search;

use concepture\yii2static\models\StaticBlock;
use yii\db\ActiveQuery;
use Yii;
use yii\data\ActiveDataProvider;

/**
 * Class StaticBlockSearch
 * @package concepture\yii2static\search
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class StaticBlockSearch extends StaticBlock
{

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'id',
                    'status'
                ],
                'integer'
            ],
            [
                [
                    'title',
                    'seo_name',
                    'locale',
                ],
                'safe'
            ],
        ];
    }

    protected function extendQuery(ActiveQuery $query)
    {
        $query->andFilterWhere([
            'id' => $this->id
        ]);
        $query->andFilterWhere([
            'status' => $this->status
        ]);
        static::$search_by_locale_callable = function($q, $localizedAlias){
            $q->andFilterWhere(['like', "{$localizedAlias}.seo_name", $this->seo_name]);
            $q->andFilterWhere(['like', "{$localizedAlias}.title", $this->title]);
        };
    }

    protected function extendDataProvider(ActiveDataProvider $dataProvider)
    {
        $this->addSortByLocalizationAttribute($dataProvider, 'seo_name');
        $this->addSortByLocalizationAttribute($dataProvider, 'title');
    }

}


```


4. Создаем сервис для реализации бизнес логики унаследованный от *concepture\yii2logic\services\Service* и подключаем треит * concepture\yii2logic\services\traits\LocalizedReadTrait*


```php

<?php
namespace concepture\yii2static\services;

use concepture\yii2logic\forms\Form;
use concepture\yii2logic\services\Service;
use Yii;
use concepture\yii2logic\services\traits\StatusTrait;
use concepture\yii2logic\services\traits\LocalizedReadTrait;


/**
 * Class StaticBlockService
 * @package concepture\yii2static\service
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class StaticBlockService extends Service
{
    use StatusTrait;
    use LocalizedReadTrait;

    protected function beforeCreate(Form $form)
    {
        $form->user_id = Yii::$app->user->identity->id;
    }
}


```



5. регистрируем сервис как компонент yii

```php

<?php

return [
    'components' => [
        'staticBlockService' => [
            'class' => '\concepture\yii2static\services\StaticBlockService'
        ]
    ]
];

```

6. создаем контроллер унаследованный от *concepture\yii2logic\controllers\web\localized\Controller*

```php

<?php

namespace concepture\yii2static\web\controllers;

use concepture\yii2user\enum\UserRoleEnum;
use concepture\yii2logic\controllers\web\localized\Controller;
use concepture\yii2logic\actions\web\StatusChangeAction;


/**
 * Class StaticBlockController
 * @package concepture\yii2static\web\controllers
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class StaticBlockController extends Controller
{
    protected function getAccessRules()
    {
        return [
            [
                'actions' => ['index', 'view','create', 'update', 'delete', 'status-change'],
                'allow' => true,
                'roles' => [UserRoleEnum::ADMIN],
            ]
        ];
    }


    public function actions()
    {
        $actions = parent::actions();

        return array_merge($actions,[
            'status-change' => StatusChangeAction::class
        ]);
    }
}

```