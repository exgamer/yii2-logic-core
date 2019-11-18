<?php

namespace concepture\yii2logic\traits;

/**
 * Трейт для хранения настроек класса
 *
 * @author kamaelkz <kamaelkz@yandex.kz>
 */
trait ConfigAwareTrait
{
    /**
     * @var array
     */
    private $config = [];

    /**
     * Получить значение из конфига
     *
     * @param mixed $value
     * @return mixed|null
     */
    public function getConfigItem($value)
    {
        $config = $this->getConfig();
        $nesting = explode('.' ,$value);
        $first = array_shift($nesting);
        if(! isset($config[$first])) {
            return null;
        }

        if(! $nesting) {
            return $config[$first];
        }

        $branch = $config[$first];
        foreach ($nesting as $level => $key) {
            if(! isset($branch[$key])) {
                return null;
            }

            $branch = &$branch[$key];
        }

        return $branch;
    }

    /**
     * Установка настроек
     *
     * @param array $data
     */
    public function setConfig(array $items)
    {
        $this->config = $items;
    }

    /**
     * Возвращает настройки
     *
     * @return array
     */
    public function getConfig() : array
    {
        return $this->config;
    }

    /**
     * Возвращает конфиг в виде json строки
     *
     * @return false|string
     */
    public function getConfigAsString()
    {
        return json_encode($this->config);
    }

    /**
     * Добавляет элементы
     *
     * @param array $items
     */
    public function pushConfig(array $items)
    {
        if(! $this->config) {
            $this->config = $items;

            return;
        }

        foreach ($items as $key => $value) {
            $this->config[$key] = $value;
        }
    }

    /**
     * Очистка настроек
     */
    public function clearConfig()
    {
        $this->config = [];
    }
}