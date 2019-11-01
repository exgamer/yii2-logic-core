<?php
namespace concepture\yii2logic\actions\traits;

use Yii;

/**
 * Trait LocalizedTrait
 * @package concepture\yii2logic\actions\traits
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
trait LocalizedTrait
{
    /**
     * Получить из аргументов экшона локаль
     * по умолчанию вернет язык приложения
     *
     * @return string
     */
    protected function getLocale()
    {
        $args = $this->getRunArguments();
        if (!isset($args['locale'])){

            return Yii::$app->language;
        }

        return $args['locale'];
    }
}

