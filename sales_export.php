<?php
include('_base.php');
global $db,$user;

$smarty->assign('page_title', 'Exportar ventas');
$smarty->assign('sidebar_closed', true);

$smarty->assign('can_purchases', $user->can('purchases'));

$fs = new stdClass();
$fs->date_from      = isset($_GET['date_from']) ? $_GET['date_from'] : '';
$fs->date_to        = isset($_GET['date_to']) ? $_GET['date_to'] : '';
$fs->id_provider    = isset($_GET['id_provider']) ? $_GET['id_provider'] : '';

$WHERE = "tr.id_branch = $user->id_branch AND tr.state = 1 AND tr.type = 1";

if(empty($fs->date_from)){
    $fs->date_from = (new DateTime('first day of this month'))->format('Y-m-d');
}
if(empty($fs->date_to)){
    $fs->date_to = (new DateTime('last day of this month'))->format('Y-m-d');
}

$WHERE .= " AND DATE(tr.date_added) BETWEEN '$fs->date_from' AND '$fs->date_to'";

$SQL = "SELECT tr.*,
               bo.serie,
               pf.code pf_code,
               pf.name pf_name,
               cl.name cl_name,
               cl.dni cl_dni
        FROM transactions tr
          LEFT JOIN regboxes rb ON rb.id = tr.id_regbox
            LEFT JOIN boxes bo ON bo.id = rb.id_box
          LEFT JOIN proofs pf ON pf.id = tr.id_proof
          LEFT JOIN clients cl ON cl.id = tr.id_client
        WHERE $WHERE
        ORDER BY id DESC";

$purchases = [];
$os = $db->get($SQL);
while($o = $os->fetch_assoc()){

    $adq_igv = ($o['total']*$stg->igv)/100;
    $adq_base = $o['total']-$adq_igv; // Gravada

    $glosaArr = [];

    // Obtener productos que incluyen esta venta
    $SQL = "SELECT op.*,
                   ar.name ar_name
            FROM ordpros op
              INNER JOIN propres pp ON pp.id = op.id_propre
                INNER JOIN products pr ON pr.id = pp.id_product
                  INNER JOIN areas ar ON ar.id = pr.id_area
            WHERE op.id_transaction = ".$o['id'];
    $ops = $db->get($SQL);
    while($op = $ops->fetch_object()){
        if(!in_array($op->ar_name, $glosaArr)){
            $glosaArr[] = $op->ar_name;
        }
    }

    $o['adq_base'] = $adq_base;
    $o['adq_igv'] = $adq_igv;
    $o['glosa'] = implode(' - ', $glosaArr);

    $purchases[] = $o;
}

$smarty->assign('fs', $fs);
$smarty->assign('purchases', $purchases);
$smarty->assign('storages', $db->arr("SELECT * FROM storages WHERE id_branch = $user->id_branch AND state = 1 ORDER BY name"));
$smarty->assign('providers', $db->arr("SELECT * FROM providers WHERE state = 1 ORDER BY name"));
$smarty->assign('proofs', $db->arr("SELECT * FROM proofs WHERE state = 1 ORDER BY name"));

$smarty->display(PAGE.'.tpl');