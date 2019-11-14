<?php
namespace concepture\yii2logic\forms;

use concepture\yii2logic\actions\traits\ModelScenarioTrait;
use yii\base\Model as Base;

/**
 * Class Model
 * @package concepture\yii2logic\forms
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class Model extends Base
{
    use ModelScenarioTrait;
}