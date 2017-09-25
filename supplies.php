<?php
include('_base.php');
global $db,$user;

$smarty->assign('page_title', 'Insumos');

$smarty->assign('can_supplies', $user->can('supplies'));

$SQL = "SELECT su.*,
               un.name un_name,
               COALESCE(SUM(sk.stock),0) stock
        FROM supplies su
          LEFT JOIN unimeds un ON un.id = su.id_unimed
          LEFT JOIN stocks sk ON sk.id_supply = su.id
        WHERE su.id_branch = $user->id_branch AND su.state = 1
        GROUP BY su.id
        ORDER BY su.name";
//print_r($db->arr($SQL));exit;
$smarty->assign('supplies', $db->arr($SQL));

$smarty->display(PAGE.'.tpl');