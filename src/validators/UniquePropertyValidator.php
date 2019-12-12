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
//        $qFunc = function($q, $localizedAlias) use ($model, $attribute){
//            $q->andWhere([$localizedAlias .".". $attribute => $model->{$attribute}]);
//            if (isset($model->id)) {
//                $q->andWhere(['<>', 'entity_id', $model->id]);
//            }
//        };
        if ($model instanceof Form){
            $arClass = $model::getModelClass();
//            $arClass::$search_by_locale_callable = $qFunc;
            $arClass::setLocale($model->locale);
//            $arClass::enableLocaleHardSearch();
        }else{
//            $model::$search_by_locale_callable = $qFunc;
            $model::setLocale($model->locale);
//            $model::enableLocaleHardSearch();
        }

        $serviceName = ClassHelper::getServiceName($model, "Form");
        $query = Yii::$app->{$serviceName}->getQuery();
        $query->andWhere([$model::localizationAlias() .".". $attribute => $model->{$attribute}]);
        if (isset($model->id)) {
            $query->andWhere(['<>', $model::localizationAlias() . "." . 'entity_id', $model->id]);
        }
        $result = $query->all();
        if (count($result)>0){
            $this->addError($model, $attribute,  Yii::t('core', 'Значение «{attribute}» должно быть уникальным.', ['attribute' => $attribute]));
            return false;
        }

        return true;
    }
}
