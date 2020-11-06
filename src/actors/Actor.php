<?php
namespace concepture\yii2logic\actors;

use yii\base\Component;

/**
 * Class Actor
 * @package concepture\yii2logic\actors
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class Actor extends Component
{
    abstract function run();
}