<?php class dues_rental extends _base
{

    public function __construct()
    {
        parent::__construct();
        // El contructor

        $this->setModule('drivers');
    }

    private function index()
    {
    }

    public function item($id_driver)
    {

        /*$time_pay = strtotime('2017-11-21');
        $time_curr = strtotime(date('Y-m-d'));
        echo 'time_pay:'.$time_pay;
        echo '<br>time_curr:'.$time_curr;
        exit;*/

        $ui = $this->ui();

        $driver = $this->db->o('drivers', $id_driver);

        if ($driver == FALSE) {
            $ui->display('e404.tpl');
            exit;
        }

        $items = [];


        $tts = [];
        $tts['total_amount_due'] = 0;
        $tts['total_amount_pit'] = 0;
        $tts['total_amount_cabify'] = 0;
        $tts['total_amount_penalty'] = 0;
        $tts['total_amount_discount'] = 0;
        $tts['total_amount_additionals'] = 0;
        $tts['total_amount_loans'] = 0;
        $tts['total_amount_previous'] = 0;
        $tts['total_amount_total'] = 0;
        $tts['total_amount_paid'] = 0;

        $today_date = date('Y-m-d');
        $today_time = strtotime($today_date);

        $SQL = "SELECT dr.*,
                       COALESCE(SUM(dl.amount_due),0) amount_loans
                FROM dues_rental dr
                  LEFT JOIN loans lo ON lo.id_driver = dr.id_driver AND lo.state != 0
                    LEFT JOIN dues_loans dl ON dl.id_loan = lo.id AND dl.date_due = dr.date_due AND dl.state != 0
                WHERE dr.id_driver = $driver->id AND dr.state != 0
                GROUP BY dr.id";

        $os = $this->db->get($SQL);
        if ($os) {
            while ($o = $os->fetch_assoc()) {

                $o['amount_total'] = ($o['amount_due'] +
                        $o['amount_pit'] +
                        $o['amount_penalty'] +
                        $o['amount_additionals'] +
                        $o['amount_previous']
                        + $o['amount_loans']) - $o['amount_cabify'] - $o['amount_discount'];

                // total pagado
                $o['total_paid'] = ($o['amount_paid'] + $o['amount_cabify']);

                $tts['total_amount_due'] += $o['amount_due'];
                $tts['total_amount_pit'] += $o['amount_pit'];
                $tts['total_amount_cabify'] += $o['amount_cabify'];
                $tts['total_amount_penalty'] += $o['amount_penalty'];
                $tts['total_amount_discount'] += $o['amount_discount'];
                $tts['total_amount_additionals'] += $o['amount_additionals'];
                $tts['total_amount_loans'] += $o['amount_loans'];
                $tts['total_amount_previous'] += $o['amount_previous'];
                $tts['total_amount_total'] += $o['amount_total'];
                $tts['total_amount_paid'] += $o['amount_paid'];

                if ($o['free_days'] == '') {
                    $o['worked_days_text'] = '(7/7)';
                } else {
                    $arr = explode(',', $o['free_days']);
                    $o['worked_days_text'] = '(' . (7 - count($arr)) . '/7)';
                }

                $due_time = strtotime($o['date_due']);

                $expired_days = 0;

                if ($o['state'] == 2 || $o['state'] == 3) {
                    $o['pay_state'] = 'paid';

                    $o['all_paid'] = ($o['state'] == 3);

                } else if ($due_time < $today_time) {
                    $o['pay_state'] = 'expired';

                    $expired_days = floor(($today_time - $due_time) / (60 * 60 * 24));

                    $o['amount_penalty'] += ($expired_days * 5);

                    $o['amount_total'] += $o['amount_penalty'];

                } else {
                    $o['pay_state'] = 'pending';

                }

                $o['expired_days'] = $expired_days;

                $items[] = $o;
            }
        }

        $ui->assign('page_title', 'Cronograma de alquiler');
        $ui->assign('driver', $driver);
        $ui->assign('items', $items);
        $ui->assign('tts', $tts);

        $ui->display('dues_rental.tpl');
    }

    public function add()
    {
        $this->checkEditPerms();

        $id_driver = _POST_INT('id_driver');
        $dues = _POST_INT('dues');
        $amount = _POST_INT('amount');
        $amount_pit = _POST_INT('amount_pit');
        $date = _POST('date');

        if ($id_driver <= 0) {
            $this->rsp['msg'] = 'No se reconoce el conductor';

        } else if ($this->db->has("SELECT * FROM dues_rental WHERE id_driver = $id_driver AND state != 0")) {
            $this->rsp['msg'] = 'El programa de alquiler para este conductor se generó previamente.';

        } else if ($dues <= 0) {
            $this->rsp['msg'] = 'Indica el número de cuotas';

        } else if ($amount <= 0) {
            $this->rsp['msg'] = 'Indica el monto de cuota';

        } else if (!$this->uu->isDate($date)) {
            $this->rsp['msg'] = 'Indica la fecha de inicio';

        } else {

            $lastTime = strtotime($date);
            for ($i = 1; $i <= $dues; $i++) {

                $date_due = date("Y-m-d", $lastTime);
                $day_num = date("w", $lastTime);
                $day_nam = date("l", $lastTime);

                $this->rsp['dayy'] = date("w", $lastTime);

                $this->rsp['dues'][] = [
                    'due' => $i,
                    'date_due' => $date_due,
                    'day_num' => $day_num,
                    'day_nam' => $day_nam
                ];

                $this->db->insert('dues_rental', [
                    'num_due' => $i,
                    'id_driver' => $id_driver,
                    'amount_due' => $amount,
                    'amount_pit' => $amount_pit,
                    'date_due' => $date_due,
                    'state' => 1
                ]);

                $lastTime = strtotime('next ' . $day_nam, $lastTime);
            }

            if ($this->db->update('drivers', ['rental_dues' => $dues, 'rental_amount' => $amount, 'rental_date' => $date], $id_driver)) {
                $this->rsp['ok'] = true;

            } else {
                $this->rsp['msg'] = 'Error interno :: DB';
            }

        }

        $this->rsp();
    }

    public function remove_all()
    {
        $id_driver = _POST_INT('id_driver');

        if ($id_driver <= 0) {
            $this->rsp['msg'] = 'No se reconoce el conductor';

        } else {
            if ($this->db->query("UPDATE dues_rental SET state = 0 WHERE id_driver = $id_driver")) {
                $this->rsp['ok'] = true;

                $this->db->update('drivers', [
                    'rental_date' => 'NULL',
                    'rental_amount' => 0,
                    'rental_dues' => 0
                ], $id_driver);

            } else {
                $this->rsp['msg'] = 'Error interno :: DB';
            }
        }

        $this->rsp();
    }

    public function set_due_paid()
    {
        $this->checkEditPerms();

        $id = _POST_INT('id');

        $amount_total = _POST_INT('amount_total');
        $amount = _POST_INT('amount_paid');
        $amount_cabify = _POST_INT('amount_cabify');
        $amount_penalty = _POST_INT('amount_penalty');
        $amount_discount = _POST_INT('amount_discount');
        $amount_additionals = _POST_INT('amount_additionals');
        $comment_additionals = _POST('comment_additionals');
        $date_paid = _POST('date_paid');
        $voucher_code = _POST('voucher_code');


        $items = _POST_ARR('items');

        $json_aux = json_encode($items);

        // Obtener actual cuota a pagar
        $due = $this->db->o("
            SELECT dr.*,
                   COALESCE(SUM(dl.amount_due),0) amount_loans
            FROM dues_rental dr
              LEFT JOIN loans lo ON lo.id_driver = dr.id_driver AND lo.state != 0
                LEFT JOIN dues_loans dl ON dl.id_loan = lo.id AND dl.date_due = dr.date_due AND dl.state != 0
            WHERE dr.id = $id
            GROUP BY dr.id
            LIMIT 1
        ");

        if (!$due) {
            $this->rsp['msg'] = 'No se reconoce el registro';

        } else if ($amount < 0) {
            $this->rsp['msg'] = 'Debe ingresar un monto válido';

        } else if (!$this->uu->isDate($date_paid)) {
            $this->rsp['msg'] = 'Debe ingresar la fecha de pago';

        } else {

            $amount_total = (
                    $amount_penalty
                    + $amount_additionals
                    + $due->amount_due
                    + $due->amount_pit
                    + $due->amount_loans
                    + $due->amount_previous
                    + $due->amount_additionals
                ) - $amount_cabify - $amount_discount;

            $data = [];
            $data['amount_paid'] = $amount;
            $data['amount_cabify'] = $amount_cabify;
            $data['amount_penalty'] = $amount_penalty;
            $data['amount_discount'] = $amount_discount;
            $data['amount_additionals'] = $amount_additionals;
            $data['comment_additionals'] = $comment_additionals;
            $data['date_paid'] = $date_paid;
            $data['voucher_code'] = $voucher_code;
            $data['state'] = 3; // Pago total
            $data['sub_paids'] = $json_aux; //subpagos


            // El pago parcial es cuando el pago de alquiler es menor, solo eso
            if (($amount) < ($due->amount_due - ($amount_cabify - $amount_discount))) {
                $data['state'] = 2; // Pago parcial
            }

            // La siguiente cuota
            $next_amount = 0; // Actualizamos el monto anterior de la siguiente cuota, para garantizar que cuando se actualice
            if ($amount < $amount_total) {
                $next_amount = $amount_total - $amount; // A pagar el proximo mes
            }
            // Obtener la siguiente cuota
            $nextO = $this->db->o("SELECT * FROM dues_rental WHERE id_driver = $due->id_driver AND num_due > $due->num_due AND state != 0 LIMIT 1");
            if ($nextO) {
                $this->db->update('dues_rental', ['amount_previous' => $next_amount], $nextO->id);
                //exit('$nextO: '.$nextO->id);
            }

            if ($this->db->update('dues_rental', $data, $id)) {

                // Se pagan todos los prestamos de esta fecha, si no es pago total, se agrega como anterior
                $SQL = "SELECT dl.*
                        FROM dues_loans dl
                          LEFT JOIN loans lo ON lo.id = dl.id_loan
                        WHERE lo.id_driver = $due->id_driver AND dl.date_due = '2017-11-22' AND dl.state != 0";
                $os = $this->db->get($SQL);
                while ($o = $os->fetch_object()) {
                    $this->db->update('dues_loans', ['amount_paid' => $o->amount_due, 'date_paid' => 'NOW()', 'state' => 3], $o->id);
                }


                $this->rsp['ok'] = true;
            } else {
                $this->rsp['msg'] = 'Error interno :: DB';
            }

        }

        $this->rsp();
    }

    public function set_due_unpaid()
    {
        $this->checkEditPerms();

        $id = _POST_INT('id');

        $data = [];
        $data['amount_paid'] = 0;
        $data['date_paid'] = '';
        if ($this->db->query("UPDATE dues_rental SET amount_paid = 0, date_paid = NULL WHERE id = $id")) {
            $this->rsp['ok'] = true;
        } else {
            $this->rsp['msg'] = 'Error interno :: DB';
        }

        $this->rsp();
    }

    public function set_free_days()
    {
        $this->checkEditPerms();

        /*$_POST['id']    = 1;
        $_POST['days']  = ['0','1','2','3','4','5'];*/

        $id = _POST_INT('id');
        $notes = _POST('notes');
        $days = _POST_ARR('days');

        $SQL = "SELECT du.*,
                       dr.rental_amount,
                       dr.rental_dues
                FROM dues_rental du
                  INNER JOIN drivers dr ON dr.id = du.id_driver
                WHERE du.id = $id";

        $due = $this->db->o($SQL);

        if (!$due) {
            $this->rsp['msg'] = 'No se reconoce el registro';

        } else if (count($days) > 7) {
            $this->rsp['msg'] = 'Solo se puede elegir 7 dias';

        } else {

            // Solo aplicar a dias no asignados
            $free_days = array_filter(explode(',', $due->free_days), 'strlen');
            foreach ($free_days as $d) {
                if (in_array($d, $days)) { // Si este dia ya fue descontado, no se descuenta nuevamente
                    unset($days[array_search($d, $days)]);
                }
            }

            $free_days = array_merge($free_days, $days);

            $amount = (float)$due->rental_amount;
            $amount_due = (float)$due->amount_due;

            $daily = ($amount / 7);
            $num_days = count($days);
            $discount = round(($daily * $num_days), 2);
            $new_amount = $amount_due - $discount;

            $this->rsp['$free_days'] = $free_days;
            $this->rsp['$days'] = $days;

            $this->rsp['amount'] = $amount;
            $this->rsp['num_days'] = $num_days;
            $this->rsp['daily'] = $daily;
            $this->rsp['amount_due'] = $amount_due;
            $this->rsp['discount'] = $discount;
            $this->rsp['new_amount'] = $new_amount;

            if ($discount > 0) {
                $data = [];
                $data['amount_due'] = $new_amount;
                $data['free_days'] = implode(',', $free_days);
                $data['fd_notes'] = $notes;

                if ($this->db->update('dues_rental', $data, $id)) {
                    $this->rsp['ok'] = true;

                } else {
                    $this->rsp['msg'] = 'Error interno :: DB';
                }

                // Obtener la ultima cuota
                // En caso que la cuota sea menor, aplicamos, caso contrario creamos nueva
                $SQL = "SELECT * FROM dues_rental
                        WHERE id_driver = $due->id_driver AND state != 0
                        ORDER BY date_due DESC LIMIT 1";
                $ld = $this->db->o($SQL);

                if (($ld->id != $due->id) && $ld->amount_due < $due->rental_amount) {

                    $amount_due = $ld->amount_due + $discount;
                    $amount_due_dif = $amount_due - $due->rental_amount;
                    $amount_due_dif = $amount_due_dif > 0 ? $amount_due_dif : 0;

                    $data = [];
                    $data['amount_due'] = $amount_due - $amount_due_dif;
                    $this->db->update('dues_rental', $data, $ld->id);

                    if ($amount_due_dif > 0) {
                        $lastTime = strtotime($ld->date_due);
                        $day_nam = date("l", $lastTime);
                        $nextTime = strtotime('next ' . $day_nam, $lastTime);
                        $date_due = date("Y-m-d", $nextTime);

                        $data = [];
                        $data['id_driver'] = $ld->id_driver;
                        $data['amount_due'] = $amount_due_dif;
                        $data['date_due'] = $date_due;
                        $data['state'] = 1;
                        $this->db->insert('dues_rental', $data);
                    }

                } else {
                    $lastTime = strtotime($ld->date_due);
                    $day_nam = date("l", $lastTime);
                    $nextTime = strtotime('next ' . $day_nam, $lastTime);
                    $date_due = date("Y-m-d", $nextTime);


                    $data = [];
                    $data['id_driver'] = $ld->id_driver;
                    $data['amount_due'] = $discount;
                    $data['date_due'] = $date_due;
                    $data['state'] = 1;
                    $this->db->insert('dues_rental', $data);

                }
                $this->rsp['$lastD'] = $ld;

            } else {

                // Guardar notas aunque no descuente
                if ($this->db->update('dues_rental', ['fd_notes' => $notes], $id)) {
                    $this->rsp['ok'] = true;

                } else {
                    $this->rsp['msg'] = 'Error interno :: DB';
                }

                //$this->rsp['msg'] = 'No se ha descontado';
            }
            $this->rsp['amount'] = $amount;
            $this->rsp['data'] = $days;

        }

        $this->rsp();
    }

    public function get_pics($id_ref)
    {
        //$id_driver = _POST_INT('id_driver');

        if ($id_ref <= 0) {
            $this->rsp['msg'] = 'No se reconoce el registro';

        } else {
            $this->rsp['items'] = $this->db->arr("SELECT * FROM pics WHERE type = 1 AND id_ref = $id_ref AND state = 1");
            $this->rsp['ok'] = true;

        }
        $this->rsp();
    }

    public function remove_pic()
    {
        $id = _POST_INT('id');

        if ($id <= 0) {
            $this->rsp['msg'] = 'No se reconoce el registro';

        } else {
            if ($this->db->update('pics', ['state' => 0], $id)) {
                $this->rsp['ok'] = true;
            }
        }
        $this->rsp();
    }

    public function upload_voucher()
    {
        $this->checkEditPerms();

        $id_ref = _POST_INT('id');

        $photo = (isset($_FILES['photo']) ? $_FILES['photo'] : '');

        $this->rsp['photo'] = $photo;

        if ($id_ref <= 0) {
            $this->rsp['msg'] = 'No se reconoce el registro';

        } else if (empty($photo['name'])) {
            $this->rsp['msg'] = 'No se ha seleccionado la imágen';

        } else {
            require('inc/plugins/ImageResize.php');
            $ext = pathinfo($photo['name'], PATHINFO_EXTENSION);
            $pic_voucher = md5(uniqid($id_ref)) . '.' . $ext;


            if (move_uploaded_file($photo['tmp_name'], 'uploads/' . $pic_voucher)) {
                $data = [];
                $data['type'] = 1;
                $data['id_ref'] = $id_ref;
                $data['pic'] = $pic_voucher;
                $data['state'] = 1;
                if ($this->db->insert('pics', $data)) {
                    $this->rsp['pic'] = $pic_voucher;
                    $this->rsp['ok'] = true;

                } else {
                    $this->rsp['msg'] = 'Error interno :: DB';
                }
                /*if($this->db->update('dues_rental', ['pic_voucher'=>$pic_voucher], $id_ref)){
                    $this->rsp['pic_voucher'] = $pic_voucher;
                    $this->rsp['ok'] = true;

                } else {
                    $this->rsp['msg'] = 'Error interno :: DB';
                }*/
            } else {
                $this->rsp['msg'] = 'Error al guardar la imágen';
            }

            /*$image = new ImageResize($photo['tmp_name']);
            $image->width(600);
            $image->height(600);
            $image->resize();
            if($image->save('uploads/'.$pic_voucher)){
                if($this->db->update('dues_rental', ['pic_voucher'=>$pic_voucher], $id_ref)){
                    $this->rsp['pic_voucher'] = $pic_voucher;
                    $this->rsp['ok'] = true;

                } else {
                    $this->rsp['msg'] = 'Error interno :: DB';
                }
            } else {
                $this->rsp['msg'] = 'Error al guardar la imágen';
            }*/

        }
        $this->rsp();
    }

}