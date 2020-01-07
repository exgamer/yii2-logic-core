<?php
namespace concepture\yii2logic\services\traits;

use Yii;
use yii\caching\TagDependency;
use yii\db\ActiveQuery;

/**
 * Trait CacheTrait
 * @package concepture\yii2logic\services\traits
 */
trait CacheTrait
{
    /**
     * Признак кеширвоания данных сервиса
     *
     * @var bool
     */
    protected $cacheQuery = false;

    /**
     * сброс кеша по тегам
     * @param array $tags
     */
    public function invalidateQueryCacheByTags($tags = [])
    {
        if (! $this->cacheQuery)
        {
            return;
        }

        $tags = $this->getAliasedTags($tags);
        $tags = array_merge($this->getCacheTagsDependency(), $tags);
        TagDependency::invalidate(Yii::$app->cache, $tags);
    }

    /**
     * кеширование запроса
     *
     * @param ActiveQuery $query
     * @param int $duration
     * @param array $tags
     */
    public function queryCacheByTags($query, $duration = 3600, $tags = [])
    {
        if (! $this->cacheQuery)
        {
            return;
        }

        $tags = $this->getAliasedTags($tags);
        $tags = array_merge($this->getCacheTagsDependency(), $tags);
        $query->cache($duration, new TagDependency(['tags' => $tags]));
    }

    /**
     * @todo протестить
     * Кеширование dataProvider
     *
     * @param $dataProvider
     * @param int $duration
     * @param array $tags
     */
    public function cacheDataProvider($dataProvider, $duration = 3600, $tags = [])
    {
        if (! $this->cacheQuery)
        {
            return;
        }

        $tags = $this->getAliasedTags($tags);
        $tags = array_merge($this->getCacheTagsDependency(), $tags);
        $dependency = new TagDependency(['tags' => $tags]);
        $this->getDb()->cache(function ($db) use ($dataProvider) {
            $dataProvider->prepare();
        }, $duration, $dependency);

        return $dataProvider;
    }

    /**
     * Добавляет к тегам префикс таблицы
     *
     * @param $tags
     * @return array
     */
    protected function getAliasedTags($tags)
    {
        if (empty($tags)){
            return $tags;
        }

        $table =$this->getTableName();
        $allyTags = [];
        foreach ($tags as $tag){
            $allyTags[] = $table ."_" . $tag;
        }

        return $allyTags;
    }

    /**
     * Возвращает теги зависимостей для кеша
     *
     * @return array
     */
    public function getCacheTagsDependency()
    {
        return [
            $this->getTableName()
        ];
    }
}

