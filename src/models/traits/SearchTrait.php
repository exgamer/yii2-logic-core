<?php

namespace concepture\yii2logic\models\traits;

use Yii;
use yii\web\JsonParser;

/**
 * Трейт для поиска по моделям
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
trait SearchTrait
{
    /**
     * Подсчет количества выбранных фильтров
     *
     * @return integer
     */
    public function getSelectedFilterCount()
    {
        $request = Yii::$app->request->get($this->formName());
        $result = 0;
        foreach ($this->getValidators() as $validator) {
            $attrs = $validator->attributes;
            foreach ($attrs as $attr) {
                if(! isset($request[$attr]) || $request[$attr] == null) {
                    continue;
                }

                $result++;
            }
        }

        return $result;
    }

    /**
     * Если доступ через api отключаем ключ название формы
     *
     * @return string
     */
    public function formName()
    {
        if (Yii::$app->request instanceof \yii\web\Request){
            $parsers = Yii::$app->request->parsers;
            if(
                isset($parsers['application/json'])
                && $parsers['application/json'] == JsonParser::class
            ){
                return '';
            }
        }

        return parent::formName();
    }
}
