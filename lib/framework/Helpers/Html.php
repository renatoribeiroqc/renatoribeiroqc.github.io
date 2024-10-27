<?php

/* Copyright (C) conexperience.com.br, Inc - All Rights Reserved
 * Unauthorized copying of this file, via any medium is strictly prohibited
 * Proprietary and confidential
 * Written by Renato Ribeiro <renato.ribeiro@conexperience.com.br>, June 2023
 */

namespace Lib\Framework\Helpers;

use Lib\Framework\Core\ResourceManager;
use Lib\Framework\Core\SecurityService;

class Html
{

    public static function control($params = array())
    {

        $control = '<div class="form-group">';
        if (
            array_key_exists('label', $params) &&
            array_key_exists('type', $params) &&
            $params['type'] != 'radio' &&
            $params['type'] != 'checkbox' &&
            $params['type'] != 'hidden'
        ) {
            $control .= '<label class="control-label">' .
                ResourceManager::TextFor($params['label']) . '</label>';
        }

        $control .= '<div class="controls">';
        if (array_key_exists('type', $params)) {
            $control .= '<input type="' . $params['type'] . '" ';
        }

        if (array_key_exists('name', $params)) {
            $control .= 'name="' . $params['name'] . '" ';
        }

        if (array_key_exists('value', $params)) {
            $control .= 'value="' . $params['value'] . '" ';
        }

        if (array_key_exists('id', $params)) {
            $control .= ' id="' . $params['id'] . '" ';
        }

        if (array_key_exists('class', $params)) {
            $control .= ' class="' . $params['class'] . '" ';
        }

        if (array_key_exists('required', $params)) {
            $control .= ' required = "' . $params['required'] . '" ';
        }
        if (array_key_exists('readonly', $params)) {
            $control .= ' readonly = "' . $params['readonly'] . '" ';
        }

        if (array_key_exists('checked', $params)) {
            $control .= ' ' . $params['checked'] . ' ';
        }
        if ($params['type'] == 'radio' || $params['type'] == 'checkbox') {
            $control .= '/>' . ResourceManager::TextFor($params['label']) . '</div></div>';
        } else {
            $control .= '/></div></div>';
        }

        return $control;
    }

    public function getTokenInput()
    {
        $securityService = new SecurityService();
        return $securityService->getToken();
    }

    public static function textArea($params = array())
    {
        $textArea = '<div class="form-group">';
        if (array_key_exists('label', $params)) {
            $textArea .= '<label class="control-label">'
                . ResourceManager::TextFor($params['label'])
                . '</label>';
        }

        $textArea .= '<div class="controls">';
        $textArea .= '<textarea ';
        if (array_key_exists('name', $params)) {
            $textArea .= 'name="' . $params['name'] . '"';
        }
        $textArea .= 'class="form-control">';

        if (array_key_exists('text', $params)) {
            $textArea .= $params['text'];
        }

        $textArea .= '</textarea>';

        $textArea .= '</div></div>';
        return $textArea;
    }

    public static function dropdownlist($params = array())
    {

        $dropdownlist = '<div class="form-group">';
        if (array_key_exists('label', $params)) {
            $dropdownlist .= '<label class="control-label">'
                . ResourceManager::TextFor($params['label'])
                . '</label>';
        }

        $dropdownlist .= '<div class="controls">';

        if (array_key_exists('name', $params)) {
            $dropdownlist .= '<select name="' . $params['name'] . '" class="form-control" ';
        }

        if (array_key_exists('id', $params)) {
            $dropdownlist .= 'id = "' . $params['id'] . '" ';
        }

        if (array_key_exists('readonly', $params)) {
            $dropdownlist .= 'readonly = "' . $params['readonly'] . '" ';
        }

        if (array_key_exists('required', $params)) {
            $dropdownlist .= 'required = "' . $params['required'] . '" ';
        }
        $dropdownlist .= ' >';

        $selectedValue = '';
        if (array_key_exists('selected', $params)) {
            $selectedValue = $params['selected'];
        }

        if (array_key_exists('options', $params)) {
            foreach ($params['options'] as $key => $value) {
                $selected = ($key == $selectedValue) ? 'SELECTED' : '';
                $dropdownlist .= '<option value ="' . $key . '" ' . $selected . '>' . $value . '</option>';
            }
        }
        $dropdownlist .= '</select></div></div>';
        return $dropdownlist;
    }

