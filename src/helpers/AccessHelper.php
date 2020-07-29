<?php
namespace concepture\yii2logic\helpers;

use Yii;
use concepture\yii2logic\enum\AccessEnum;
use concepture\yii2logic\enum\PermissionEnum;

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
        'update-multiple',
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
     * AccessHelper::checkAccess(['site/CUSTOM_PEMISSION]);
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
            //сначала проверяем может это кастмное полномочие
            $customPerms = [
                static::getAccessPermission($controller, $name),
                static::getDomainAccessPermission($controller, $name)
            ];

            $existsPerm = [];
            foreach ($customPerms as $perm) {
                if (Yii::$app->rbacService->getPermission($perm)) {
                    $existsPerm[] = $perm;
                }
            }

            if ($existsPerm) {
                foreach ($existsPerm as $p) {
                    if (Yii::$app->user->can($p, $params)){
                        return true;
                    }
                }

                // если полномочия были кастомные и у юзера нет доступа возвращает false
                return false;
            }

            // если полночоие не кастомное надо вернуть true иначе сломается rbac
            return true;
        }

        if (! $controller){
            $controller = Yii::$app->controller;
        }

        $params['action'] = $name;
        $domain_id = null;
        if (isset($params['domain_id'])) {
            $domain_id = $params['domain_id'];
        }

        $permissions = static::getPermissionsByAction($controller, $name, $domain_id);
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
     * @param null $domain_id
     * @return array
     */
    public static function getPermissionsByAction($controller, $action, $domain_id = null)
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
                static::getDomainAccessPermission($controller, PermissionEnum::STAFF, $domain_id),
                static::getDomainAccessPermission($controller, PermissionEnum::EDITOR, $domain_id),
                static::getDomainAccessPermission($controller, PermissionEnum::READER, $domain_id),
                static::getDomainAccessPermission(null, PermissionEnum::STAFF, $domain_id),
                static::getDomainAccessPermission(null, PermissionEnum::EDITOR, $domain_id),
                static::getDomainAccessPermission(null, PermissionEnum::READER, $domain_id),
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
                static::getDomainAccessPermission($controller, PermissionEnum::STAFF, $domain_id),
                static::getDomainAccessPermission($controller, PermissionEnum::EDITOR, $domain_id),
                static::getDomainAccessPermission(null, PermissionEnum::STAFF, $domain_id),
                static::getDomainAccessPermission(null, PermissionEnum::EDITOR, $domain_id),
            ];
        }

        if ((! is_array($action) && in_array($action, static::$_sort_actions) )
            || (is_array($action) && $action === static::$_sort_actions)){
            return [
                AccessEnum::SUPERADMIN,
                AccessEnum::ADMIN,
                static::getAccessPermission($controller, PermissionEnum::ADMIN),
                static::getAccessPermission($controller, PermissionEnum::EDITOR),
                static::getDomainAccessPermission($controller, PermissionEnum::EDITOR, $domain_id),
                static::getDomainAccessPermission(null, PermissionEnum::EDITOR, $domain_id),
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

    /**
     * Возвращает значение полномочия для переданного контроллера на доступ к текущему домену
     *
     * @param $controller
     * @param $permission
     * @param null $domain_id
     * @return string
     */
    public static function getDomainAccessPermission($controller = null, $permission, $domain_id = null)
    {
        $name = '';
        if ($controller) {
            if (is_object($controller)) {
                $name = ClassHelper::getShortClassName($controller, 'Controller', true);
            } else {
                $name = str_replace("-", '', strtoupper($controller));
            }
            $name .= "_" ;
        }

        if (! $domain_id) {
            $data = Yii::$app->domainService->getCurrentDomainData();
        }else{
            $data = Yii::$app->domainService->getDomainDataById($domain_id);
        }

        $alias = $data['alias'] ?? "_";

        $role = $name . strtoupper($alias) . "_" . $permission;

        if ($controller) {
            return $role;
        }

        return str_replace('_', '', $role);
    }
}