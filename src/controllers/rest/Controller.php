<?php
namespace concepture\yii2logic\controllers\rest;

use Yii;
use yii\filters\Cors;
use yii\rest\Controller as Base;

/**
 * Class Controller
 * @package concepture\yii2logic\controllers\rest
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class Controller extends Base
{
    public function behaviors()
    {
        $b = parent::behaviors();
        $b['corsFilter'] = [
            'class' => Cors::class,
        ];

        return $b;
    }

    public function init()
    {
        parent::init();
        Yii::$app->user->enableSession = false;
    }
}