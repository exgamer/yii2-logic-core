<?php
namespace concepture\yii2logic\services\traits;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * Trait ViewsTrait
 * @package concepture\yii2logic\services\traits
 */
trait ViewsTrait
{
    /**
     * Наворачиваем счетчик просмотров
     *
     * @param $model
     * @return bool
     */
    public function processViewCount($model)
    {
        $table = $this->getTableName();
        $key = $table . '_views';
        $session = Yii::$app->session;
        // Если в сессии отсутствуют данные,
        // создаём, увеличиваем счетчик, и записываем в бд
        if (!isset($session[$key])) {
            $session->set($key, [$model->id]);
            $model->views++;
            $model->save(false);
        // Если в сессии уже есть данные то проверяем засчитывался ли данный пост
        // если нет то увеличиваем счетчик, записываем в бд и сохраняем в сессию просмотр этого поста
        } else {
            if (!ArrayHelper::isIn($model->id, $session[$key])) {
                $array = $session[$key];
                array_push($array, $model->id);
                $session->remove($key);
                $session->set($key, $array);
                $model->views++;
                $model->save(false);
            }
        }
        return true;
    }
}