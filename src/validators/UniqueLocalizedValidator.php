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
    public $fields = [];

    /**
     * Локализованные поля для валидации
     * @var array
     */
    public $localizedFields = [];

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
        foreach ($this->fields as $field){
            $query->andWhere([$field => $model->{$field}]);
        }

        foreach ($this->localizedFields as $field) {
            $query->andWhere([$locAlias . "." . $field => $model->{$field}]);
        }

        if (isset($model->id)) {
            $query->andWhere(['<>', $locAlias . '.entity_id', $model->id]);
        }

        $result = $query->all();
        if (count($result)>0){
            $this->addError($model, $attribute,  Yii::t('core', 'Значение «{attribute}» должно быть уникальным.', ['attribute' => $attribute]));
            return false;
        }

        return true;
    }
}
