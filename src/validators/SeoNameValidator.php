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
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class SeoNameValidator extends Validator
{
    /**
     * Менять значение при изменениии
     * @var boolean
     */
    public $changeOnEdit = false;
    public $skipOnEmpty = false;

    public function validateAttribute($model, $attribute)
    {
        if(! $model->{$attribute} && ! $this->changeOnEdit){
            return;
        }

        $model->{$attribute} = Transliterator::translit($model->{$attribute});
    }
}
