<?php

namespace concepture\yii2logic\traits;

use Exception;

/**
 * Trait StaticDataTrait
 * @package concepture\yii2logic\traits
 */
trait StaticDataTrait
{
    /**
     * Для хранения статических данных
     * @var mixed
     */
    static $static_data;

    /**
     * запись статических данных сервиса
     *
     * $this->setStaticData(['apple], 'fruits');
     *
     * !!! if use callback dont forget return $staticData
     *   $this->setStaticData(function ($staticData) use ($data){
     *       $staticData['ratings'][$data['domain_id']][] = $data['mark'];
     *       return $staticData;
     *   });
     *
     *
     * @param $data
     * @param null $key
     * @return bool
     * @throws Exception
     */
    public function setStaticData($data ,$key = null)
    {
        if (is_callable($data)){
            $data = call_user_func($data, $this->getStaticData());
            static::$static_data[static::class] = $data;

            return true;
        }

        if (! $key){
            throw new Exception("set key for data");
        }

        static::$static_data[static::class][$key] = $data;

        return true;
    }

    /**
     * получение статических данных сервиса
     *
     * @param string|int|null $key
     * @return mixed|null
     */
    public function getStaticData($key = null)
    {
        if (! $key){
            return static::$static_data[static::class] ?? null;
        }

        return static::$static_data[static::class][$key] ?? null;
    }
}