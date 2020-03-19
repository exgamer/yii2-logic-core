<?php
namespace concepture\yii2logic\validators;

use concepture\yii2logic\helpers\ClassHelper;
use Yii;
use yii\base\Exception;
use yii\validators\Validator;
use yii\db\ActiveRecord;

/**
 * Class DomainBasedUniqueValidator
 * @package concepture\yii2logic\validators
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class DomainBasedUniqueValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        if (is_array($attribute)) {
            $conditions = [];
            foreach ($attribute as $k => $v) {
                $conditions[$v] = is_int($k) ? $model->$v : $model->$k;
            }
        } else {
            $conditions = [$attribute => $model->$attribute];
        }

        $conditions['domain_id'] = Yii::$app->domainService->getCurrentDomainId();

        $serviceName = ClassHelper::getServiceName($model);
        $models = Yii::$app->{$serviceName}->getAllByCondition($conditions);
        if (count($models)>0){
            $label = $model->getAttributeLabel($attribute);
            $this->addError($model, $attribute,  Yii::t('core', 'Значение «{attribute}» должно быть уникальным.', ['attribute' => $label]));

            return false;
        }

        return true;
    }
}
