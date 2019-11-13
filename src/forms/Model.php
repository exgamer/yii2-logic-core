<?php
namespace concepture\yii2logic\forms;

use concepture\yii2logic\enum\ScenarioEnum;
use yii\base\Model as Base;

/**
 * Class Model
 * @package concepture\yii2logic\forms
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class Model extends Base
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