    public static function button($params = array())
    {
        $button = '<div class="form-group">';
        $button .= '<div class="controls">';

        $button .= '<button ';
        if (array_key_exists('type', $params)) {
            $button .= ' type="' . $params['type'] . '"';
        }
        if (array_key_exists('class', $params)) {
            $button .= ' class="' . $params['class'] . '"';
        }
        if (array_key_exists('name', $params)) {
            $button .= ' name="' . $params['name'] . '"';
        }
        if (array_key_exists('id', $params)) {
            $button .= ' id="' . $params['id'] . '"';
        }
        $button .= '>';
        $button .= ResourceManager::TextFor($params['label']);
        $button .= '</button></div></div>';

        return $button;
    }

    public static function ahrefLink($params = array())
    {

        $ahrefLink = '<a ';

        if (array_key_exists('link', $params)) {
            $ahrefLink .= 'href= "' . $params['link'] . '"';
        }
        if (array_key_exists('id', $params)) {
            $ahrefLink .= ' id = "' . $params['id'] . '"';
        }
        if (array_key_exists('class', $params)) {
            $ahrefLink .= ' class = "' . $params['class'] . '"';
        }
        $ahrefLink .= ' >' . ResourceManager::TextFor($params['label']) . '</a>';

        return $ahrefLink;
    }

