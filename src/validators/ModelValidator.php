<?php

namespace concepture\yii2logic\validators;

use Yii;
use yii\validators\Validator;
use yii\helpers\Json;
use concepture\yii2logic\forms\Model;

/**
 * Универсальный валидатор для возможности валидации массивов данных с помощью моделей
 *
 * @todo переработать отдачу ошибок
 *
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class ModelValidator extends Validator
{
    /**
     * @var \concepture\yii2logic\forms\Model
     */
    public $modelClass;

    /**
     * @var bool
     */
    public $asArray = false;

    /**
     * @var array
     */
    public $errors = [];

    /**
     * @todo продумать поведение
     * @var bool признак модификации исходного атрибута
     */
    public $modifySource = true;

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();
        if ($this->modelClass === null) {
            $this->modelClass = Yii::t('core', '{attribute} must set a model class.');
        }

        if ($this->asArray) {
            $this->asArray = true;
        }
    }

    /**
     * @inheritDoc
     */
    public function validateAttribute($model, $attribute)
    {
        if (! $this->asArray) {
            $result = $this->validateModel($model->{$attribute});
            if ($result == false){
                $this->addError($model, $attribute,  $this->message ?? Json::encode($this->errors));
                return false;
            }

            if($this->modifySource === true) {
                $model->{$attribute} = $result;
            }
            
            return true;
        }

        $dataArray = [];
        if(! is_array($model->{$attribute})) {
            $this->addError($model, $attribute,  Yii::t('core', 'Значение «{attribute}» должно быть массивом.', ['attribute' => $attribute]));

            return false;
        }

        foreach ($model->{$attribute} as $data) {
            $result = $this->validateModel($data);
            if ($result == false){
                $this->addError($model, $attribute,  $this->message ?? Json::encode($this->errors));

                return false;
            }

            $dataArray[] = $result;
        }

        if (
            ! empty($dataArray)
            && $this->modifySource === true
        ){
            $model->{$attribute} = $dataArray;
        }
        
        return true;
    }

    /**
     * Валидация данных через класс `modelClass`
     *
     * @param mixed $value
     * @return bool
     */
    protected function validateModel($value)
    {
        $validatorModel = new $this->modelClass();
        $validatorModel->load($value, '');
        if (! $validatorModel->validate()) {
            $this->errors[] = $validatorModel->getErrors();

            return false;
        }
        
        return $validatorModel->attributes;
    }
}