<?php
namespace concepture\yii2logic\services\traits;

use concepture\yii2logic\models\LinkActiveRecord;
use Exception;
use Yii;
use yii\db\ActiveQuery;

/**
 * Треит подключается к сервисам которые отвечают за перевязку сущности к другим сущностям
 * линкушки
 *
 * Trait LinkTrait
 * @package concepture\yii2logic\services\traits
 */
trait LinkTrait
{
    /**
     * Создаем связки сущности и привязанной сущности
     *
     * @param integer $entityId
     * @param array $selectedLinkedIds
     * @throws Exception
     */
    public function link($entityId, $selectedLinkedIds)
    {
        $relatedIds = array_unique($selectedLinkedIds);
        $modelClass = $this->getRelatedModelClass();
        if (! $modelClass instanceof LinkActiveRecord){
            throw new Exception($modelClass . " must be instance of " . LinkActiveRecord::class);
        }

        $currentLinks = $this->getAllByCondition(function(ActiveQuery $query) use ($modelClass, $entityId){
            $query->andWhere([ 'entity_id' => $entityId]);
            $query->indexBy('linked_id');
        });
        $currentTagsIds = array_keys($currentLinks);
        $deletedTagsIds = array_diff($currentTagsIds, $relatedIds);
        $modelClass::deleteAll(['linked_id' => $deletedTagsIds, 'entity_id' => $entityId]);
        if (! empty($tagIds)){
            $insertData = [];
            foreach ($tagIds as $id){
                $insertData[] = [
                    $entityId,
                    $id
                ];
            }
            $this->batchInsert(['entity_id', 'linked_id'], $insertData);
        }
    }
}

