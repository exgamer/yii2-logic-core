<?php
namespace concepture\yii2logic\helpers;

use concepture\yii2logic\enum\AccessEnum;
use concepture\yii2logic\enum\PermissionEnum;
use Yii;

/**
 * Класс содержит вспомогательные методы для рабоыт с rbac
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class AccessHelper
{
    /**
     * дефолтные экшоны чтения данных
     * @var string[]
     */
    static $_read_actions = [
        'index',
        'list',
        'view',
    ];

    /**
     * дефолтные экшоны модификации данных
     * @var string[]
     */
    static $_edit_actions = [
        'create',
        'update',
        'delete',
        'undelete',
        'status-change',
        'image-upload',
        'image-delete',
        'editable-column',
        'create-validate-attribute',
        'update-validate-attribute',
    ];

    /**
     * экшоны модуля сортировки
     * @var string[]
     */
    static $_sort_actions = [
        'sort',
        'position-sort-index',
    ];

    /**
     * Проверка прав доступа к текущему роуту
     *
     * @param array $params
     * @return bool
     */
    public static function checkCurrentRouteAccess( $params = [])
    {
        return static::checkAccess([Yii::$app->requestedRoute], $params);
    }

    /**
     * Проверка прав доступа
     *
     * Пример:
     *
     * AccessHelper::checkAccess('create');
     * AccessHelper::checkAccess(['site/index]);
     *
     * @param $name
     * @param array $params
     * @return bool
     */
    public static function checkAccess($name, $params = [])
    {
        $action = null;
        $controller = null;
        if (is_array($name)) {
            $tmp = trim($name[0], '/');
            $tmpArray = explode('/', $tmp);
            if (count($tmpArray) == 1) {
                $action = $tmpArray[0];
                $controller = Yii::$app->controller;
            }

            if (count($tmpArray) > 1) {
                $tmp1 = $tmpArray;
                $action = array_pop($tmp1);
                $controller = array_pop($tmp1);
            }
        }

        if ($action){
            $name = $action;
        }

        if (in_array($name, [ 'activate', 'deactivate' ])){
            $name = 'status-change';
        }

        /**
         * Если экшен не является дефолтно заданным значит нужно проверку ставить вручную
         */
        if (! in_array($name, static::$_edit_actions) && ! in_array($name, static::$_read_actions) && ! in_array($name, static::$_sort_actions)){
            return true;
        }

        if (! $controller){
            $controller = Yii::$app->controller;
        }

        $params['action'] = $name;
        $permissions = static::getPermissionsByAction($controller, $name);
        foreach ($permissions as $permission){
            if (Yii::$app->user->can($permission, $params)){
                return true;
            }
        }

        return false;
    }

    /**
     * Возвращает полномочия по экшену или массиву экшенов
     * @param $controller
     * @param $action
     * @return array
     */
    public static function getPermissionsByAction($controller, $action)
    {
        if ((! is_array($action) && in_array($action, static::$_read_actions) )
            || (is_array($action) && $action === static::$_read_actions)){
            return [
                AccessEnum::SUPERADMIN,
                AccessEnum::ADMIN,
                static::getAccessPermission($controller, PermissionEnum::ADMIN),
                static::getAccessPermission($controller, PermissionEnum::STAFF),
                static::getAccessPermission($controller, PermissionEnum::EDITOR),
                static::getAccessPermission($controller, PermissionEnum::READER),
                static::getAccessPermission($controller, PermissionEnum::DOMAIN),
            ];
        }

        if ((! is_array($action) && in_array($action, static::$_edit_actions) )
            || (is_array($action) && $action === static::$_edit_actions)){
            return [
                AccessEnum::SUPERADMIN,
                AccessEnum::ADMIN,
                static::getAccessPermission($controller, PermissionEnum::ADMIN),
                static::getAccessPermission($controller, PermissionEnum::STAFF),
                static::getAccessPermission($controller, PermissionEnum::EDITOR),
                static::getAccessPermission($controller, PermissionEnum::DOMAIN),
            ];
        }

        if ((! is_array($action) && in_array($action, static::$_sort_actions) )
            || (is_array($action) && $action === static::$_sort_actions)){
            return [
                AccessEnum::SUPERADMIN,
                AccessEnum::ADMIN,
                static::getAccessPermission($controller, PermissionEnum::ADMIN),
                static::getAccessPermission($controller, PermissionEnum::EDITOR),
                static::getAccessPermission($controller, PermissionEnum::DOMAIN),
            ];
        }

        return [];
    }


    /**
     * Возвращает базовые правила доступа
     * @param $controller
     * @return array
     */
    public static function getDefaultAccessRules($controller)
    {
        $rules = [];
        /**
         * Просмотр
         */
        $rules[] = [
            'actions' => static::$_read_actions,
            'allow' => true,
            'roles' => static::getPermissionsByAction($controller, static::$_read_actions),
            'roleParams' => [
                'action' => static::$_read_actions,
            ]
        ];
        /**
         * Модификация
         */
        $rules[] = [
            'actions' => static::$_edit_actions,
            'allow' => true,
            'roles' => static::getPermissionsByAction($controller, static::$_edit_actions),
            'roleParams' => [
                'action' => static::$_read_actions,
            ]
        ];
        /**
         * Сортировка
         */
        $rules[] = [
            'actions' => static::$_sort_actions,
            'allow' => true,
            'roles' => static::getPermissionsByAction($controller, static::$_sort_actions),
            'roleParams' => [
                'action' => static::$_read_actions,
            ]
        ];

        return $rules;
    }

    /**
     * Возвращает значение полномочия для переданного контроллера
     * @param $controller
     * @param $permission
     * @return string
     */
    public static function getAccessPermission($controller, $permission)
    {
        if (is_object($controller)) {
            $name = ClassHelper::getShortClassName($controller, 'Controller', true);
        }else{
            $name = str_replace("-", '', strtoupper($controller));
        }

        return $name . "_" . $permission;
    }
}