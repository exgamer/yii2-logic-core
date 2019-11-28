####Мини гайд по использованию перевязок через таблицу

Для использования перевязок через линковочную таблицу

1. Создаем таблицу дял перевязки у которой первичный ключ составной

```php
        $this->addTable([
            'entity_id' => $this->bigInteger()->notNull(),
            'linked_id' => $this->bigInteger()->notNull(),
            'created_at' => $this->dateTime()->defaultValue(new \yii\db\Expression("NOW()"))
        ]);

        ]);
        $this->addPK(['entity_id', 'linked_id']);
        $this->addIndex(['entity_id']);
        $this->addIndex(['linked_id']);
```

2. модель наследуем от LinkActiveRecord

```php
<?php
namespace concepture\yii2article\models;

use Yii;
use concepture\yii2logic\models\LinkActiveRecord;

/**
 * Class PostTagsLink
 * @package concepture\yii2article\models
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class PostTagsLink extends LinkActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{post_tags_link}}';
    }
}
```

3. форму от LinkForm

```php
<?php
namespace concepture\yii2article\forms;


use concepture\yii2logic\forms\LinkForm;
use Yii;

/**
 * Class PostTagsForm
 * @package concepture\yii2article\forms
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class PostTagsLinkForm extends LinkForm
{

}
```

4. сервис от LinkService

```php
<?php
namespace concepture\yii2article\services;

use concepture\yii2logic\services\LinkService;
use Yii;

/**
 * Class PostTagsService
 * @package concepture\yii2article\services
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class PostTagsLinkService extends LinkService
{

}

```

5. для перевязки вызываем пример

```php
        $this->postTagsLinkService()->link($modelId, $selectedIds);
```