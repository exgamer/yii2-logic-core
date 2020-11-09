<?php
namespace concepture\yii2logic\validators\v2;

use concepture\yii2logic\forms\Form;
use concepture\yii2logic\helpers\ClassHelper;
use Yii;
use yii\base\Exception;
use yii\validators\Validator;
use yii\db\ActiveRecord;
/**
 * Валидатор на уникальность по свойству
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class UniquePropertyValidator extends Validator
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
    public $propertyFields = [];
    /**
     * @var string название колонки для перевязки с основной сущностью
     */
    public $linkedEntityColumnName = 'entity_id';

    /**
     * @var string
     */
    public $serviceName;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        if (! $this->propertyFields) {
            throw new Exception(Yii::t('yii', 'Свойство {$propertyFields} должно быть установлено.'));
        }

        if (! is_array($this->propertyFields)){
            $this->propertyFields = [$this->propertyFields];
        }

        if ($this->fields && ! is_array($this->propertyFields)){
            $this->fields = [$this->fields];
        }
    }

    public function validateAttribute($model, $attribute)
    {
        if ($model instanceof Form){
            $arClass = $model::getModelClass();
            $propertyAlias = $arClass::propertyAlias();
        }else{
            $propertyAlias = $model::propertyAlias();
        }

        if($this->serviceName) {
            $serviceName = $this->serviceName;
        } else {
            $serviceName = ClassHelper::getServiceName($model, "Form");
        }
        
        $query = Yii::$app->{$serviceName}->getQuery();
        foreach ($this->fields as $field){
            $query->andWhere([$field => $model->{$field}]);
        }

        foreach ($this->propertyFields as $field) {
            $query->andWhere([$propertyAlias . "." . $field => $model->{$field}]);
        }

        if (isset($model->id)) {
            $query->andWhere(['<>', $propertyAlias . ".{$this->linkedEntityColumnName}", $model->id]);
        }

        $result = $query->all();
        if (count($result)>0){
            $this->addError($model, $attribute,  Yii::t('core', 'Значение «{attribute}» должно быть уникальным.', ['attribute' => $attribute]));
            return false;
        }

        return true;
    }
}
