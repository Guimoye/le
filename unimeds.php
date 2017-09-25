<?php
include('_base.php');
global $db,$user;

$smarty->assign('page_title', 'Unidades de medida');

$smarty->assign('can_unimeds', $user->can('unimeds'));

$SQL = "SELECT ur.*,
               uno.name uno_name,
               und.name und_name
        FROM unimeds_rel ur
          LEFT JOIN unimeds uno ON uno.id = ur.id_unimed_org
          LEFT JOIN unimeds und ON und.id = ur.id_unimed_dst
        WHERE ur.state = 1
        ORDER BY uno_name";

$smarty->assign('unimeds', $db->arr("SELECT * FROM unimeds WHERE state = 1 AND id_parent = 0 ORDER BY name"));
$smarty->assign('unimeds_rel', $db->arr($SQL));

$smarty->display(PAGE.'.tpl');