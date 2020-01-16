<?php

use concepture\yii2logic\gii\model\Generator;

/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */
/* @var $generator concepture\yii2logic\gii\model\Generator */

echo $form->field($generator, 'tableName')->textInput(['table_prefix' => $generator->getTablePrefix()]);
echo $form->field($generator, 'modelClass');
echo $form->field($generator, 'ns');
echo $form->field($generator, 'baseClass');
echo $form->field($generator, 'generateRelations')->dropDownList([
    Generator::RELATIONS_NONE => 'No relations',
    Generator::RELATIONS_ALL => 'All relations',
    Generator::RELATIONS_ALL_INVERSE => 'All relations with inverse',
]);
echo $form->field($generator, 'generateLabelsFromComments')->checkbox();
echo $form->field($generator, 'enableI18N')->checkbox();
echo $form->field($generator, 'messageCategory');
echo $form->field($generator, 'useSchemaName')->checkbox();
