<?php
include('_base.php');
global $db,$user;

$smarty->assign('page_title', 'Ventas');
//$smarty->assign('sidebar_closed', true);

$smarty->assign('can_purchases', $user->can('purchases'));

$fs = new stdClass();
$fs->date_from      = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$fs->date_to        = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$fs->id_provider    = isset($_GET['id_provider']) ? $_GET['id_provider'] : '';

$WHERE = "tr.id_branch = $user->id_branch AND tr.state = 1";

if(empty($fs->date_from)){
    $fs->date_from = (new DateTime('first day of this month'))->format('Y-m-d');
}
if(empty($fs->date_to)){
    $fs->date_to = (new DateTime('last day of this month'))->format('Y-m-d');
}

$WHERE .= " AND DATE(tr.date_added) BETWEEN '$fs->date_from' AND '$fs->date_to'";


$SQL = "SELECT tr.*,
               cl.name cl_name,
               pf.name pf_name
        FROM transactions tr
          LEFT JOIN clients cl ON cl.id = tr.id_client
          LEFT JOIN proofs pf ON pf.id = tr.id_proof
        WHERE $WHERE
        ORDER BY id DESC";

$smarty->assign('fs', $fs);
$smarty->assign('purchases', $db->arr($SQL));
$smarty->assign('storages', $db->arr("SELECT * FROM storages WHERE id_branch = $user->id_branch AND state = 1 ORDER BY name"));
$smarty->assign('providers', $db->arr("SELECT * FROM providers WHERE state = 1 ORDER BY name"));
$smarty->assign('proofs', $db->arr("SELECT * FROM proofs WHERE state = 1 ORDER BY name"));

$smarty->display(PAGE.'.tpl');