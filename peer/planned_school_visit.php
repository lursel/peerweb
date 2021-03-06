<?php

/**
 * The simple table editor for the tutor
 * @author Pieter van den Hombergh
 * $Id: planned_school_visit.php 1723 2014-01-03 08:34:59Z hom $
 */
include_once("peerutils.php");
include_once('navigation2.php');
include_once("utils.php");
include_once("ste.php");
requireCap(CAP_RECRUITER);

$page = new PageContainer("Peerweb planned_school_visit" . $PHP_SELF . " on DB " . $db_name);
$ste = new SimpleTableEditor($dbConn, $page);
$ste->setTransactional(true)
        ->setFormAction($PHP_SELF)
        ->setRelation('planned_school_visit')
        ->setMenuName('planned_school_visit')
        ->setKeyColumns(array('planned_school_visit'))
        ->setSubRel('transaction_operator')
        ->setSubRelJoinColumns(array('trans_id' => 'trans_id'))
        ->setNameExpression("rtrim(visit_short)")
        ->setListRowTemplate(array('visit_short', 'visit_date'))
        ->setOrderList(array('visit_date desc', 'visit_short'))
        ->setFormTemplate('templates/planned_school_visit.html')
        ->show();
?>
