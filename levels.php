<?php
include('_base.php');
global $db, $user;

$smarty->assign('page_title', 'Perfiles de usuario');

$smarty->assign('menu_all', $user->getMenuAll());

$levels = [];
$os = $db->get("SELECT * FROM levels");
while($o = $os->fetch_object()){
    $o->perms = $db->arr("SELECT * FROM perms WHERE id_level = $o->id");
    $levels[$o->id] = $o;
}

$smarty->assign('levels', $levels);

$smarty->display(PAGE.'.tpl');