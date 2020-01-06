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
        $sessionId = Yii::$app->session->getId();
        $key = $sessionId . "_" . $table . '_view_' . $model->id;
        $cache = Yii::$app->cache;
        $data = $cache->get($key);
        if ($data === false) {
            $cache->set($key, [$model->id], 86400);
            $model->views++;
            $model->save(false);
        }

        return true;
    }
}