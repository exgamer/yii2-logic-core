<?php
namespace concepture\yii2logic\validators\v2;

use Yii;
use yii\base\Exception;
use yii\validators\Validator;
use yii\db\ActiveRecord;

/**
 * Валидатор который проверяет, что если хоть одно поле заполнено то все остальные должны быть заполнены
 * 
 * Class OnThenAllRequiredValidator
 * @package concepture\yii2logic\validators\v2
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class OnThenAllRequiredValidator extends Validator
{
    public function validateAttributes($model, $attributes = null)
    {
        $attributes = $this->getValidationAttributes($attributes);
        $filled = [];
        $notFilled = [];
        foreach ($attributes as $attribute) {
            if ($model->{$attribute}) {
                $filled[] = $attribute;
            }else{
                $notFilled[] = $attribute;
            }
        }

        if (! empty($filled) && ! (empty($notFilled))) {
            foreach ($notFilled as $attr) {
                $this->addError($model, $attr,  Yii::t('core', 'Значение «{attribute}» должно быть заполнено.', ['attribute' => $model->getAttributeLabel($attr)]));
            }
        }
    }
}
