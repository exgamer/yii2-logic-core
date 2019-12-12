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
class UniqueLocalizedValidator extends Validator
{
    /**
     * Поля для валидации
     * @var array
     */
    public $fields;

    /**
     * Локализованные поля для валидации
     * @var array
     */
    public $localizedFields;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        if (! $this->localizedFields) {
            throw new Exception(Yii::t('yii', 'Свойство {$localizedFields} должно быть установлено.'));
        }

        if (! is_array($this->localizedFields)){
            $this->localizedFields = [$this->localizedFields];
        }

        if ($this->fields && ! is_array($this->localizedFields)){
            $this->fields = [$this->fields];
        }
    }

    public function validateAttribute($model, $attribute)
    {
        $qFunc = function($q, $localizedAlias) use ($model){
            foreach ($this->localizedFields as $field) {
                $q->andWhere([$localizedAlias . "." . $field => $model->{$field}]);
            }

            if (isset($model->id)) {
                $q->andWhere(['<>', 'entity_id', $model->id]);
            }
        };
        $locAlias = "";
        if ($model instanceof Form){
            $arClass = $model::getModelClass();
//            $arClass::$search_by_locale_callable = $qFunc;
            $arClass::setLocale($model->locale);
            $locAlias = $arClass::localizationAlias();
//            $arClass::enableLocaleHardSearch();
        }else{
//            $model::$search_by_locale_callable = $qFunc;
            $model::setLocale($model->locale);
            $locAlias = $model::localizationAlias();
//            $model::enableLocaleHardSearch();
        }

        $serviceName = ClassHelper::getServiceName($model, "Form");
        $query = Yii::$app->{$serviceName}->getQuery();
        foreach ($this->fields as $field){
            $query->andWhere([$field => $model->{$field}]);
        }
        foreach ($this->localizedFields as $field) {
            $query->andWhere([$locAlias . "." . $field => $model->{$field}]);
        }

        $result = $query->all();
        if (count($result)>0){
            $this->addError($model, $attribute,  Yii::t('core', 'Значение «{attribute}» должно быть уникальным.', ['attribute' => $attribute]));
            return false;
        }

        return true;
    }
}
