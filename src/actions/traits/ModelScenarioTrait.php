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
            $attributes = $this->resolveScenarioAttributes($excludedAttributes);
            $scenarios[$name] = $attributes;
        }

        return $scenarios;
    }

    /**
     * Исключить ненужные аттрибуты для сценария
     *
     * @param array $excludedAttributes
     * @return array
     */
    protected function resolveScenarioAttributes(array $excludedAttributes)
    {
        $attributes = $this->attributes();
        $attributes = array_flip($attributes);
        foreach ($excludedAttributes as $attribute){
            unset($attributes[$attribute]);
        }

        return array_flip($attributes);
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

