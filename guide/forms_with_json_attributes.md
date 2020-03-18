####работа с таблицами с json полями

1. Создаем pojo класс описывающий структуру данных json

```php

<?php

namespace common\pojo;

use concepture\yii2logic\pojo\Pojo;

/**
 * Модель для данных по соцсетям
 *
 * Class Social
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class Social extends Pojo
{
    public $social;
    public $url;

    /**
     * В виджете DynamicForm требуется AR поэтому подсовываем это
     * @var bool
     */
    public $isNewRecord = true;

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            [
                [
                    'social',
                    'url',
                ],
                'required'
            ],
            [
                [
                    'url',
                ],
                'string'
            ],
            [
                [
                    'social',
                ],
                'string'
            ],
        ];
    }

    public function attributeLabels()
    {
        return [
            'social' => \Yii::t('common','Социальная сеть'),
            'url' => \Yii::t('common','Адрес'),
        ];
    }
}


```


2. В модели добавляем 
```php

    public function rules()
    {
        return [
            [
                [
                    'social',
                ],
                'safe'
            ]
        ];
    }

```

3. В форме добавляем

```php

<?php

namespace common\forms;

use common\enum\PlayerRoleEnum;
use common\models\Lineup;
use Yii;
use common\pojo\Social;
use kamaelkz\yii2admin\v1\forms\BaseForm;
use yii\db\ActiveRecord;

/**
 * Class BookmakerForm
 *
 * @package common\forms
 * @author Poletaev Eugene <evgstn7@gmail.com>
 */
class BookmakerForm extends BaseForm
{
    public $social = [];

    /**
     * @see Form::formRules()
     * @return array
     */
    public function formRules()
    {
        return [
        ];
    }

    /**
     * Возвращает атрибуты которые являются json данными
     *
     * [
     *   'attribute' => Pojo::class
     * ]
     *
     * @return array
     */
    public function jsonAttributes()
    {
        return [
            'social'  => Social::class
        ];
    }
}

```

4. на вьюшке

```php

<?= DynamicForm::widget([
    'limit' => 20, // the maximum times, an element can be cloned (default 999)
    'min' => empty($model->social) ? 0 :1, // 0 or 1 (default 1)
    'form' => $form,
    'models' => empty($model->social) ? [new Social()] : $model->social,
    'dragAndDrop' => false,
    'formId' => $form->getId(),
    'attributes' => [
        'social' => [
            'type' => Html::FIELD_DROPDOWN,
            'params' => [
                SocialEnum::arrayList(),
                [
                    'class' => 'form-control custom-select',
                    'prompt' => ''
                ]
            ]
        ],
        'url' => Html::FIELD_TEXT_INPUT,
    ]
]); ?>

```

