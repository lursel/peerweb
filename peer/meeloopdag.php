<?php

require_once 'component.php';
require_once 'navigation2.php';

require_once 'SimpleTableFormatter.php';
require_once 'mailFunctions.php';

$pp = array( );
$page = new PageContainer();
$page->setTitle( "Plan meeloopdag" );
$nav = new Navigation( $tutor_navtable, basename( $PHP_SELF ), "Plan een meeloopdag" );
$page->addBodyComponent( $nav );


$pp['subject_de'] = 'Einladung zum Schnuppertag an der Fontys FH in Venlo';
$pp['subject_nl'] = 'Uitnodiging voor een meeloopdag bij Fontys Hogescholen in Venlo';

$pp['mailbody_de'] = file_get_contents( 'templates/meeloop_mailbody_de.html', true );
$pp['mailbody_nl'] = file_get_contents( 'templates/meeloop_mailbody_nl.html', true );

$sql = "select 0 as sorter,m.*,s.roepnaam||coalesce(' '||s.tussenvoegsel||' ',' ')||s.achternaam as mail_author from meeloopmail m join student s on (owner=snummer) where owner=$peer_id \n"
        . "union\n"
        . "select 1 as sorter,m.* ,s.roepnaam||coalesce(' '||s.tussenvoegsel||' ',' ')||s.achternaam as mail_author from meeloopmail m join student s on (owner=snummer) \n"
        . "order by sorter,meeloop_datum desc limit 1";
$resultSet = $dbConn->Execute( $sql );
if ( $resultSet !== false && !$resultSet->EOF ) {
  $pp = array_merge( $pp, $resultSet->fields );
}
$sqlsender = "select rtrim(email1) as sender,roepnaam||coalesce(' '||tussenvoegsel,'')||' '||achternaam as sender_name," .
        "coalesce(signature," .
        "'sent by the peerweb service on behalf of '||roepnaam||coalesce(' '||tussenvoegsel,'')||' '||achternaam)\n" .
        "  as signature from student left join email_signature using(snummer) where snummer='$peer_id'";
$rs = $dbConn->Execute( $sqlsender );
if ( !$rs->EOF ) {
  extract( $rs->fields );
} else {
  $replyto = 'Pieter.van.den.Hombergh@fontysvenlo.org';
  $sender_name = 'Pieter van den Hombergh';
  $signature = '';
}

if ( isSet( $_POST['mailbody_de'] ) ) {
  $pp['mailbody_de'] = preg_replace( '/"/', '\'', $_POST['mailbody_de'] );
}
if ( isSet( $_POST['subject_de'] ) ) {
  $pp['subject_de'] = $_POST['subject_de'];
}

if ( isSet( $_POST['mailbody_nl'] ) ) {
  $pp['mailbody_nl'] = preg_replace( '/"/', '\'', $_POST['mailbody_nl'] );
}
if ( isSet( $_POST['subject_nl'] ) ) {
  $pp['subject_nl'] = $_POST['subject_nl'];
}

if ( isSet( $_POST['meeloop_datum'] ) ) {
  $pp['meeloop_datum'] = $_POST['meeloop_datum'];
}
if ( isSet( $_POST['domail'] ) ) {
  $mailbody_nl = preg_replace( "/'/", "''", $pp['mailbody_nl'] );
  $mailbody_de = preg_replace( "/'/", "''", $pp['mailbody_de'] );
  $sql = "insert into meeloopmail (owner,meeloop_datum,subject_nl,subject_de,mailbody_nl,mailbody_de)\n" .
          "select {$peer_id},'{$pp['meeloop_datum']}','{$pp['subject_nl']}','{$pp['subject_de']}','{$mailbody_nl}','{$mailbody_de}'"
          . "where ($peer_id,'{$pp['meeloop_datum']}') not in (select owner,meeloop_datum from meeloopmail)";
  $dbConn->Execute( $sql );
  $result = $dbConn->Affected_Rows();
  if ( $result == 0 ) {
    $sql = "update meeloopmail set subject_nl='{$pp['subject_nl']}',\n"
            . "subject_de='{$pp['subject_de']}',\n"
            . "mailbody_nl='{$mailbody_nl}',\n"
            . "mailbody_de='{$mailbody_de}'\n"
            . "where owner={$peer_id} and meeloop_datum='{$pp['meeloop_datum']}'";
    $dbConn->Execute( $sql );
    $result = $dbConn->Affected_Rows();
  }
}


if ( isSet( $_POST['mail'] ) && isSet( $_POST['domail'] ) ) {

  $mail = $_POST['mail'];
  $mailset = '\'' . implode( "','", $mail ) . '\'';

  $sql_de = "select meelopen_id,email as email1, \n"
          . "roepnaam ||' '||coalesce(tussenvoegsel,'')||' '||achternaam as name\n"
          . "from meelopen\n"
          . "where taal ='DE' and meelopen_id in ($mailset)";
  $sql_nl = "select meelopen_id,email as email1, \n"
          . "roepnaam ||' '||coalesce(tussenvoegsel,'')||' '||achternaam as name\n"
          . "from meelopen\n"
          . "where taal ='NL' and meelopen_id in ($mailset)";
  //$dbConn->log( $sql );
  formMailer( $dbConn, $sql_de, $pp['subject_de'], $pp['mailbody_de'], $sender, $sender_name );
  formMailer( $dbConn, $sql_nl, $pp['subject_nl'], $pp['mailbody_nl'], $sender, $sender_name );
  // update invitation
  $sql = "begin work;\n"
          . "update meelopen set invitation=now()::date where meelopen_id in ($mailset);\n"
          . "update meeloopmail set invitation_datum = now()::date where owner=$peer_id and meeloop_datum='{$pp['meeloop_datum']}';\n"
          . "commit";
  $rs = $dbConn->Execute( $sql );
  if ( $rs === false ) {
    $dbConn->Execute( 'rollback' );
  }
}

$sql = "select '<input type=''checkbox'' name=''mail[]'' value='''||meelopen_id||'''/>' as chk,\n"
        . "achternaam,roepnaam,tussenvoegsel,plaats,land,postcode,email,sex,datum_in,invitation \n"
        . "from meelopen where participation isnull order by invitation desc,land,achternaam";

$pp['rtable'] = new SimpleTableFormatter( $dbConn, $sql, $page );
$pp['rtable']->setCheckColumn( 0 )
        ->setCheckName( 'mail[]' )
        ->setColorChangerColumn( 10 )
        ->setTabledef( "<table id='myTable' class='tablesorter' summary='meeloop studenten'"
                . " style='empty-cells:show;border-collapse:collapse' border='1'>" );
$page->addHtmlFragment( 'templates/meeloopdag.html', $pp );
$page->addHeadText( file_get_contents( 'templates/simpledatepicker.html' ) );
//$page->addScriptResource( 'js/jquery-1.7.1.min.js' );
//$page->addScriptResource( 'js/jquery-ui-1.8.17.custom.min.js' );
$page->addJqueryFragment( '$(\'#meeloop_datum\').datepicker(dpoptions);' );
$page->addHtmlFragment('templates/tinymce_include.html', $pp);
$page->show();
