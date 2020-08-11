<?php
namespace concepture\yii2logic\forms;

use Yii;
use yii\validators\Validator;

/**
 * Class NoModelForm
 * @package concepture\yii2logic\forms
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class NoModelForm extends Form
{
    public function rules()
    {
        return [];
    }

    public function behaviors()
    {
        return [];
    }

    public function attributeLabels()
    {
        return [];
    }

    /**
     * возвращает класс модели
     *
     * @return string;
     */
    public static function getModelClass()
    {
        return static::class;
    }
}
