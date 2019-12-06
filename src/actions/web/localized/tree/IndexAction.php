<?php
namespace concepture\yii2logic\actions\web\localized\tree;

use yii\db\ActiveRecord;
use yii\web\NotFoundHttpException;
use Yii;
use yii\db\Exception;
use concepture\yii2logic\actions\traits\LocalizedTrait;
use concepture\yii2logic\actions\Action;

/**
 * Class IndexAction
 * @package concepture\yii2logic\actions\web\tree
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class IndexAction extends Action
{
    use LocalizedTrait;

    public $view = 'index';
    public $serviceMethod = 'getDataProvider';

    public function run($locale = null, $parent_id = null)
    {
        $searchClass = $this->getSearchClass();
        $searchModel = new $searchClass();
        $searchModel->load(Yii::$app->request->queryParams);
        $searchModel::$current_locale = $this->getConvertedLocale($locale);
        $searchModel->parent_id = $parent_id;
        $dataProvider =  $this->getService()->{$this->serviceMethod}([], [], $searchModel);

        return $this->render($this->view, [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}