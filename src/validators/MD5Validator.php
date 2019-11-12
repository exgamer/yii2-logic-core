<?php
namespace concepture\yii2logic\validators;

use Yii;
use yii\base\Exception;
use yii\validators\Validator;
use yii\db\ActiveRecord;

/**
 * Валидатор переводит в MD5 выбранный атрибут, используя значение $toAttr
 *
 * @author Kamaelkz
 */
class MD5Validator extends Validator
{
    /**
     * Атрибут источник для MD5
     * @var string
     */
    public $source;

    /**
     * Менять значение при изменениии
     * @var boolean
     */
    public $changeOnEdit = true;
    public $skipOnEmpty = false;
    public function init()
    {
        parent::init();
        if (! $this->source) {
            throw new Exception(Yii::t('yii', 'Свойство {$source} должно быть установлено.'));
        }
    }

    public function validateAttribute($model, $attribute)
    {
        if($model->{$attribute} && ! $this->changeOnEdit){
            return;
        }

        if ($model->{$this->source} === null || $model->{$this->source} === ''){
            return;
        }

        $result = $model->{$this->source};
        if(is_array($result)){
            $result = reset($result);
        }
        $model->{$attribute} = md5($result);
    }
}
