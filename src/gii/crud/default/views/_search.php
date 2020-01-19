<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\crud\Generator */

echo "<?php\n";
?>

?>

<div class="row">
    <?php
    $count = 0;
    foreach ($generator->getColumnNames() as $attribute) {
        echo "<div class=\"col-lg-4 col-md-6 col-sm-12\">\n\n";
        if (++$count < 6) {
            echo "    <?= " . $generator->generateActiveSearchField($attribute) . " ?>\n\n";
        } else {
            echo "    <?php // echo " . $generator->generateActiveSearchField($attribute) . " ?>\n\n";
        }
        echo "</div>\n\n";
    }
    ?>
</div>

