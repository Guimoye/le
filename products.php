<?php
include('_base.php');
global $db,$user;

$fils = [];
$fils['word'] = isset($_GET['word']) ? $_GET['word'] : '';
$fils['id_area'] = isset($_GET['id_area']) ? $_GET['id_area'] : '';
$fils['max'] = isset($_GET['max']) && is_numeric($_GET['max']) ? $_GET['max'] : 50;

$smarty->assign('page_title', 'Platos y bebidas');

$smarty->assign('can_products', $user->can('products'));

$smarty->assign('fils', $fils);

$smarty->assign('areas', $db->arr("SELECT * FROM areas WHERE id_branch = $user->id_branch AND state = 1"));
$smarty->assign('unimeds', $db->arr("SELECT * FROM unimeds WHERE state = 1"));
$smarty->assign('storages', $db->arr("SELECT *, 0 stock FROM storages WHERE id_branch = $user->id_branch AND state = 1 ORDER BY name"));

$WHERE = "pr.id_branch = $user->id_branch AND pr.state = 1";
$LIMIT = "";

if(!empty($fils['word'])){
    $word = '%'.str_replace(' ','%',$fils['word']).'%';
    $WHERE .= " AND pr.name LIKE '$word'";
}
if(is_numeric($fils['id_area']) && $fils['id_area'] > 0){
    $WHERE .= " AND pr.id_area = ".$fils['id_area'];
}
if(is_numeric($fils['max']) && $fils['max'] > 0){
    $LIMIT = $fils['max'];
}

$SQL = "SELECT pr.*,
               ar.name ar_name,
               ca.name ca_name,
               un.name un_name
        FROM products pr
          LEFT JOIN areas ar ON ar.id = pr.id_area
          LEFT JOIN categories ca ON ca.id = pr.id_category
          LEFT JOIN unimeds un ON un.id = pr.id_unimed
        WHERE $WHERE
        ORDER BY pr.name
        LIMIT $LIMIT";
//print_r($db->arr($SQL));exit;

$products = [];

$os = $db->get($SQL);
while($o = $os->fetch_assoc()){

    $propres = [];

    $products[] = $o;
}

$smarty->assign('products', $db->arr($SQL));

$smarty->display(PAGE.'.tpl');