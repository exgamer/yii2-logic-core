<?php
namespace concepture\yii2logic\actions\traits;

use concepture\yii2logic\enum\ScenarioEnum;
use Yii;

/**
 * Trait ModelScenarioTrait
 * @package concepture\yii2logic\actions\traits
 */
trait ModelScenarioTrait
{
    /**
     * @return array
     */
    public function scenarios()
    {
        $scenarios = array_merge(
            parent::scenarios(),
            [
                ScenarioEnum::INSERT => $this->attributes(),
                ScenarioEnum::UPDATE => $this->attributes()
            ]
        );

        return $scenarios;
    }
}

