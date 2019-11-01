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
        $query = $model::find();
        $query->joinWith([
            'localization' => function ($q) use ($model, $attribute) {
                $q->on = null;
                $locale = null;
                if ($model instanceof Form){
                    $arClass = $model::getModelClass();
                    $locale = $arClass::$current_locale;
                    $propModelClass = $arClass::getLocalizationModelClass();
                }else{
                    $locale = $model::$current_locale;
                    $propModelClass = $model::getLocalizationModelClass();
                }
                $q->from($propModelClass::tableName() . " p");
                $q->andWhere(['p.locale' => $locale]);
                $q->andWhere(['p.' . $attribute => $model->{$attribute}]);
                if (isset($model->id)) {
                    $q->andWhere(['<>', 'entity_id', $model->id]);
                }
            }
        ]);
        $result = $query->all();
        if (count($result)>0){
            $this->addError($model, $attribute,  Yii::t('core', 'Значение «{attribute}» должно быть уникальным.', ['attribute' => $attribute]));
            return false;
        }

        return true;
    }
}