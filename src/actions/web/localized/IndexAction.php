<?php
namespace concepture\yii2logic\actions\web\localized;

use yii\db\ActiveRecord;
use yii\helpers\Url;
use yii\web\NotFoundHttpException;
use Yii;
use yii\db\Exception;
use concepture\yii2logic\actions\traits\LocalizedTrait;
use concepture\yii2logic\actions\Action;
/**
 * @deprecated
 *
 * Экшон для прсомотра списка локализованных сущностей
 *
 * Class IndexLocalizedAction
 * @package concepture\yii2logic\actions\web
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class IndexAction extends Action
{
    use LocalizedTrait;

    /**
     * @var string
     */
    public $view = 'index';
    /**
     * @var string
     */
    public $serviceMethod = 'getDataProvider';

    /**
     * @var bool
     */
    public $storeUrl = true;

    /**
     * @inheritDoc
     */
    public function run($locale = null)
    {
        $this->rememberUrl();
        $searchClass = $this->getSearchClass();
        $searchModel = Yii::createObject($searchClass);
        $searchModel->load(Yii::$app->request->queryParams);
        $searchModel::setLocale($locale);
        $dataProvider =  $this->getService()->{$this->serviceMethod}([], [], $searchModel);
        if($this->storeUrl) {
            $this->getController()->storeUrl();
        }

        return $this->render($this->view, [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }
}