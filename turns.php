<?php
include('_base.php');
global $db,$user;

$smarty->assign('page_title', 'Turnos');

$smarty->assign('can_turns', $user->can('turns'));

$smarty->assign('turns', $db->arr("SELECT * FROM turns WHERE state = 1"));

$smarty->display(PAGE.'.tpl');