    public static function table($params = array())
    {
        $table = '<table ';

        if (array_key_exists('class', $params)) {
            $table .= ' class="' . $params['class'] . '" ';
        }
        if (array_key_exists('id', $params)) {
            $table .= ' id="' . $params['id'] . '" ';
        }

        $table .= '>';

        //header
        if (array_key_exists('th', $params)) {
            $table .= ' <thead><tr>';
            foreach ($params['th'] as $key => $value) {
                $table .= '<th>' . $value . '</th>';
            }
            $table .= '<th>&nbsp;</th></tr></thead>';
        }

        //body
        $fkId = null;
        $pkId = null;

        $table .= ' <tbody> ';
        if (array_key_exists('tr', $params)) {
            foreach ($params['tr'] as $key => $value) {
                $table .= ' <tr>';

                if (array_key_exists('idx', $params)) {
                    foreach ($params['idx'] as $index) {
                        if (array_key_exists('pk', $params)) {
                            $pkId = $value[$params['pk']];
                        }
                        if (array_key_exists('fk', $params)) {
                            $fkId = $value[$params['fk']];
                        }

                        if ($index == 'pes_ind_cons_bonus' && array_key_exists('pes_ind_cons_bonus', $value)) {
                            $pes_ind_cons_bonus = $value[$index];
                        } else {
                            if ($index == 'placeHolder' && array_key_exists('placeHolder', $value)) {
                                $table .= '<td>' . self::fillPlaceHolder($value) . '</td>';
                            } elseif (array_key_exists($index, $value)) {
                                if ($index == 'pa_data') {
                                    $table .= '<td>' . date('d/m/Y', strtotime($value[$index])) . '</td>';
                                } elseif ($index == 'pa_status') {
                                    $table .= '<td>' . self::statusColumn($pkId, $value[$index]) . '</td>';
                                } elseif ($index == 'DATACOMPRA' || $index == 'DATACONSULTA') {
                                    $table .= '<td>' . date('d/m/Y', strtotime($value[$index])) . '</td>';
                                } elseif ($index == 'VALORPAGO' || $index == 'VALORPSIC' || $index == 'VALORCONEXPERIENCE') {
                                    $table .= '<td>' . number_format((float)$value[$index], 2) . '</td>';
                                } else {
                                    $table .= '<td>' . $value[$index] . '</td>';
                                }
                            }
                        }
                    }
                } else {
                    $isDate = date_parse($value);
                    if ($isDate['year']) {
                        $value = date('d/M/Y', strtotime($value));
                    }
                    $table .= '<td>' . explode('</td><td>', $value) . '</td>';
                }

                if (array_key_exists('action', $params)) {
                    $table .= '<td>';

                    if (!is_null($fkId)) {
                        $table .= ' <a href="' . sprintf($params['action']['hrefEdit'], $fkId, $pkId) . '"  title="Editar">' . $params['action']['linkEdit'] . '</a> ';
                    } else {
                        $table .= ' <a href="' . sprintf($params['action']['hrefEdit'], $pkId) . '" title="Editar">' . $params['action']['linkEdit'] . '</a> ';
                    }

                    $table .= ' <a href="' . sprintf($params['action']['hrefDelete'], $pkId) . '"  title="Excluir" class="confirmDelete">' . $params['action']['linkDelete'] . '</a> ';
                    $table .= self::isBonusSession($value);
                    if (isset($pes_ind_cons_bonus)) {
                        $table .= self::bonusConsultingColumn($pkId, $pes_ind_cons_bonus);
                    }

                    $table .= '</td>';
                }

                $table .= ' </tr>';
            }
        }
        //pagination

        $table .= '</tbody>';

        $table .= ' <tfoot> ';
        if (array_key_exists('total', $params)) {
            $table .= ' <tr>';

            if (array_key_exists('idx', $params)) {
                foreach ($params['idx'] as $k => $v) {
                    $table .= '<td><b>';

                    if (in_array($v, $params['total'])) {
                        $total = 0;

                        if (array_key_exists('tr', $params)) {
                            foreach ($params['tr'] as $tr) {
                                foreach ($tr as $key => $value) {
                                    if ($key == $v) {
                                        $total = $total + $value;
                                    }
                                }
                            }
                        }

                        $table .= $total;
                    }

                    $table .= '</b></td>';
                }
            }

            $table .= ' </tr>';
        }

        $table .= '</tfoot></table>';

        return $table;
    }

    public static function statusColumn($sessionId, $status)
    {
        $aStatus = self::statusArray();

        $form = '<form method="POST" action="' . BASE_URI . 'Session/updatestatus">'
            . '<input type="hidden" name="__authstamp" value=" ">'
            . '<input type="hidden" name="idpessoa_agenda" value="' . $sessionId . '">'
            . '<select name = "pa_status" class="form-control" style="width:auto;" onChange="form.submit();">'
            . self::statusOptions()
            . '</select>'
            . '</form>';

        $selected = array_search($status, $aStatus);
        $form = str_replace('value="' . $selected . '"', 'value="' . $selected . '" SELECTED', $form);
        return $form;
    }

    private static function statusArray()
    {
        $Html_Helper = new Html();
        return array(
            "1" => $Html_Helper->text(array('label' => 'SESSION_STATUS_RESERVED')),
            "2" => $Html_Helper->text(array('label' => 'SESSION_STATUS_CONFIRMED')),
            "3" => $Html_Helper->text(array('label' => 'SESSION_STATUS_PAYED')),
            "4" => $Html_Helper->text(array('label' => 'SESSION_STATUS_RESUMED')),
            "5" => $Html_Helper->text(array('label' => 'SESSION_STATUS_CUSTOMER_RESCHEDULED')),
            "6" => $Html_Helper->text(array('label' => 'SESSION_STATUS_PROFESSIONAL_RESCHEDULED')),
            "7" => $Html_Helper->text(array('label' => 'SESSION_STATUS_SECRETARY_RESCHEDULED')),
            "8" => $Html_Helper->text(array('label' => 'SESSION_STATUS_SYSADMIN_RESCHEDULED')),
            "9" => $Html_Helper->text(array('label' => 'SESSION_STATUS_CANCELED')),
            "10" => $Html_Helper->text(array('label' => 'SESSION_STATUS_FINISHED')),
        );
    }

