<?php

namespace concepture\yii2logic\helpers;

use Yii;
use Exception;
use ReflectionClass;
use ReflectionException;

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

    public static function getShortClassName($objectOrClass)
    {
        $reflection = new ReflectionClass($objectOrClass);

        return $reflection->getShortName();
    }

    public static function getServiceName($objectOrClass, $replacedNamePart = [])
    {
        $name = static::getShortClassName($objectOrClass);
        $name = lcfirst($name);
        foreach ($replacedNamePart as $part){
            $name = str_replace($part, "", $name);
        }

        return $name."Service";
    }
}