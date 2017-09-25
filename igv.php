<?php
include('_base.php');
global $db,$user;

$smarty->assign('can_igv', $user->can('igv'));

$smarty->assign('page_title', 'Configuración de IGV');

$smarty->display(PAGE.'.tpl');