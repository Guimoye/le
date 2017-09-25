<?php
include('_base.php');
global $db,$user;

$smarty->assign('page_title', 'Comprobantes');

$smarty->assign('can_proofs', $user->can('proofs'));

$smarty->assign('proofs', $db->arr("SELECT * FROM proofs WHERE state = 1 ORDER BY name"));

$smarty->display(PAGE.'.tpl');