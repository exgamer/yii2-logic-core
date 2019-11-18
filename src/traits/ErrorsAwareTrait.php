<?php

namespace concepture\yii2logic\traits;

/**
 * Трейт для хранения ошибок
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
trait ErrorsAwareTrait
{
    /**
     * @var array
     */
    private $errors = [];

    /**
     * Возвращает ошибки
     *
     * @return array
     */
    public function getErrors() : array
    {
        return $this->errors;
    }

    /**
     * Добавление ошибки
     *
     * @param mixed $item
     */
    public function addError($item)
    {
        $this->errors[] = $item;
    }

    /**
     * Добавление ошибок
     *
     * @param array $items
     */
    public function addErrors(array $items)
    {
        foreach ($items as $item) {
            $this->addError($item);
        }
    }

    /**
     * Очистка настроек
     */
    public function clearErrors()
    {
        $this->errors = [];
    }

    /**
     * Возвращает ошибки в виде строки
     *
     * @return string
     */
    public function getErrorsAsString() : string
    {
        $errors = $this->getErrors();
        if(! $errors) {
            return '';
        }

        return json_encode($errors);
    }

    /**
     * Наличие ошибок
     *
     * @return bool
     */
    public function hasErrors() : bool
    {
        return count($this->errors) > 0;
    }
}