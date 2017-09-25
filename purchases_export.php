<?php
include('_base.php');
global $db,$user;

$smarty->assign('page_title', 'Exportar compras');
$smarty->assign('sidebar_closed', true);

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

if(is_numeric($fs->id_provider) && $fs->id_provider > 0){
    $WHERE .= " AND pu.id_provider = $fs->id_provider";
}

$SQL = "SELECT pu.*,
               st.name st_name,
               pr.name pr_name,
               pr.ruc pr_ruc,
               pf.name pf_name,
               pf.code pf_code
        FROM purchases pu
          LEFT JOIN storages st ON st.id = pu.id_storage
          LEFT JOIN providers pr ON pr.id = pu.id_provider
          LEFT JOIN proofs pf ON pf.id = pu.id_proof
        WHERE $WHERE
        ORDER BY id DESC";

$purchases = [];
$os = $db->get($SQL);
while($o = $os->fetch_assoc()){

    $adq_0_base = 0; // Gravada
    $adq_0_igv = 0;
    $adq_1_base = 0; // Gravada exportacion
    $adq_1_igv = 0;
    $adq_2_base = 0; // No gravada
    $adq_2_igv = 0;

    $SQL = "SELECT ps.*,
                   su.tipo_adq
            FROM purchases_sup ps
              INNER JOIN supplies su ON su.id = ps.id_supply
            WHERE id_purchase = ".$o['id'];
    $ops = $db->get($SQL);
    while($op = $ops->fetch_object()){
        $igv = ($op->total*$stg->igv)/100;
        $base = $op->total-$igv;
        switch($op->tipo_adq){
            case 0:
                $adq_0_base += $base;
                $adq_0_igv += $igv;
                break;
            case 1:
                $adq_1_base += $base;
                $adq_1_igv += $igv;
                break;
            case 2:
                $adq_2_base += $base;
                $adq_2_igv += $igv;
                break;
        }
    }

    $o['adq_0_base'] = $adq_0_base;
    $o['adq_0_igv'] = $adq_0_igv;
    $o['adq_1_base'] = $adq_1_base;
    $o['adq_1_igv'] = $adq_1_igv;
    $o['adq_2_base'] = $adq_2_base;
    $o['adq_2_igv'] = $adq_2_igv;

    $purchases[] = $o;
}

$smarty->assign('fs', $fs);
$smarty->assign('purchases', $purchases);
$smarty->assign('storages', $db->arr("SELECT * FROM storages WHERE id_branch = $user->id_branch AND state = 1 ORDER BY name"));
$smarty->assign('providers', $db->arr("SELECT * FROM providers WHERE state = 1 ORDER BY name"));
$smarty->assign('proofs', $db->arr("SELECT * FROM proofs WHERE state = 1 ORDER BY name"));

$smarty->display(PAGE.'.tpl');