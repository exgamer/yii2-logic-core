<?php

namespace concepture\yii2logic\helpers;

use Yii;
use Exception;
use ReflectionClass;
use ReflectionException;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

/**
 * Class ClassHelper
 * @package concepture\yii2logic\helpers
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class ClassHelper
{
    /**
     * Возвращает связанный класс для обьекта
     * используется для получения например связанной формы для сервиса или связанной модели для формы
     *
     * @param $object
     * @param array $replacedClassNameParts
     * @param array $replacedNamespaceParts
     * @return string
     * @throws ReflectionException
     */
    public static function getRelatedClass($object, array $replacedClassNameParts = [], array $replacedNamespaceParts = [])
    {
        $reflection = new ReflectionClass($object);
        $name = $reflection->getShortName();
        $nameSpace = $reflection->getNamespaceName();
//        $originClass = $nameSpace."\\".$name;
//        $definitions = Yii::$container->getDefinitions();
//        foreach ($definitions as $alias => $definition){
//            if (! isset($definition['class'])){
//                continue;
//            }
//            if ($definition['class'] == $originClass){
//                $obj = new $alias();
//                $ref = new ReflectionClass($obj);
//                $nameSpace = $ref->getNamespaceName();
//            }
//        }
        foreach ($replacedClassNameParts as $from => $to){
            $name = str_replace($from, $to, $name);
        }

        foreach ($replacedNamespaceParts as $from => $to){
            $nameSpace = str_replace($from, $to, $nameSpace);
        }
        $class = $nameSpace."\\".$name;

        if (! class_exists($class)){
            /**
             * @todo Тут возможно костылек если связанный класс не найден начинаем копать вниз
             */
            $parentClass = get_parent_class($object);
            $class = static::getRelatedClass(new $parentClass, $replacedClassNameParts, $replacedNamespaceParts);

            //throw new Exception("related class {$class} does not exists" . $parentClass);
        }

        return  $class;
    }

    public static function getShortClassName($objectOrClass, $cutPart = null, $capitalize = false)
    {
        $reflection = new ReflectionClass($objectOrClass);
        $name = str_replace($cutPart, "", $reflection->getShortName());
        if ($capitalize){
            $name = strtoupper($name);
        }

        return $name;
    }

    public static function getTraits($objectOrClass)
    {
        if (is_object($objectOrClass)) {
            $objectOrClass = static::getRelatedClass($objectOrClass);
        }

        return array_keys((new ReflectionClass($objectOrClass))->getTraits());
    }

    public static function getServiceName($objectOrClass, $replacedNamePart = [])
    {
        $name = static::getShortClassName($objectOrClass);
        $name = lcfirst($name);
        if (! is_array($replacedNamePart)){
            $replacedNamePart = [$replacedNamePart];
        }
        foreach ($replacedNamePart as $part){
            $name = str_replace($part, "", $name);
        }

        return $name."Service";
    }

    public static function getServiceByEntityTable($tableName)
    {
        $serviceName =  lcfirst(Inflector::camelize($tableName));
        $serviceName.="Service";

        return $serviceName;
    }

    public static function modelToArray($model)
    {
        $modelClass = static::getRelatedClass($model);

        return ArrayHelper::toArray($model, [$modelClass => $model->attributes()]);
    }

    /**
     * @param object $object
     * @param mixed $modifiers
     * @return ReflectionProperty[]
     * @throws ReflectionException
     */
    public static function getProperties($object, $modifiers = \ReflectionProperty::IS_PUBLIC)
    {
        $reflection = new \ReflectionClass($object);
        return $reflection->getProperties($modifiers);
    }

    /**
     * Возвращает конфиг поведения обьекта по классу
     * @param $object
     * @param $class
     * @return mixed|null
     */
    public static function getBehavior($object, $class)
    {
        $behaviors = $object->behaviors();
        foreach ($behaviors as $name => $config){
            if (is_string($config) && $config === $class){
                return $config;
            }

            if (isset($config['class']) && $config['class'] === $class){
                return $config;
            }
        }

        return null;
    }
}