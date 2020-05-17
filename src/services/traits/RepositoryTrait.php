<?php
namespace concepture\yii2logic\services\traits;

use ReflectionException;
use Yii;
use concepture\yii2logic\services\repositories\Repository;

/**
 * Trait RepositoryTrait
 * @package concepture\yii2logic\services\traits
 */
trait RepositoryTrait
{
    /**
     * @var array
     */
    public $repos = [];

    /**
     * @param $shortClassName
     * @return Repository
     * @throws ReflectionException
     */
    public function getRepository($shortClassName)
    {
        if (! isset($this->repos[$shortClassName])) {
            $reflection = new \ReflectionClass($this);
            $nameSpace = $reflection->getNamespaceName();
            $class = $nameSpace. "\\repositories\\" . $shortClassName. "Repository";
            $this->repos[$shortClassName] = Yii::createObject(['class' => $class, 'service' => $this]);
        }

        return $this->repos[$shortClassName];
    }
}

