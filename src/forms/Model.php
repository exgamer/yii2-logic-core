<?php
namespace concepture\yii2logic\forms;

use concepture\yii2logic\actions\traits\ModelScenarioTrait;
use concepture\yii2logic\traits\ModelSupportTrait;
use yii\base\Model as Base;

/**
 * Class Model
 * @package concepture\yii2logic\forms
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class Model extends Base
{
    use ModelScenarioTrait;
    use ModelSupportTrait;

    private $old_scenario;

    /**
     * @return mixed
     */
    public function getOldScenario()
    {
        return $this->old_scenario;
    }

    /**
     * @param mixed $old_scenario
     */
    public function setOldScenario($old_scenario)
    {
        $this->old_scenario = $old_scenario;
    }

    /**
     * Sets the scenario for the model.
     * Note that this method does not check if the scenario exists or not.
     * The method [[validate()]] will perform this check.
     * @param string $value the scenario that this model is in.
     */
    public function setScenario($value)
    {
        $this->setOldScenario($this->getScenario());
        parent::setScenario($value);
    }

    /**
     *
     */
    public function rollbackOldScenario()
    {
        if ($this->getOldScenario()) {
            $this->setScenario($this->getOldScenario());
        }
    }
}