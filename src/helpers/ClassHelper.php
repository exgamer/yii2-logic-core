<?php

namespace concepture\yii2logic\helpers;

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
        foreach ($replacedClassNameParts as $from => $to){
            $name = str_replace($from, $to, $name);
        }
        $nameSpace = $reflection->getNamespaceName();
        foreach ($replacedNamespaceParts as $from => $to){
            $nameSpace = str_replace($from, $to, $nameSpace);
        }
        $class = $nameSpace."\\".$name;
        if (! class_exists($class)){
            throw new Exception("related class {$class} does not exists");
        }

        return  $class;
    }

    public static function getShortClassName($objectOrClass)
    {
        $reflection = new ReflectionClass($objectOrClass);

        return $reflection->getShortName();
    }
}