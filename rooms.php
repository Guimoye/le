<?php
include('_base.php');
global $db,$user;

$smarty->assign('page_title', 'Salas');

$smarty->assign('can_rooms', $user->can('rooms'));

$rooms = [];
$os = $db->get("SELECT * FROM rooms WHERE id_branch = $user->id_branch AND state = 1");
while($o = $os->fetch_assoc()){

    $o['tables'] = $db->arr("SELECT * FROM tables WHERE id_room = ".$o['id']." AND state = 1");

    $rooms[] = $o;
}

//print_r($rooms);exit;

$smarty->assign('rooms', $rooms);

$smarty->display(PAGE.'.tpl');