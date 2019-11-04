<?php
namespace concepture\yii2logic\validators;

use concepture\yii2logic\forms\Form;
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
        $qFunc = function($q, $localizedAlias) use ($model, $attribute){
            $q->andWhere([$localizedAlias .".". $attribute => $model->{$attribute}]);
            if ($model->id) {
                $q->andWhere(['<>', 'entity_id', $model->id]);
            }
        };
        if ($model instanceof Form){
            $arClass = $model::getModelClass();
            $arClass::$search_by_locale_callable = $qFunc;
            $arClass::$current_locale = $model->locale;
            $arClass::$by_locale_hard_search = true;
        }else{
            $model::$search_by_locale_callable = $qFunc;
            $model::$current_locale = $model->locale;
            $model::$by_locale_hard_search = true;
        }

        $result = $model::find()->all();
        if (count($result)>0){
            $this->addError($model, $attribute,  Yii::t('core', 'Значение «{attribute}» должно быть уникальным.', ['attribute' => $attribute]));
            return false;
        }

        return true;
    }
}
