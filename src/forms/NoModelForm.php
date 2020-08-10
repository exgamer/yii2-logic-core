<?php
namespace concepture\yii2logic\forms;

use Yii;

/**
 * Class NoModelForm
 * @package concepture\yii2logic\forms
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class NoModelForm extends Form
{
    /**
     * возвращает класс модели
     *
     * @return string;
     */
    public static function getModelClass()
    {
        return Yii::createObject(static::class);
    }
}
