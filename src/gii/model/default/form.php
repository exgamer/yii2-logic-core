<?php
/**
 * This is the template for generating the model class of a specified table.
 */

/* @var $this yii\web\View */
/* @var $generator yii\gii\generators\model\Generator */
/* @var $tableName string full table name */
/* @var $className string class name */
/* @var $queryClassName string query class name */
/* @var $tableSchema yii\db\TableSchema */
/* @var $properties array list of properties (property => [type, name. comment]) */
/* @var $labels string[] list of attribute labels (name => label) */
/* @var $rules string[] list of validation rules */
/* @var $relations array list of relations (name => relation declaration) */

echo "<?php\n";
?>

namespace <?= $formNs ?>;

<?php if (($generator->hasSeoProperty($properties))): ?>
use concepture\yii2logic\traits\SeoPropertyTrait;
<?php endif; ?>

/**
* This is the form class for model "<?= $generator->ns ?>\<?= $className ?>".
*
<?php foreach ($formProperties as $property => $data): ?>
    * @property <?= "{$data['type']} \${$property}"  . ($data['comment'] ? ' ' . strtr($data['comment'], ["\n" => ' ']) : '') . "\n" ?>
<?php endforeach; ?>
*/
class <?= $formName ?> extends <?= '\\' . ltrim($formBaseClass, '\\') . "\n" ?>
{
    <?php if (($generator->hasSeoProperty($properties))): ?>
    use SeoPropertyTrait;
    <?php endif; ?>

    <?php foreach ($formProperties as $property => $data): ?>
    public $<?= $property; ?>; <?="\n"?>
    <?php endforeach; ?>

    /**
    * {@inheritdoc}
    */
    public function formRules()
    {
        return [

        <?php if (! empty($rules)) :?>
            <?php foreach ($rules as $rule): ?>
                <?php if (strpos($rule, 'required')) :?>
                    <?= $rule; ?>
                    <?php continue; ?>
                <?php endif;?>
            <?php endforeach; ?>
        <?php endif;?>

        ];
    }
}
