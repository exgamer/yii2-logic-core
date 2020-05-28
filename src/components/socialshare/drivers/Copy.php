<?php
namespace concepture\yii2logic\components\socialshare\drivers;

use Yii;
use ymaker\social\share\base\AbstractDriver;

/**
 * Драйвер для копирования ссылки в буфер обмена для
 * https://github.com/yiimaker/yii2-social-share
 *
 * @see guide/social_sharing.md
 *
 * Class Copy
 * @package frontend\components\socialshare\drivers
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class Copy extends AbstractDriver
{
    public function init()
    {
        parent::init();
        $script = <<< JS
        $(function() {
            function copyStringToClipboard (str) {
               // Create new element
               var el = document.createElement('textarea');
               // Set value (string to be copied)
               el.value = str;
               // Set non-editable to avoid focus and move outside of view
               el.setAttribute('readonly', '');
               el.style = {position: 'absolute', left: '-9999px'};
               document.body.appendChild(el);
               // Select text inside element
               el.select();
               // Copy text to clipboard
               document.execCommand('copy');
               // Remove temporary element
               document.body.removeChild(el);
            }

            $(document).on('click', '.js-copy-to-clipboard', function(event) {
                event.preventDefault();
                copyStringToClipboard("{$this->url}");
            });
        });
JS;
        Yii::$app->view->registerJs($script);

    }

    /**
     * {@inheritdoc}
     */
    protected function processShareData()
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function buildLink()
    {
        return $this->url;
    }
}
