<?php
namespace concepture\yii2logic\services\traits;

use yii\helpers\ArrayHelper;
use yii\db\ActiveQuery;
use Yii;

/**
 * Треит для подключения к сервисам локализованных сущностей
 *
 * Trait LocalizedReadTrait
 * @package concepture\yii2logic\services\traits
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
trait LocalizedReadTrait
{
    /**
     * @param $seo_name
     * @return mixed
     */
    public function getBySeoName($seo_name)
    {
        return $this->getOneByCondition(function(ActiveQuery $query) use ($seo_name){
            $query->andWhere(['p.seo_name' => $seo_name]);
        });
    }
}

