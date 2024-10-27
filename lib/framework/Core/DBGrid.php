<?php

namespace Lib\Framework\Core;

use Lib\Framework\Helpers\Html;
use const Lib\Framework\Resources\DICTIONARY;

class DBGrid
{
    public function prepareDataGridConfiguration($tableName, $table, $pk, $fk, $routes, $header = array(), $indexes = array(), $features = array())
    {
        $tableParams = array(
            'class' => 'table table-responsive table-condensed table-hover',
            'pk' => $pk,
            'id' => 'tb' . $tableName,
            'th' => $header,
            'tr' => $table,
            'idx' => $indexes,
            'action' => array(
                'hrefEdit' => $routes['update'] . '/%s',
                'hrefDelete' => $routes['delete'] . '/%s',
                'linkEdit' => '<i class="glyphicon glyphicon-pencil"></i>Edit',
                'linkDelete' => '<i class="glyphicon glyphicon-remove"></i>Delete',
            ),
            'features' => $features,
        );

        if (!empty($fk)) {
            $tableParams['fk'] = $fk;
        }
        return $tableParams;
    }
    public function createGrid($data, $routes, $tableName)
    {
        $helper = new Html();
        $table = $data;
        $pk = DICTIONARY[$tableName]['pk'];
        $fk = DICTIONARY[$tableName]['fk'];
        $header = $this->i18nTableHeaders(DICTIONARY[$tableName]['header']);
        $indexes = DICTIONARY[$tableName]['indexes'];
        $tableParams = $this->prepareDataGridConfiguration($tableName, $table, $pk, $fk, $routes, $header, $indexes);
        return $helper->table($tableParams);
    }

    public function i18nTableHeaders($tableHeader)
    {
        $translatedHeaders = array();
        foreach ($tableHeader as $label) {
            $translatedHeaders[] = ResourceManager::TextFor($label);
        }
        return $translatedHeaders;
    }

    public function createPagination($countTotal, $start, $limit, $page, $routes)
    {
        $helper = new Html();

        $pagination = array(
            'total' => $countTotal,
            'start' => $start,
            'limit' => $limit,
            'page' => $page,
            'first' => '<<',
            'last' => '>>',
            'ahrefLink' => $routes['paging']
        );

        return $helper->pagination($pagination);
    }
}
