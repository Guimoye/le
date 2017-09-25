<?php
include('_base.php');
global $uu,$db,$user;

$smarty->assign('page_title', 'Generar pedidos');

$smarty->assign('can_request', $user->can('request'));

$smarty->assign('sidebar_closed', false);

$rooms = [];
$os = $db->get("SELECT * FROM rooms WHERE id_branch = $user->id_branch AND state = 1");
if($os){
    while($o = $os->fetch_assoc()){
        $o['tables'] = $db->arr("SELECT * FROM tables WHERE id_room = ".$o['id']." AND state = 1");
        $rooms[] = $o;
    }
}

$products = [];
$SQL = "SELECT pr.*,
               ca.name ca_name
        FROM products pr
          LEFT JOIN categories ca ON ca.id = pr.id_category
        WHERE pr.id_branch = $user->id_branch AND pr.state = 1";
$os = $db->get($SQL);
if($os){
    while($o = $os->fetch_object()){
        $o->propres = $db->arr("SELECT * FROM propres WHERE id_product = $o->id AND state = 1");
        $products[] = $o;
    }
}

//print_r($products);
//exit;

$smarty->assign('rooms', $rooms);
$smarty->assign('categories_fav', $db->arr("SELECT * FROM categories WHERE favorite = 1 AND state = 1 ORDER BY sort"));
$smarty->assign('categories', $uu->ordMenu($db->arr("SELECT * FROM categories WHERE state = 1 ORDER BY sort")));
$smarty->assign('products', $products);

$smarty->display(PAGE.'.tpl');