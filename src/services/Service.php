<?php
namespace concepture\yii2logic\services;

use ReflectionException;
use yii\base\Component;
use yii\db\Connection;
use ReflectionClass;
use concepture\yii2logic\services\traits\ModifyTrait;
use concepture\yii2logic\services\traits\ReadTrait;

/**
 * Class Service
 * @package concepture\yii2logic\services
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class Service extends Component
{
    use ModifyTrait;
    use ReadTrait;

    /**
     * @return Connection
     * @throws ReflectionException
     */
    public function getDb()
    {
        $modelClass = $this->getRelatedModelClass();

        return $modelClass::getDb();
    }

    /**
     * Получить класс связанной модели
     * @return string
     * @throws ReflectionException
     */
    public function getRelatedModelClass()
    {
        $reflection = new ReflectionClass($this);
        $name = $reflection->getShortName();
        $name = str_replace("Service", "", $name);
        $nameSpace = $reflection->getNamespaceName();
        $nameSpace = str_replace("services", "models", $nameSpace);

        return  $nameSpace."\\".$name;
    }

    /**
     * Получить класс связанной формы
     * @return string
     * @throws ReflectionException
     */
    public function getRelatedFormClass()
    {
        $reflection = new ReflectionClass($this);
        $name = $reflection->getShortName();
        $name = str_replace("Service", "", $name);
        $nameSpace = $reflection->getNamespaceName();
        $nameSpace = str_replace("services", "forms", $nameSpace);

        return  $nameSpace."\\".$name."Form";
    }

    /**
     * Получить класс связанной search модели
     * @return string
     * @throws ReflectionException
     */
    public function getRelatedSearchModelClass()
    {
        $reflection = new ReflectionClass($this);
        $name = $reflection->getShortName();
        $name = str_replace("Service", "", $name);
        $nameSpace = $reflection->getNamespaceName();
        $nameSpace = str_replace("services", "forms", $nameSpace);

        return  $nameSpace."\\".$name."Search";
    }
}