    private static function statusOptions()
    {
        $Html_Helper = new Html();
        return '<option> </option> '
            . '<option value="1" >'
            . $Html_Helper->text(array('label' => 'SESSION_STATUS_RESERVED')) . '</option>'
            . '<option value="2" >'
            . $Html_Helper->text(array('label' => 'SESSION_STATUS_CONFIRMED')) . ' </option>'
            . '<option value="3" >'
            . $Html_Helper->text(array('label' => 'SESSION_STATUS_PAYED')) . '</option>'
            . '<option value="4" >'
            . $Html_Helper->text(array('label' => 'SESSION_STATUS_RESUMED')) . '</option>'
            . '<option value="5" >'
            . $Html_Helper->text(array('label' => 'SESSION_STATUS_CUSTOMER_RESCHEDULED')) . '</option>'
            . '<option value="6" >'
            . $Html_Helper->text(array('label' => 'SESSION_STATUS_PROFESSIONAL_RESCHEDULED')) . '</option>'
            . '<option value="7" >'
            . $Html_Helper->text(array('label' => 'SESSION_STATUS_SECRETARY_RESCHEDULED')) . '</option>'
            . '<option value="8" >'
            . $Html_Helper->text(array('label' => 'SESSION_STATUS_SYSADMIN_RESCHEDULED')) . '</option>'
            . '<option value="9" >'
            . $Html_Helper->text(array('label' => 'SESSION_STATUS_CANCELED')) . '</option>'
            . '<option value="10" >'
            . $Html_Helper->text(array('label' => 'SESSION_STATUS_FINISHED')) . '</option>';
    }

    public static function fillPlaceHolder($dataRow)
    {
        return "<small><strong><p>Psicóloga(o) : " . $dataRow['professionalname'] . "</p>"
            . "<p>Cliente : " . $dataRow['clientname'] . "</p>"
            . "<p>Paypal : " . $dataRow['rec_token_gateway'] . "</p>"
            . "<p>Qtd de consultas : " . $dataRow['qty'] . "</p>"
            . "<p>Data/Hora pagto : " . $dataRow['rec_data_pagamento'] . "</p></strong></small>";
    }

    public static function bonusConsultingColumn($clientId, $pes_ind_cons_bonus)
    {

        $table = ' <a href="' . BASE_URI . 'Client/bonusconsulting/' . $clientId . '" ';
        if ($pes_ind_cons_bonus == 1) {
            $table .= ' class="btn btn-xs btn-success" data-toggle="tooltip" data-placement="right" title="Consulta Interagir ativada" >';
            $table .= '<i class="glyphicon glyphicon-star"></i></a>';
        } else {
            $table .= ' class="btn btn-xs btn-danger" data-toggle="tooltip" data-placement="right" title="Consulta Interagir desativada">';
            $table .= '<i class="glyphicon glyphicon-star" ></i></a>';
        }
        return $table;
    }

    public static function isBonusSession($value)
    {
        $isbonus = '';
        if (array_key_exists('isbonus', $value) && $value['isbonus'] == 1) {
            $isbonus = '<i class="glyphicon glyphicon-star" title="Consulta Interagir"></i>';
        }
        return $isbonus;
    }

