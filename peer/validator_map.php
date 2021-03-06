<?php

/**
 * The simple table editor for the table menu. Menu is one of the tables that support
 * the simple table editor.
 *
 * @package peerweb
 * @author Pieter van den Hombergh
 * $Id: validator_map.php 1570 2013-08-09 19:51:30Z hom $
 */
include_once("ste.php");

$page = new PageContainer("Validator Regex Editor on DB " . $db_name);
$ste = new SimpleTableEditor($dbConn, $page);
$ste->setFormAction($PHP_SELF)
        ->setRelation('validator_map')
        ->setMenuName('validator_map')
        ->setKeyColumns(array('input_name'))
        ->setNameExpression("rtrim(input_name,' ')||', '||rtrim(regex_name,' ')")
        ->setOrderList(array('input_name'))
        ->setFormTemplate('templates/validator_map.html')
        ->show()
?>





