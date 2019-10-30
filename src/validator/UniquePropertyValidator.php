<?php
namespace concepture\yii2logic\validators;

use Yii;
use yii\base\Exception;
use yii\validators\Validator;
use yii\db\ActiveRecord;
/**
 *
 *
 * @author citizenzet <exgamer@live.ru>
 */
class UniquePropertyValidator extends Validator
{
    public function validateAttribute($model, $attribute)
    {
        $query = $model::find();
        $query->joinWith([
            'localization' => function ($q) use ($model, $attribute) {
                $q->on = null;
                $propModelClass = $model::getLocalizationModelClass();
                $q->from($propModelClass::tableName() . " p");
                $q->andWhere(['p.locale' => $model::getLocalizationLocale()]);
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
