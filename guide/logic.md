####Мини гайд по использованию

1. Создаем модель AR унаследованную от *concepture\yii2logic\models\ActiveRecord*

```php

<?php
namespace concepture\yii2user\models;

use Yii;
use concepture\yii2logic\models\ActiveRecord;

/**
 * UserRole model
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $role_id
 * @property datetime $created_at
 */
class UserRole extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user_role}}';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'user_id',
                    'role_id'
                ],
                'integer'
            ]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('user', '#'),
            'user_id' => Yii::t('user', 'Пользователь'),
            'role_id' => Yii::t('user', 'Роль'),
            'created_at' => Yii::t('user', 'Дата создания'),
        ];
    }
}



```

2. Создаем форму унаследованную от *concepture\yii2logic\forms\Form*

```php
<?php
namespace concepture\yii2user\forms;

use Yii;
use concepture\yii2logic\forms\Form;

/**
 * UserCredentialForm
 */
class UserRoleForm extends Form
{
    public $user_id;
    public $role_id;

    /**
     * @see CForm::formRules()
     */
    public function formRules()
    {
        return [
            [
                [
                    'user_id',
                    'role_id'
                ],
                'required'
            ],
        ];
    }
}

```

3. Создаем search модель унаследованную от модели AR к примеру *UserRole*

```php

<?php

namespace concepture\yii2user\search;

use concepture\yii2user\models\User;
use concepture\yii2user\models\UserRole;
use concepture\yii2user\models\UserRoleHandbook;
use yii\db\ActiveQuery;
use Yii;
use yii\data\ActiveDataProvider;

class UserRoleSearch extends UserRole
{
    public $username;
    public $caption;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['caption','username'], 'safe'],
        ];
    }

    protected function extendQuery(ActiveQuery $query)
    {
        $query->joinWith(['user','role']);
        $query->andFilterWhere([
            'id' => $this->id
        ]);
        $query->andFilterWhere(['like', User::tableName().'.username', $this->username]);
        $query->andFilterWhere(['like', UserRoleHandbook::tableName().'.caption', $this->caption]);
    }

    protected function extendDataProvider(ActiveDataProvider $dataProvider)
    {
        $dataProvider->sort->attributes['username'] = [
            'asc' => [User::tableName().'.username' => SORT_ASC],
            'desc' => [User::tableName().'.username' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['caption'] = [
            'asc' => [UserRoleHandbook::tableName().'.caption' => SORT_ASC],
            'desc' => [UserRoleHandbook::tableName().'.caption' => SORT_DESC],
        ];
    }

    public function attributeLabels()
    {
        $labels = parent::attributeLabels();

        return array_merge($labels, [
            'username' => Yii::t('user', 'Пользователь'),
            'caption' => Yii::t('user', 'Роль')
        ]);
    }
}


```


4. Создаем сервис для реализации бизнес логики унаследованный от *concepture\yii2logic\services\Service*


```php

<?php
namespace concepture\yii2user\services;

use concepture\yii2logic\services\Service;

/**
 * UserRoleService
 *
 */
class UserRoleService extends Service
{

}


```



5. регистрируем сервис как компонент yii

```php

<?php

return [
    'components' => [
        'userRoleService' => [
            'class' => '\concepture\yii2user\services\UserRoleService'
        ]
    ]
];

```