<?php
include('_base.php');
global $db,$user;

$smarty->assign('page_title', 'Anular compras');
//$smarty->assign('sidebar_closed', true);

$smarty->assign('can_purchases', $user->can('purchases'));

$fs = new stdClass();
$fs->date_from      = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$fs->date_to        = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$fs->id_provider    = isset($_GET['id_provider']) ? $_GET['id_provider'] : '';

$WHERE = "pu.id_branch = $user->id_branch AND pu.state = 1";

if(empty($fs->date_from)){
    $fs->date_from = (new DateTime('first day of this month'))->format('Y-m-d');
}
if(empty($fs->date_to)){
    $fs->date_to = (new DateTime('last day of this month'))->format('Y-m-d');
}

$WHERE .= " AND DATE(pu.date_added) BETWEEN '$fs->date_from' AND '$fs->date_to'";

$SQL = "SELECT pu.*,
               st.name st_name,
               pr.name pr_name,
               pf.name pf_name
        FROM purchases pu
          LEFT JOIN storages st ON st.id = pu.id_storage
          LEFT JOIN providers pr ON pr.id = pu.id_provider
          LEFT JOIN proofs pf ON pf.id = pu.id_proof
        WHERE $WHERE
        ORDER BY id DESC";

$smarty->assign('fs', $fs);
$smarty->assign('purchases', $db->arr($SQL));

$smarty->display(PAGE.'.tpl');