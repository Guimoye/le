<?php
include('_base.php');
include('inc/data.php');
global $db, $arr_months,$arr_days;

$driver = $db->o('drivers', _GET_INT('id'));

if($driver == FALSE){
    $smarty->display('e404.tpl');
    exit;
}

$rental_started = false;

if($driver->rental_date){
    $rental_started = true;

    $time = strtotime($driver->rental_date);
    $dayMonth = date('d', $time);
    $dayWeek = date('w', $time);
    $month = date('n', $time);
    $year = date('Y', $time);

    $driver->rental_date_formated = $dayMonth.' de '.$arr_months[$month-1].' de '.$year;
    $driver->rental_date_day = $arr_days[$dayWeek];

    $driver->rental_age_weeks = $uu->datediffInWeeks($driver->rental_date,date('Y-m-d'));

}

// Alquiler
$rental = new stdClass();
$rental->total_due = 0;
$rental->total_paid = 0;
$rental->date_next_pay = null; // Fecha de siguiente pago, para detectar si ha vencido
$rental->weeks_late = 0; // Semanas de retraso
$rental->last_date_paid = ''; // Ultima fecha pagada

$os = $db->get("SELECT * FROM dues_rental WHERE id_driver = $driver->id ORDER BY date_due");
while($o = $os->fetch_object()){
    $rental->total_due += $o->amount_due;
    $rental->total_paid += $o->amount_paid;

    if(!$rental->date_next_pay && $o->amount_paid == 0){
        $rental->date_next_pay = $o->date_due;
    }

    if($o->amount_paid == 0 && strtotime($o->date_due) < time()){
        $rental->weeks_late += 1;
    }

    if($o->amount_paid > 0){
        $rental->last_date_paid = $o->date_paid;
    }
}
$rental->percent = @($rental->total_paid/$rental->total_due*100) ?: 0;

// Venta
$sale = new stdClass();
$sale->total_due = 0;
$sale->total_paid = 0;
$sale->date_next_pay = null; // Fecha de siguiente pago, para detectar si ha vencido
$sale->weeks_late = 0; // Semanas de retraso
$sale->last_date_paid = ''; // Ultima fecha pagada

$os = $db->get("SELECT * FROM dues_sale WHERE id_driver = $driver->id ORDER BY date_due");
while($o = $os->fetch_object()){
    $sale->total_due += $o->amount_due;

    if($o->amount_paid > 0){
        $sale->total_paid += $o->amount_due;
    }


    if(!$sale->date_next_pay && $o->amount_paid == 0){
        $sale->date_next_pay = $o->date_due;
    }

    if($o->amount_paid == 0 && strtotime($o->date_due) < time()){
        $sale->weeks_late += 1;
    }

    if($o->amount_paid > 0){
        $sale->last_date_paid = $o->date_paid;
    }
}
$sale->percent = @($sale->total_paid/$sale->total_due*100) ?: 0;

// Gastos
$expenses = new stdClass();
$expenses->total_items = 0;
$expenses->total_amount = 0;
$expenses->total_amount_paid = 0;
$expenses->date_next_pay = null; // Fecha de siguiente pago, para detectar si ha vencido
$expenses->weeks_late = 0; // Semanas de retraso
$expenses->last_amount = ''; // Ultimo registro
$expenses->last_date_pay = ''; // Ultima fecha pagada

$os = $db->get("SELECT * FROM expenses WHERE id_driver = $driver->id AND state = 1 ORDER BY date_pay");
while($o = $os->fetch_object()){
    $expenses->total_items += 1;
    $expenses->total_amount += $o->amount;

    if($o->date_paid){
        $expenses->total_amount_paid += $o->amount;
    }


    if(!$expenses->date_next_pay && $o->date_paid){
        $expenses->date_next_pay = $o->date_pay;
    }

    if(!$o->date_paid && strtotime($o->date_pay) < time()){
        $expenses->weeks_late += 1;
    }

    $expenses->last_amount = $o->amount;
    $expenses->last_date_pay = $o->date_pay;
}
$expenses->percent = @($expenses->total_paid/$expenses->total_due*100) ?: 0;

//echo ':'.$sale->weeks_late;
//exit;


$smarty->assign('page_title', 'Conductor');
$smarty->assign('driver', $driver);
$smarty->assign('rental', $rental);
$smarty->assign('sale', $sale);
$smarty->assign('expenses', $expenses);
$smarty->assign('rental_started', $rental_started);

$smarty->display(PAGE.'.tpl');