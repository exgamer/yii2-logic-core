####Мини гайд по использованию сущностей которые будут словарями

#!!! ВАЖНО!! Yii::$app->domainService->getResolvedCurrentDomainAndLocale() определяет domain_id и locale_id который будет подставлен в запрос. Зависит от ключей languages b language_iso из domain_map


# В этом случае данне будут собираться в зависимости от  domain_id и locale_id


- создается основная таблица например sports
- создается таблица со свойствами sports_property (постфикс _property обязателен)
- для таблицы со свойствами нужно создать простую модель SportsProperty(правила необязательны класс будет пустой) 
- таблица со свойствами должна обязательно содержать поля (entity_id, deafult, domain_id, locale_id) 


1. Создаем модель AR унаследованную от *concepture\yii2logic\models\DomainBasedDictionaryActiveRecord*
   
```php

<?php

namespace common\models;

use concepture\yii2logic\models\DomainBasedDictionaryActiveRecord;
use Yii;

class Sports extends DomainBasedDictionaryActiveRecord
{
    /**
     * @var bool
     */
    public $allow_physical_delete = false;

    /**
     * @inheritDoc
     */
    public static function tableName()
    {
        return 'sports';
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return  [

        ];
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return [

        ];
    }

    /**
     * @inheritDoc
     */
    public static function getPropertyModelClass()
    {
        return SportsProperty::class;
    }
}



```



2. В сервис при создании добавляем установку локали указанного домена
    
```php

<?php

namespace common\services;

use concepture\yii2logic\forms\Model;
use Yii;
use yii\helpers\ArrayHelper;
use yii\db\Expression;

/**
 * Сервис видов спорта
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class SportsService extends \concepture\yii2logic\services\Service
{
    use \concepture\yii2handbook\services\traits\ModifySupportTrait;
    use \concepture\yii2handbook\services\traits\ReadSupportTrait;

    /**
     * @param Model $form
     */
    protected function beforeCreate(Model $form)
    {
        parent::beforeCreate($form);
        $this->setCurrentDomain($form);
        //установка локали указанного домена обязательно после утсановки domain_id
        $this->setCurrentDomainLocale($form);
    }
}

```



3. Контроллер для админки. Выставить параметры $domain и $domainByLocale

```php

<?php

namespace backend\controllers;

use kamaelkz\yii2admin\v1\controllers\BaseController;

/**
 * Виды спорта
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
class SportsController extends BaseController
{
    /**
     * @var bool
     */
    public $domain = true;

    /**
     * Для сущностей где проперти являются переводами нужно выставить true
     *
     * @var bool
     */
    public $domainByLocale = true;
}

```

4. Форма редактирования в админке. Подключить боковое меню для справочников

```php
            <?php if(isset($originModel)) :?>
                <?= $this->render('@concepture/yii2handbook/views/include/_domains_with_cross_locales_sidebar', [
                    'domain_id' => $domain_id,
                    'locale_id' => $locale_id,
                    'edited_domain_id' => $edited_domain_id,
                    'url' => isset($originModel) ? ['update', 'id' => $originModel->id] : ['create'],
                    'originModel' => $originModel ?? null,
                ]);
                ?>
            <?php endif;?>
```



