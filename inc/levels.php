<?php
include('../ajax/_base.php');
global $db,$user;

$menu = $db->getMenuUser($user->id);


/*$os = $db->get("SELECT * FROM menu ORDER BY sort");
while($o = $os->fetch_object()){
    if($o->id_parent == 0){
        $menu[$o->id] = $o;
    } else {
        $menu[$o->id_parent]->submenu[] = $o;
    }
}*/
//print_r($menu);exit;

echo '<ul>';
foreach($menu as $a => $b){
    echo '<li>'.(empty($b->url) ? $b->name : '<a href="'.$b->url.'">'.$b->name.'</a>');
    if(isset($b->submenu) && is_array($b->submenu)){
        echo '<ul>';
        foreach($b->submenu as $c => $d){
            echo '<li><a href="'.$d->url.'">'.$d->name.'</a></li>';
        }
        echo '</ul>';
    }
    echo '</li>';
}
echo '</ul>';

/*$menu = [
    'Dashboard'=>'am.php',
    'Clientes'=>'am.php',
    'Configuraciones'=>[
        'Ajustes generales'=>'am.php',
        'Usuarios de sistema'=>'am.php'
    ]
];

foreach($menu as $a => $b){
    echo '• '.$a;
    if(is_array($b)){
        foreach($b as $c => $d){
            echo '<br>&nbsp;&nbsp;• ';
            echo $c;
        }
    }
    echo '<hr>';
}*/