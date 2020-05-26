# использование Search моделей как фильтры

1. Вслучаях когда нужны кастомные выборки можно использовать кастомные Search модели для фильтрации данных
   Это позволяет соблюдать модульность и не засорять сервисы кастомнымим методами с запросами
   
# Пример использования в контроллере

```php
<?php

namespace frontend\search\post;

use common\models\Post;
use concepture\yii2logic\enum\IsDeletedEnum;
use concepture\yii2logic\enum\StatusEnum;
use yii\db\ActiveQuery;

class PostIndexSearch extends Post
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'category_id',
                ],
                'integer'
            ]
        ];
    }

    public function extendQuery(ActiveQuery $query)
    {
        $query->andWhere([
            'status' => StatusEnum::ACTIVE,
            'is_deleted' => IsDeletedEnum::NOT_DELETED
        ]);
        $query->andFilterWhere([
            'category_id' => $this->category_id
        ]);
        $query->with(['category']);
        $query->asArray();

        $query->orderBy('published_at DESC');
    }
}

```

```php
class PostController extends BaseController
{
    public function dataProviderConfig($page = 0)
    {
        return [
            'pagination' => [
                'pageSize' => 2,
                'pageSizeParam' => false,
                'forcePageParam' => false,
                'page' => $page
            ]
        ];
    }

    public function actionIndex($page = 0, $category_id = null)
    {
        $dataProvider = $this->postService()->getDataProvider(
            Yii::$app->request->queryParams,
            $this->dataProviderConfig($page),
            new PostIndexSearch(['category_id' => $category_id]),
            ''
        );

        return $this->render('index.html.twig', [
            'dataProvider' => $dataProvider,
        ]);
    }
}
```



# Пример использования в виджете

```php
<?php

namespace frontend\widgets;

use concepture\yii2article\traits\ServicesTrait;
use concepture\yii2logic\db\ActiveQuery;
use frontend\search\post\BestPostSearch;
use frontend\widgets\traits\EntityTypePositionTrait;
use Yii;
use concepture\yii2handbook\components\cache\CacheWidget;
use common\components\cache\CacheTagEnum;


/**
 * Class Post
 * @package frontend\widgets
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class Post extends CacheWidget
{
    use ServicesTrait;
    /**
     * @var string представление
     */
    public $view = 'post/index.html.twig';

    /**
     * @var int
     */
    public $limit = 5;

    /**
     * @var string
     */
    public $searchClass;

    /**
     * параметры для серч модели
     * @var array
     */
    public $params = [];

    /**
     * @inheritDoc
     */
    public function getContent()
    {
        $config = [
            'pagination' => [
                'pageSize' => $this->limit,
                'pageSizeParam' => false,
                'forcePageParam' => false,
                'page' => 0
            ]
        ];
        $dataProvider = $this->postService()->getDataProvider($this->params, $config, new $this->searchClass(), '');
        if (empty($dataProvider->getModels())){
            return null;
        }

        return $this->render($this->view, [
            'items' => $dataProvider->getModels(),
            'totalCount' => $dataProvider->getTotalCount(),
        ]);
    }
}

```

Вызов виджета

```twig
    {{ post_widget({'searchClass' : "frontend\\search\\post\\PostIndexSearch", 'view' : 'post/best.html.twig', 'params':{'category_id': model.id} }) }}
```
