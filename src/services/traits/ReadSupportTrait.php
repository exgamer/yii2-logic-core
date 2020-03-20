<?php
namespace concepture\yii2logic\services\traits;

use Yii;
use yii\db\ActiveQuery;
use concepture\yii2logic\enum\IsDeletedEnum;

/**
 * Trait ReadSupportTrait
 * @package concepture\yii2logic\services\traits
 */
trait ReadSupportTrait
{
    /**
     * Добавялет в запрос условие выборки где запись не удалена
     *
     * @param ActiveQuery $query
     */
    protected function applyNotDeleted(ActiveQuery $query)
    {
        $query->andWhere(['is_deleted' => IsDeletedEnum::NOT_DELETED]);
    }
}

