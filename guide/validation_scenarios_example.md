####Мини гайд по использованию сценриев валидации

1. Рассмотрим использвоание сценариев для валидации на примере формы
    Реализуем метод extendedScenarios как в примере, где в значении указываетс массив с атрибутами 
    которые нужно исключить из валидации
2.  Важно для использования выставления сценария через метод baforeValidate формы
    на пресдатвлении у ActiveForm должна быть отлючена клиентская валидация 
    *<?php $form = ActiveForm::begin(['enableClientValidation'=>false]) ?>*
    
    

```php

<?php
namespace concepture\yii2banner\forms;


use concepture\yii2banner\enum\BannerTypesEnum;
use concepture\yii2logic\forms\Form;
use concepture\yii2logic\enum\StatusEnum;
use Yii;

/**
 * Class BannerForm
 * @package concepture\yii2banner\forms
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class BannerForm extends Form
{
    public $type;
    public $user_id;
    public $domain_id;
    public $locale = "ru";
    public $title;
    public $content;
    public $seo_name;
    public $image;
    public $from_at;
    public $to_at;
    public $url;
    public $target;
    public $status = StatusEnum::INACTIVE;

    protected function extendedScenarios()
    {
        return [
            BannerTypesEnum::IMAGE => [
                'content'
            ],
            BannerTypesEnum::HTML => [
                'image'
            ]
        ];
    }

    /**
     * @see Form::formRules()
     */
    public function formRules()
    {
        return [
            [
                [
                    'title',
                    'locale',
                    'image',
                    'type',
                    'content'
                ],
                'required'
            ],
        ];
    }

    public function beforeValidate()
    {
        /**
         * выставляем сценарий в зависимости от type
         */
        if ($this->type) {
            $this->setScenario($this->type);
        }

        return true;
    }
}

```

3. Пример формы со сценариями которые зависят от типа
    На событие onchange dropDownList отправляем ПОСТ с выставленным типом и метку reload=true чтобы не произошло сохранение формы

```php
<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use mihaildev\ckeditor\CKEditor;
use yii\widgets\Pjax;
use concepture\yii2handbook\enum\TargetAttributeEnum;
use concepture\yii2banner\enum\BannerTypesEnum;
?>

<div class="post-category-form">
    <?php Pjax::begin(['id' => 'form']); ?>


    <?php $form = ActiveForm::begin(['enableClientValidation'=>false]) ?>

    <?= $form->field($model, 'type')->dropDownList(
        BannerTypesEnum::arrayList(),
        [
            'onchange'=> "$.pjax.reload({container: '#form', 'type': 'POST', 'data': {'BannerForm[type]': this.value, 'reload': true}});"
        ]
    );?>
    <?php if ($model->type == BannerTypesEnum::IMAGE) :?>
        <?= $form->field($model, 'image')->textInput(['maxlength' => true]) ?>
    <?php endif;?>

    <?php if ($model->type == BannerTypesEnum::HTML) :?>
        <?= $form->field($model, 'content')->widget(CKEditor::className(),[
            'editorOptions' => [
                'preset' => 'full', //разработанны стандартные настройки basic, standard, full данную возможность не обязательно использовать
                'inline' => false, //по умолчанию false
                'allowedContent' => true,
            ],
        ]); ?>
    <?php endif;?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('banner', 'Сохранить'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>
    <?php Pjax::end(); ?>
</div>


```