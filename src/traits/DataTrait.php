<?php

namespace concepture\yii2logic\traits;

use Exception;

/**
 * Trait DataTrait
 * @package concepture\yii2logic\traits
 */
trait DataTrait
{
    /**
     * Для хранения данных
     * @var mixed
     */
    public $data = [];

    /**
     * запись данных класса
     *
     * $this->setData(['apple], 'fruits');
     *
     * !!! if use callback dont forget return $staticData
     *   $this->setData(function ($staticData) use ($data){
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
    public function setData($data ,$key = null)
    {
        if (is_callable($data)){
            $data = call_user_func($data, $this->getData());
            $this->data = $data;

            return true;
        }

        if (! $key){
            throw new Exception("set key for data");
        }

        $this->data[$key] = $data;

        return true;
    }

    /**
     * получение данных класса
     *
     * @param string|int|null $key
     * @return mixed|null
     */
    public function getData($key = null)
    {
        if (! $key){
            return $this->data ?? null;
        }

        return $this->data[$key] ?? null;
    }
}