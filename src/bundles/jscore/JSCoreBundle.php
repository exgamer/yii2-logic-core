<?php

namespace concepture\yii2logic\bundles\jscore;

use frontend\components\cdn\AssetsBundle;
use common\helpers\AppHelper;

/**
 * Class JSCoreBundle
 * @package concepture\yii2logic\bundles\jscore
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class JSCoreBundle extends AssetsBundle
{
    public $js = [];

    public function init()
    {
        $this->js = [
            'scripts/jquery-3.5.1.min.js',
            'scripts/core/helpers/lodash.js',
            'scripts/core/helpers/modal/util.js',
            'scripts/fingerprint2.min.js',
            'scripts/core/helpers/modal/bootstrap-modal.js',
            'scripts/core/helpers/app-helpers.js',
            'scripts/core/app.js',
            'scripts/app.js',
            [
                'scripts/modernizr-custom.js',
            ],
            [
                'scripts/main.js',
            ],
            [
                'scripts/autosize.min.js',
            ],
            [
                'scripts/tracker.js',
                'defer' => true,
            ],
            [
                'scripts/lazysizes.min.js',
                'async' => true,
            ],
        ];

        parent::init();
    }
}