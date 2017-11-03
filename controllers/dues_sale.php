<?php class dues_sale extends _base{

    public function __construct(){
        parent::__construct();
        // El contructor

        $this->setModule('drivers');
    }

    private function index(){}

    public function item($id_driver){
        $ui = $this->ui();

        $driver = $this->db->o('drivers', $id_driver);

        if($driver == FALSE){
            $ui->display('e404.tpl');
            exit;
        }

        $items = [];
        $total_amount_due = 0;

        $os = $this->db->get("SELECT * FROM dues_sale WHERE id_driver = $driver->id");
        if($os){
            while($o = $os->fetch_assoc()){
                $o['amount_total'] = $o['amount_due']+
                    $o['amount_penalty'] +
                    $o['amount_interest'] +
                    $o['amount_insurance'] +
                    $o['amount_previous'];

                $total_amount_due += $o['amount_due'];

                if($o['amount_paid'] > 0){
                    $o['pay_state'] = 'paid';

                } else if(strtotime($o['date_due']) > time()) {
                    $o['pay_state'] = 'pending';

                } else {
                    $o['pay_state'] = 'expired';

                }

                if($o['amount_paid'] == 0){
                    $o['amount_paid'] = ($o['amount_total'] + ($o['amount_total']*$this->stg->igv/100));
                }

                $items[] = $o;
            }
        }

        $ui->assign('page_title', 'Cronograma de venta');
        $ui->assign('driver', $driver);
        $ui->assign('items', $items);
        $ui->assign('total_amount_due', $total_amount_due);

        $ui->display('dues_sale.tpl');
    }

    public function add(){
        $this->checkEditPerms();

        $id = _POST_INT('id');

        $id_driver  = _POST_INT('id_driver');
        $dues       = _POST_INT('dues');
        $amount     = _POST_INT('amount');
        $interest   = _POST_INT('interest');
        $insurance  = _POST_INT('insurance');
        $date       = _POST('date');

        if($id_driver <= 0){
            $this->rsp['msg'] = 'No se reconoce el conductor';

        } else if($this->db->has('dues_sale', 'id_driver', $id_driver)){
            $this->rsp['msg'] = 'El programa de alquiler para este conductor se generó previamente.';

        } else if($dues <= 0){
            $this->rsp['msg'] = 'Indica el número de cuotas';

        } else if($amount <= 0){
            $this->rsp['msg'] = 'Indica el monto de cuota';

        } else if(!$this->uu->isDate($date)){
            $this->rsp['msg'] = 'Indica la fecha de inicio';

        } else {

            $lastTime = strtotime($date);
            for($i = 1; $i <= $dues; $i++){

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

                $this->db->insert('dues_sale', [
                    'id_driver' => $id_driver,
                    'num_due' => $i,
                    'amount_due' => $amount,
                    'amount_interest' => $interest,
                    'amount_insurance' => $insurance,
                    'date_due' => $date_due
                ]);

                $lastTime = strtotime('next '.$day_nam, $lastTime);
            }

            if($this->db->update('drivers',['rental_dues'=>$dues, 'rental_amount'=>$amount, 'rental_date'=>$date], $id_driver)){
                $this->rsp['ok'] = true;

            } else {
                $this->rsp['msg'] = 'Error interno :: DB';
            }

        }

        $this->rsp();
    }

    public function edit(){
        $this->checkEditPerms();

        $id = _POST_INT('id');

        $data = [];
        $data['amount_penalty'] = _POST_INT('amount_penalty');

        if($id <= 0){
            $this->rsp['msg'] = 'No se reconoce el registro';

        } else {

            if($this->db->update('dues_sale', $data, $id)){
                $this->rsp['ok'] = true;

            } else {
                $this->rsp['msg'] = 'Error interno :: DB';
            }

        }

        $this->rsp();
    }

    public function set_due_paid(){
        $this->checkEditPerms();

        $id = _POST_INT('id');


        $amount_total = _POST_INT('amount_total');
        $amount = _POST_INT('amount');

        $due = $this->db->o('dues_sale', $id);

        if(!$due){
            $this->rsp['msg'] = 'No se reconoce el registro';

        } else if($amount <= 0){
            $this->rsp['msg'] = 'Debe ingresar un monto válido';

        } else {

            // La siguiente cuota
            if($amount < $amount_total){
                $next_amount = $amount_total - $amount; // A pagar el proximo mes

                // Obtener la siguiente cuota
                $nextO = $this->db->o("SELECT * FROM dues_sale WHERE date_due > '$due->date_due' LIMIT 1");
                if($nextO){
                    $this->db->update('dues_sale', ['amount_previous' => $next_amount], $nextO->id);
                }
            }

            $data = [];
            $data['amount_paid'] = $amount;
            $data['date_paid'] = 'NOW()';
            if($this->db->update('dues_sale', $data, $id)){
                $this->rsp['ok'] = true;
            } else {
                $this->rsp['msg'] = 'Error interno :: DB';
            }

        }

        $this->rsp();
    }

    public function set_due_unpaid(){
        $this->checkEditPerms();

        $id = _POST_INT('id');

        $data = [];
        $data['amount_paid'] = 0;
        $data['date_paid'] = '';
        if($this->db->query("UPDATE dues_sale SET amount_paid = 0, date_paid = NULL WHERE id = $id")){
            $this->rsp['ok'] = true;
        } else {
            $this->rsp['msg'] = 'Error interno :: DB';
        }

        $this->rsp();
    }

    public function upload_voucher(){
        $this->checkEditPerms();

        $id = _POST_INT('id');

        $photo = (isset($_FILES['photo']) ? $_FILES['photo'] : '' );
        if($id <= 0){
            $this->rsp['msg'] = 'No se reconoce el registro';

        } else if(empty($photo['name'])){
            $this->rsp['msg'] = 'No se ha seleccionado la imágen';

        } else {
            require('inc/plugins/ImageResize.php');
            $pic_voucher = md5(uniqid($id));

            $image = new ImageResize($photo['tmp_name']);
            $image->width(600);
            $image->height(600);
            $image->resize();
            if($image->save('uploads/'.$pic_voucher.'.jpg')){
                if($this->db->update('dues_sale', ['pic_voucher'=>$pic_voucher], $id)){
                    $this->rsp['pic_voucher'] = $pic_voucher;
                    $this->rsp['ok'] = true;

                } else {
                    $this->rsp['msg'] = 'Error interno :: DB';
                }
            } else {
                $this->rsp['msg'] = 'Error al guardar la imágen';
            }

        }

        $this->rsp();
    }

}