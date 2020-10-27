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
 * DataHandler для создания несуществующих проперти на новых доменах
 *
 * Class PropertyCopyDataHandler
 * @package concepture\yii2logic\dataprocessor\v2\property
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
abstract class PropertyDomainCopyDataHandler extends DataHandler
{
    /**
     * массив id доменов на которых должны быть проперти
     *
     * @var integer[]
     */
    public $domainIds;

    public function setupQuery(ActiveQuery $query)
    {
        parent::setupQuery($query);
        $query->resetCondition();
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
            // получаем разницу массивов, тюею домены на которых проперти нет
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

        if (empty($insertData)) {
            $this->outputSuccess( "no data for create");
            return;
        }

        $this->outputSuccess( "data insert start - " . count($insertData) . " rows");
        $this->getService()->modifyProperty()->batchInsert(['entity_id', 'domain_id'], $insertData);
        $this->outputSuccess( "data insert complete - " . count($insertData) . " rows");
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