    public static function pagination($params = array())
    {
        if (array_key_exists('total', $params)) {
            $total = $params['total'];
        }

        if (array_key_exists('start', $params)) {
            $start = $params['start'];
        }

        if (array_key_exists('limit', $params)) {
            $limit = $params['limit'];
        }
        if (array_key_exists('page', $params)) {
            $pages = $params['page'];
        }

        if (array_key_exists('ahrefLink', $params)) {
            $link = $params['ahrefLink'];
        }

        if (array_key_exists('query', $params)) {
            $query = $params['query'];
        } else {
            $query = '';
        }

        $totalPages = ceil($total / $limit);
        $toPage = ($pages == 0 ? 1 : $pages) + $limit;


        if ($toPage > $totalPages) {
            $toPage = $totalPages;
        }


        $pagination = '<h4>' . ResourceManager::TextFor('PAGINATION_LABEL01') . ($pages + 1)
            . ResourceManager::TextFor('PAGINATION_LABEL02') . $totalPages
            . ResourceManager::TextFor('PAGINATION_LABEL03') . $total
            . ResourceManager::TextFor('PAGINATION_LABEL04') . '</h4>'
            . '<nav aria-label="Page navigation"><ul class="pagination">';
        $pagination .= '<li><a href="' . $link . '/0/' . $limit . '/0' . $query . '" aria-label="First">'
            . '<span aria-hidden="true">' . ResourceManager::TextFor($params['first'])
            . '</span></a></li>';
        $block = $limit * $pages;
        $pagination .= '<li><a href="' . $link . '/' . $block . '/' . $limit . '/' . ($pages <= 1 ? 0 : $pages - 1) . $query . '" aria-label="Previous">'
            . '<span aria-hidden="true"> < </span></a></li>';

        for ($ii = $pages; $ii < $toPage; $ii++) {
            $block = $limit * $ii;
            $pagination .= '<li><a href=' . $link . '/' . $block . '/' . $limit . '/' . $ii . $query . '>' . ($ii + 1) . '</a></li>';
        }

        $block = $limit * ($ii + 1);
        $pagination .= '<li><a href="' . $link . '/' . $block . '/' . $limit . '/' . ($pages <= 1 ? 0 : $pages + 1) . $query . '" aria-label="Next">'
            . '<span aria-hidden="true"> > </span></a></li>';

        $pagination .= '<li><a href="' . $link . '/' . ($total - 1) . '/' . $limit . '/' . ($totalPages - 1) . $query . '" ' .
            ' aria-label="Last" ><span aria-hidden="true">' .
            ResourceManager::TextFor($params['last']) . '</span></a></li>';

        $pagination .= '</ul></nav>';
        return $pagination;
    }

    public static function label($params = array())
    {
        $label = '';
        if (array_key_exists('label', $params)) {
            $label .= '<label class="form-label">' .
                ResourceManager::TextFor($params['label']) .
                '</label>';
        }
        return $label;
    }

    public static function text($params = array())
    {
        $label = '';
        if (array_key_exists('label', $params)) {
            $label = ResourceManager::TextFor($params['label']);
        }
        return $label;
    }

    public static function OpenForm($params = array())
    {
        $form = '';
        $form .= '<form ';

        if (array_key_exists('name', $params)) {
            $form .= 'name = "' . $params['name'] . '" ';
        }

        if (array_key_exists('role', $params)) {
            $form .= 'role = "' . $params['role'] . '" ';
        }

        if (array_key_exists('class', $params)) {
            $form .= 'class = "' . $params['class'] . '" ';
        }

        if (array_key_exists('id', $params)) {
            $form .= 'id = "' . $params['id'] . '" ';
        }
        if (array_key_exists('method', $params)) {
            $form .= 'method = "' . $params['method'] . '" ';
        }

        if (array_key_exists('action', $params)) {
            $form .= 'action = "' . $params['action'] . '" ';
        }

        $form .= '>';


        if (array_key_exists('SignOff', $params)) {
            $form .= self::control(array(
                'type' => 'hidden',
                'value' => $params['SignOff'],
                'name' => 'SignOff'
            ));
        }

        return $form;
    }

    public static function CloseForm()
    {
        return '</form>';
    }
}
