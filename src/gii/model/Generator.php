<?php

namespace concepture\yii2logic\gii\model;


class Generator extends \yii\gii\generators\model\Generator
{
    public $messageCategory = 'common';
    public $generateRelations = self::RELATIONS_NONE;
    public $enableI18N = true;
    public $ns = 'common\models';
    public $baseClass = 'concepture\yii2logic\models\ActiveRecord';
}
