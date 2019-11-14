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
        $extendedScenarios = $this->extendedScenarios();
        foreach ($extendedScenarios as $name => $excludedAttributes){
            $this->addCustomScenario($scenarios, $name, $excludedAttributes);
        }

        return $scenarios;
    }

    /**
     * Добавить сценарий
     *
     * @param array $scenarios
     * @param string $name
     * @param array $excludedAttributes
     */
    protected function addCustomScenario(array &$scenarios, string $name, array $excludedAttributes)
    {
        $scenarios = parent::scenarios();
        $attributes = $this->attributes();
        $attributes = array_flip($attributes);
        foreach ($excludedAttributes as $attribute){
            unset($attributes[$attribute]);
        }
        $attributes = array_flip($attributes);
        $scenarios[$name] = $attributes;
    }

    /**
     * Возвращает массив с кастомными сценариями
     * где ключ название сценария а значение массив с аттрибутами которые нужно исключить
     *
     * [
     *      'сценарий1' => [
     *          'ненужный атрибут1',
     *          'ненужный атрибут2'
     *      ]
     * ]
     *
     * @return array
     */
    protected function extendedScenarios()
    {
        return [];
    }
}

