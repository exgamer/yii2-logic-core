<?php
namespace concepture\yii2logic\services\traits;

use concepture\yii2logic\models\traits\CurrentLocale;
use yii\helpers\ArrayHelper;
use yii\db\ActiveQuery;
use Yii;

/**
 * Trait LocalizedReadTrait
 * @package concepture\yii2logic\services\traits
 */
trait LocalizedReadTrait
{
    /**
     * @param $seo_name
     * @return mixed
     */
    public function getBySeoName($seo_name)
    {
        CurrentLocale::$_current_locale = Yii::$app->language;

        return $this->getOneByCondition(function(ActiveQuery $query) use ($seo_name){
            $query->andWhere(['seo_name' => $seo_name]);
        });
    }
}

