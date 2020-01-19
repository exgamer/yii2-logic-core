<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

/* @var $model \yii\db\ActiveRecord */
$model = new $generator->modelClass();
$safeAttributes = $model->safeAttributes();
if (empty($safeAttributes)) {
    $safeAttributes = $model->attributes();
}

echo "<?php\n";
?>

use yii\helpers\Html;
use kamaelkz\yii2admin\v1\widgets\formelements\Pjax;
use kamaelkz\yii2admin\v1\widgets\formelements\activeform\ActiveForm;

$saveRedirectButton = Html::submitButton(
    '<b><i class="icon-list"></i></b>' . Yii::t('yii2admin', 'Сохранить и перейти к списку'),
    [
        'class' => 'btn bg-info btn-labeled btn-labeled-left ml-1',
        'name' => \kamaelkz\yii2admin\v1\helpers\RequestHelper::REDIRECT_BTN_PARAM,
        'value' => 'index'
    ]
);
$saveButton = Html::submitButton(
    '<b><i class="icon-checkmark3"></i></b>' . Yii::t('yii2admin', 'Сохранить'),
    [
        'class' => 'btn bg-success btn-labeled btn-labeled-left ml-1'
    ]
);
?>

<?= "<?php " ?> Pjax::begin(['formSelector' => '#active-form']); ?>
<?= "<?php " ?> $form = ActiveForm::begin(['id' => 'active-form']); ?>
<div class="card">
    <div class="card-body text-right">
        <?= "<?= " ?> $saveRedirectButton?>
        <?= "<?= " ?> $saveButton?>
    </div>
</div>
<div class="row">
        <?php foreach ($generator->getColumnNames() as $attribute) {
            if (in_array($attribute,['id', 'created_at', 'updated_at', 'is_deleted'])) {
                continue;
            }
            if (in_array($attribute, $safeAttributes)) {
                echo "    <div class=\"col-lg-12 col-md-12 col-sm-12\">\n\n";
                echo "    <?= " . $generator->generateActiveField($attribute) . " ?>\n\n";
                echo "   </div>\n\n";
            }
        } ?>
</div>

<div class="card">
    <div class="card-body text-right">
        <?= "<?= " ?> $saveRedirectButton?>
        <?= "<?= " ?> $saveButton?>
    </div>
</div>
<?= "<?php " ?> ActiveForm::end(); ?>
<?= "<?php " ?> Pjax::end(); ?>


