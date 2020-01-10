<?php
namespace concepture\yii2logic\validators;

use Yii;
use yii\base\Exception;
use yii\validators\Validator;
use yii\db\ActiveRecord;
use concepture\yii2logic\helpers\Transliterator;

/**
 * Валидатор переводит в транслит выбранный атрибут, используя значение $toAttr
 *
 * @author Kamaelkz
 */
class TranslitValidator extends Validator
{
    /**
     * Основной атрибут источник для транслита
     * @var string
     */
    public $source;

    /**
     * Второстепенный атрибут источник для транслита
     * @var string
     */
    public $secondary_source;

    /**
     * Менять значение при изменениии
     * @var boolean
     */
    public $changeOnEdit = false;
    public $skipOnEmpty = false;

    public function validateAttribute($model, $attribute)
    {
        if($model->{$attribute} && ! $this->changeOnEdit){
            return;
        }

        if (! $model->{$this->source}){
            $model->{$this->source} = $model->{$this->secondary_source};
        }

        if (! $this->source) {
            throw new Exception(Yii::t('yii', 'Свойство {$source} должно быть установлено.'));
        }

        $result = $model->{$this->source};
        if(is_array($result)){
            $result = reset($result);
        }
        $model->{$attribute} = Transliterator::translit($result);
    }
}
