<?php
include('_base.php');
global $uu,$db,$user;

$smarty->assign('page_title', 'Generar pedidos');

$smarty->assign('can_request', $user->can('request'));

$smarty->assign('sidebar_closed', true);


$SQL = "SELECT re.*,
               DATE_FORMAT(re.date_added, '%d/%m/%Y %h:%i %p') date_added,
               bo.name bo_name
        FROM regboxes re
          INNER JOIN boxes bo ON bo.id = re.id_box
        WHERE re.id_user = $user->id AND re.state = 1";
$rb = $db->a($SQL);
if(!$rb){
    header('Location: open_box.php?r=box');exit;
}

$smarty->assign('rb', $rb);


$rooms = [];
$os = $db->get("SELECT * FROM rooms WHERE id_branch = $user->id_branch AND state = 1");
while($o = $os->fetch_assoc()){
    $o['tables'] = $db->arr("SELECT * FROM tables WHERE id_room = ".$o['id']." AND state = 1");
    $rooms[] = $o;
}

$products = [];
$SQL = "SELECT pr.*,
               ca.name ca_name
        FROM products pr
          LEFT JOIN categories ca ON ca.id = pr.id_category
        WHERE pr.id_branch = $user->id_branch AND pr.state = 1";
$os = $db->get($SQL);
while($o = $os->fetch_object()){
    $o->propres = $db->arr("SELECT * FROM propres WHERE id_product = $o->id AND state = 1");
    $products[] = $o;
}

//print_r($products);
//exit;

$smarty->assign('proofs', $db->arr("SELECT * FROM proofs WHERE state = 1 ORDER BY name"));
$smarty->assign('rooms', $rooms);
$smarty->assign('categories_fav', $db->arr("SELECT * FROM categories WHERE favorite = 1 AND state = 1 ORDER BY sort"));
$smarty->assign('categories', $uu->ordMenu($db->arr("SELECT * FROM categories WHERE state = 1 ORDER BY sort")));
$smarty->assign('products', $products);

$smarty->display(PAGE.'.tpl');