<?php

namespace concepture\yii2logic\filters;

use yii\base\InvalidConfigException;
use yii\filters\AccessRule as Base;
use yii\web\User;


class AccessRule extends Base
{
    /**
     * @param User $user the user object
     * @return bool whether the rule applies to the role
     * @throws InvalidConfigException if User component is detached
     */
    protected function matchRole($user)
    {
        $items = empty($this->roles) ? [] : $this->roles;

        if (!empty($this->permissions)) {
            $items = array_merge($items, $this->permissions);
        }

        if (empty($items)) {
            return true;
        }

        if ($user === false) {
            throw new InvalidConfigException('The user application component must be available to specify roles in AccessRule.');
        }

        foreach ($items as $item) {
            if ($item === '?') {
                if ($user->getIsGuest()) {
                    return true;
                }
            } elseif ($item === '@') {
                if (!$user->getIsGuest()) {
                    return true;
                }
            } else {
                if (!isset($roleParams)) {
                    $roleParams = !is_array($this->roleParams) && is_callable($this->roleParams) ? call_user_func($this->roleParams, $this) : $this->roleParams;
                }
                /**
                 * Только чтобы подсунуть экшоны в правило
                 */
                $roleParams['action'] = $this->actions;
                if ($user->can($item, $roleParams)) {
                    return true;
                }
            }
        }

        return false;
    }
}
