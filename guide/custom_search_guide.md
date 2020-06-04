# гайд по кастомной фильтрации данных 

1. Создаем кастомный Search класс унаслдеованный от AR и прописываем все условия поиска

```php
<?php

namespace frontend\search\review;

use Yii;
use yii\db\ActiveQuery;

/**
 * Фильтр для списка отзывов текущего авторизованного пользователя
 *
 * Class CurrentUserReviewSearch
 * @package frontend\search\review
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class CurrentUserReviewSearch extends \common\models\Review
{
    /**
    * @inheritDoc
    */
    public function rules()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function extendQuery(ActiveQuery $query)
    {
        $query->andWhere([
            static::tableName().'.user_id' => Yii::$app->user->identity->id
        ]);
        $query->andWhere([
            static::tableName().'.actual' => 1
        ]);
        $query->active();
        $query->notDeleted();
        $query->with(['bookmaker', 'user']);
        $query->orderBy(['created_at' => SORT_DESC]);
    }
}
```

2. В контроллере метод сервиса getDataProvider 3 параметрмо просто подсовываем экземпляр нашего кастомного Search

```php
<?php

class ProfileController extends BaseController
{


    /**
     * Обратная связь
     *
     * @param int $page
     * @return string HTML
     */
    public function actionIndex($page = 0)
    {
        $dataProvider = $this->reviewService()->getDataProvider(
            Yii::$app->request->queryParams,
            [],
            new CurrentUserReviewSearch()
        );

        return $this->render('index.html.twig', [
            'dataProvider' => $dataProvider
        ]);
    }
}

```