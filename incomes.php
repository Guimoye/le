<?php
include('_base.php');
global $db,$user;

$smarty->assign('page_title', 'Ingresos');

$smarty->assign('can_incomes', $user->can('incomes'));


$SQL = "SELECT re.*,
               DATE_FORMAT(re.date_added, '%d/%m/%Y %h:%i %p') date_added,
               bo.name bo_name
        FROM regboxes re
          INNER JOIN boxes bo ON bo.id = re.id_box
        WHERE re.id_user = $user->id AND re.state = 1";
$rb = $db->a($SQL);
if(!$rb){
    header('Location: open_box.php?r=incomes');exit;
}

$smarty->assign('rb', $rb);

$smarty->assign('incomes', $db->arr("SELECT * FROM transactions WHERE type = 2 AND state = 1"));

$smarty->display(PAGE.'.tpl');