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

$saveRedirectButton = Html::saveRedirectButton();
$saveButton = Html::saveButton();
?>

<?= "<?php " ?> Pjax::begin(['formSelector' => '#active-form']); ?>
<?= "<?php " ?> $form = ActiveForm::begin(['id' => 'active-form']); ?>
<div class="card">
    <div class="card-body text-right">
        <?= "<?= " ?> $saveRedirectButton?>
        <?= "<?= " ?> $saveButton?>
    </div>
</div>
<?= "<?= " ?> $form->errorSummary($model);?>
<div class="card">
    <div class="card-body">
        <div class="row">
            <?php foreach ($generator->getColumnNames() as $attribute) {
                if (in_array($attribute,['id', 'created_at', 'updated_at', 'is_deleted'])) {
                    continue;
                }

                if ($generator->isSeoProperty($attribute)){
                    continue;
                }

                if (in_array($attribute, $safeAttributes)) {
                    echo "    <div class=\"col-lg-12 col-md-12 col-sm-12\">\n\n";
                    echo "    <?= " . $generator->generateActiveField($attribute) . " ?>\n\n";
                    echo "   </div>\n\n";
                }
            } ?>
        </div>
        <?php if (($generator->hasSeoProperty($generator->getColumnNames()))): ?>
            <?= "<?= " ?>
            $this->render('@concepture/yii2handbook/views/include/_seo_attributes', [
            'form' => $form,
            'model' => $model,
            'originModel' => $originModel ?? null,
            ]);
            ?>
        <?php endif; ?>
    </div>


</div>
<div class="card">
    <div class="card-body text-right">
        <?= "<?= " ?> $saveRedirectButton?>
        <?= "<?= " ?> $saveButton?>
    </div>
</div>
<?= "<?php " ?> ActiveForm::end(); ?>
<?= "<?php " ?> Pjax::end(); ?>


