<?php

namespace concepture\yii2logic\data;

use yii\data\ArrayDataProvider as Base;

/**
 * Дата провайдер для реализации бесконечной подгрузки данных без запроса
 *
 * Class ArrayDataProvider
 * @package frontend\widgets
 * @author Olzhas Kulzhambekov <exgamer@live.ru>
 */
class ArrayDataProvider extends Base
{
    public $infinitePager = false;

    /**
     * Метод переопределен для того чтобы возвращались все данные при $infinitePager = true
     *
     * при использовании с ListView
     * на itemView показываем количество элементов widget.dataProvider.getPagination().getPageSize()
     * остальным даем класс для скрытия
     *
     *   {% set itemClass = '' %}
     *   {% if (index +1 > widget.dataProvider.getPagination().getPageSize()) %}
     *   {% set itemClass = 'd-none' %}
     *   {% endif %}
     *   <tr data-expanded="true" class="{{ itemClass }} ">
     *
     *   </tr>
     *
     * В классе Pager или JS реализуем логику для показа скрытых элементов при нажатии на кнопку пеиджера
     *
     * Пример:
     *  let itemsBlockSelector = '{$this->itemsSelector}';
     *   let pageSize = '{$this->pagination->pageSize}';
     *   let hiddenSelector = '{$this->hiddenSelector}';
     *   let countTextSelector = '{$this->countTextSelector}';
     *   $(document).on('click', '{$this->pagerSelector}' + ' ' + infinitPager{$id}.controlSelector, function(e) {
     *       e.preventDefault();
     *       var self = $(this);
     *       let items = $(itemsBlockSelector).children(hiddenSelector);
     *       let itemsAfterCount = items.length - pageSize;
     *       items.each(function( index ) {
     *       if ((index +1)  > pageSize){
     *          return false;
     *       }
     *
     *       $(this).removeClass(hiddenSelector.replace(".", ""));
     *   });
     *   items = $(itemsBlockSelector).children(hiddenSelector);
     *   if (items.length <  pageSize){
     *      $(countTextSelector).html(items.length);
     *   }
    *
    *    if (itemsAfterCount <= 0){
    *         self.hide();
    *    }
    *    });
     *
     * {@inheritdoc}
     */
    protected function prepareModels()
    {
        if (($models = $this->allModels) === null) {
            return [];
        }

        if (($sort = $this->getSort()) !== false) {
            $models = $this->sortModels($models, $sort);
        }

        if (($pagination = $this->getPagination()) !== false) {
            $pagination->totalCount = $this->getTotalCount();

            if ($pagination->getPageSize() > 0 && ! $this->infinitePager) {
                $models = array_slice($models, $pagination->getOffset(), $pagination->getLimit(), true);
            }
        }

        return $models;
    }
}