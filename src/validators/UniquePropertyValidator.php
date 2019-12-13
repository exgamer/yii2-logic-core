<?php
namespace concepture\yii2logic\validators;

use concepture\yii2logic\forms\Form;
use concepture\yii2logic\helpers\ClassHelper;
use Yii;
use yii\base\Exception;
use yii\validators\Validator;
use yii\db\ActiveRecord;
/**
 * Валидатор на уникальность по локализованному атрибуту
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class UniquePropertyValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        if ($model instanceof Form){
            $arClass = $model::getModelClass();
            $arClass::setLocale($model->locale);
            $locAlias = $arClass::localizationAlias();
        }else{
            $model::setLocale($model->locale);
            $locAlias = $model::localizationAlias();
        }

        $serviceName = ClassHelper::getServiceName($model, "Form");
        $query = Yii::$app->{$serviceName}->getQuery();
        $query->andWhere([$locAlias.".". $attribute => $model->{$attribute}]);
        if (isset($model->id)) {
            $query->andWhere(['<>', $locAlias . "." . 'entity_id', $model->id]);
        }

        $result = $query->all();
        if (count($result)>0){
            $this->addError($model, $attribute,  Yii::t('core', 'Значение «{attribute}» должно быть уникальным.', ['attribute' => $attribute]));
            return false;
        }

        return true;
    }
}
