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

namespace <?= $generator->ns ?>;

use Yii;

<?php if (($generator->hasStatusProperty($properties))): ?>
use concepture\yii2logic\models\traits\StatusTrait;
<?php endif; ?>
<?php if (($generator->hasIsDeletedProperty($properties))): ?>
use concepture\yii2logic\models\traits\IsDeletedTrait;
<?php endif; ?>
<?php if (($generator->hasSeoProperty($properties))): ?>
use concepture\yii2logic\models\traits\SeoTrait;
use yii\helpers\ArrayHelper;
<?php endif; ?>

/**
* This is the model class for table "<?= $generator->generateTableName($tableName) ?>".
*
<?php foreach ($properties as $property => $data): ?>
    * @property <?= "{$data['type']} \${$property}"  . ($data['comment'] ? ' ' . strtr($data['comment'], ["\n" => ' ']) : '') . "\n" ?>
<?php endforeach; ?>
<?php if (!empty($relations)): ?>
    *
    <?php foreach ($relations as $name => $relation): ?>
        * @property <?= $relation[1] . ($relation[2] ? '[]' : '') . ' $' . lcfirst($name) . "\n" ?>
    <?php endforeach; ?>
<?php endif; ?>
*/
class <?= $className ?> extends <?= '\\' . ltrim($generator->baseClass, '\\') . "\n" ?>
{
<?php if (($generator->hasStatusProperty($properties))): ?>
    use StatusTrait;
<?php endif; ?>
<?php if (($generator->hasIsDeletedProperty($properties))): ?>
    use IsDeletedTrait;
<?php endif; ?>
<?php if (($generator->hasSeoProperty($properties))): ?>
    use SeoTrait;
<?php endif; ?>


    /**
    * @see \concepture\yii2logic\models\ActiveRecord:label()
    *
    * @return string
    */
    public static function label()
    {
        return Yii::t('<?= $generator->messageCategory?>', '<?= $generator->generateTableName($tableName) ?>');
    }

    /**
    * @see \concepture\yii2logic\models\ActiveRecord:toString()
    * @return string
    */
    public function toString()
    {
        return $this->id;
    }

    /**
    * {@inheritdoc}
    */
    public static function tableName()
    {
        return '<?= $generator->generateTableName($tableName) ?>';
    }
<?php if ($generator->db !== 'db'): ?>

    /**
    * @return \yii\db\Connection the database connection used by this AR class.
    */
    public static function getDb()
    {
        return Yii::$app->get('<?= $generator->db ?>');
    }
<?php endif; ?>

    /**
    * {@inheritdoc}
    */
    public function rules()
    {
    <?php if (($generator->hasSeoProperty($properties))): ?>
        return ArrayHelper::merge(
        $this->seoRules(),
        [
    <?php else: ?>
        return [
    <?php endif; ?>
    <?= empty($rules) ? '' : ("\n            " . implode(",\n            ", $rules) . ",\n        ") ?>
    <?php if (($generator->hasSeoProperty($properties))): ?>
        ]);
    <?php else: ?>
        ];
    <?php endif; ?>
    }

    /**
    * {@inheritdoc}
    */
    public function attributeLabels()
    {
    <?php if (($generator->hasSeoProperty($properties))): ?>
        return ArrayHelper::merge(
        $this->seoAttributeLabels(),
        [
    <?php else: ?>
        return [
    <?php endif; ?>

    <?php foreach ($labels as $name => $label): ?>
        <?= "'$name' => " . $generator->generateString($label) . ",\n" ?>
    <?php endforeach; ?>

    <?php if (($generator->hasSeoProperty($properties))): ?>
        ]);
    <?php else: ?>
        ];
    <?php endif; ?>
    }
    <?php foreach ($relations as $name => $relation): ?>

    /**
    * @return \yii\db\ActiveQuery
    */
    public function get<?= $name ?>()
    {
    <?= $relation[0] . "\n" ?>
    }
<?php endforeach; ?>
<?php if ($queryClassName): ?>
    <?php
    $queryClassFullName = ($generator->ns === $generator->queryNs) ? $queryClassName : '\\' . $generator->queryNs . '\\' . $queryClassName;
    echo "\n";
    ?>
    /**
    * {@inheritdoc}
    * @return <?= $queryClassFullName ?> the active query used by this AR class.
    */
    public static function find()
    {
    return new <?= $queryClassFullName ?>(get_called_class());
    }
<?php endif; ?>
}
