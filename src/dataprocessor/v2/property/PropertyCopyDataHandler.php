<?php

namespace concepture\yii2logic\dataprocessor\v2\property;

use concepture\yii2logic\dataprocessor\v2\DataHandler;
use concepture\yii2logic\services\Service;
use Exception;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\ActiveQuery;
use yii\db\Query;

/**
 * Class ReviewDataHandler
 *
 *
 * @package console\datahandlers
 */
abstract class PropertyCopyDataHandler extends DataHandler
{
    public $domainIds;

//    public function beforeExecute()
//    {
//        $enabledDomainData = $this->domainService()->getEnabledDomainData();
//        $this->domainIds = array_keys($enabledDomainData);
//    }

    public function setupQuery(ActiveQuery $query)
    {
        parent::setupQuery($query);
//        $query->innerJoin('dynamic_elements_v2 d', "dynamic_elements_property.entity_id = d.id AND d.name NOT LIKE 'FUTSAL%'");
        $query->andWhere(['domain_id' => $this->domainIds]);
        $query->asArray();
    }

    public function processModel(&$data)
    {
        parent::processModel($data);
        $this->setData(function ($d) use ($data) {
            $d['data'][$data['entity_id']][] = (int) $data['domain_id'];

            return $d;
        });
    }

    public function afterExecute()
    {
        parent::afterExecute();
        $data = $this->getData('data');
        $insertData = [];
        foreach ($data as $entity_id => $domain_ids) {
            $diff = array_diff($this->domainIds, $domain_ids);
            if (empty($diff)) {
                continue;
            }

            foreach ($diff as $domain_id) {
                $insertData[$entity_id] = [
                    'entity_id' => $entity_id,
                    'domain_id' => $domain_id,
                ];
            }

        }
        $this->getService()->batchInsert(['entity_id', 'domain_id'], $insertData);
    }

    /**
     * @return Service
     */
    public abstract function getService();

    /**
     * @return Query
     */
    public function getQuery()
    {
        return $this->getService()->readProperty()->getQuery();
    }

    /**
     * @param $config
     * @return ActiveDataProvider
     * @throws Exception
     */
    public function getDataProvider($config)
    {
        return $this->getService()->readProperty()->getDataProvider([], $config);
    }
}
