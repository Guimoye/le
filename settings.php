<?php
include('_base.php');
global $db,$user;

$smarty->assign('page_title', 'Opciones de sistema');

$smarty->assign('can_settings', $user->can('settings'));

$smarty->display(PAGE.'.tpl');