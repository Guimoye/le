<?php class drivers extends _base{

    public function index(){

        // Evitar que un conductor pueda acceder a lista
        if($this->user->isDriver()){
            $this->redirect($this->user->getHome());
        }

        $ui = $this->ui();
        $ui->assign('page_title', 'Conductores');
        $ui->assign('brands', $this->db->arr("SELECT * FROM vh_brands WHERE state = 1"));
        $ui->assign('fleets', $this->db->arr("SELECT * FROM fleets WHERE state = 1 ORDER BY name"));
        $ui->display($this->module.'.tpl');
    }

    public function item($id){
        $this->inc('data');
        global $arr_months,$arr_days;

        $this->stg->isDriver = $this->user->isDriver();

        $driver = $this->db->o('drivers', $id);

        if($driver == FALSE){
            $this->exitUI('El conductor no existe');
        }

        $ui = $this->ui();

        $rental_started = false;

        $driver->debt = 0; // Deuda del conductor hasta la fecha

        if($driver->rental_date){
            $rental_started = true;

            $time = strtotime($driver->rental_date);
            $dayMonth = date('d', $time);
            $dayWeek = date('w', $time);
            $month = date('n', $time);
            $year = date('Y', $time);

            $driver->rental_date_formated = $dayMonth.' de '.$arr_months[$month-1].' de '.$year;
            $driver->rental_date_day = $arr_days[$dayWeek];

            $driver->rental_age_weeks = $this->uu->datediffInWeeks($driver->rental_date,date('Y-m-d'));

        }

        $today_date = date('Y-m-d');
        $today_time = strtotime($today_date);

        $finish_time = ($driver->state == 2 ? strtotime($driver->date_finish) : 0);

        // Si es conductor finalizado, sumar deuda de cuotas prestamos
        if($driver->state == 2){
            $os = $this->db->get("SELECT dl.*
                                  FROM dues_loans dl
                                    LEFT JOIN loans lo ON lo.id = dl.id_loan
                                  WHERE lo.id_driver = $id AND dl.state = 1");
            while($o = $os->fetch_object()){
                $driver->debt += ($o->amount_due+$o->amount_previous);
            }
        }

        // Alquiler
        $rental = new stdClass();
        $rental->total_due = 0;
        $rental->total_paid = 0;
        $rental->total_items_paid = 0; // total de cuotas pagadas
        $rental->total_items = 0; // Total de cuotas
        $rental->date_next_pay = null; // Fecha de siguiente pago, para detectar si ha vencido
        $rental->weeks_late = 0; // Semanas de retraso
        $rental->last_date_paid = ''; // Ultima fecha pagada

        $os = $this->db->get("SELECT * FROM dues_rental WHERE id_driver = $driver->id AND state != 0 ORDER BY date_due");
        while($o = $os->fetch_object()){
            $rental->total_due += $o->amount_due;

            if($o->state == 2 || $o->state == 3){
                $rental->total_paid += $o->amount_due;
            }

            if(!$rental->date_next_pay && $o->amount_paid == 0){
                $rental->date_next_pay = $o->date_due;
            }

            $due_time = strtotime($o->date_due);

            if($o->state == 1 && $due_time < $today_time){
                $rental->weeks_late += 1;
            }

            if($o->amount_paid > 0){
                $rental->last_date_paid = $o->date_paid;

                $rental->total_items_paid += 1;
            }

            // Deuda vencida si conductor fue finalizado
            if($driver->state == 2){
                if($o->state == 1 && $due_time <= $finish_time){
                    $driver->debt += $o->amount_due;
                }
            }

            $rental->total_items += 1;
        }
        $rental->percent = @($rental->total_paid/$rental->total_due*100) ?: 0;

        // Venta
        $sale = new stdClass();
        $sale->total_due = 0;
        $sale->total_paid = 0;
        $sale->total_items_paid = 0; // total de cuotas pagadas
        $sale->total_items = 0; // Total de cuotas
        $sale->date_next_pay = null; // Fecha de siguiente pago, para detectar si ha vencido
        $sale->weeks_late = 0; // Semanas de retraso
        $sale->last_date_paid = ''; // Ultima fecha pagada

        $os = $this->db->get("SELECT * FROM dues_sale WHERE id_driver = $driver->id AND state != 0 ORDER BY date_due");
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

                $sale->total_items_paid += 1;
            }

            $sale->total_items += 1;
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

        $os = $this->db->get("SELECT * FROM expenses WHERE id_driver = $driver->id AND state = 1 ORDER BY date_pay");
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

        // Prestamos
        $loans = new stdClass();
        $loans->total_items = 0;
        $loans->total_amount = 0;
        $loans->total_amount_paid = 0;
        $loans->date_next_pay = null; // Fecha de siguiente pago, para detectar si ha vencido
        $loans->weeks_late = 0; // Semanas de retraso
        $loans->last_amount = ''; // Ultimo registro
        $loans->last_date_pay = ''; // Ultima fecha pagada

        $os = $this->db->get("SELECT * FROM loans WHERE id_driver = $driver->id AND state = 1 ORDER BY date_pay");
        while($o = $os->fetch_object()){
            $loans->total_items += 1;
            $loans->total_amount += $o->amount;

            if($o->date_paid){
                $loans->total_amount_paid += $o->amount;
                $loans->last_date_pay = $o->date_paid;
            }

            if(!$loans->date_next_pay && $o->date_paid){
                $loans->date_next_pay = $o->date_pay;
            }

            if(!$o->date_paid && strtotime($o->date_pay) < time()){
                $loans->weeks_late += 1;
            }
        }
        $loans->percent = @($loans->total_paid/$loans->total_due*100) ?: 0;

        // Mantenimientos Normal
        $maintenances = new stdClass();
        $maintenances->next_kms         = 0; // Siguientes kms
        $maintenances->next_date_item   = '---'; // Siguiente fecha
        $maintenances->next_amount      = ''; // Siguiente monto

        $o = $this->db->o("SELECT * FROM maintenances WHERE id_driver = $driver->id AND type = 1 AND state = 1 ORDER BY date_item DESC LIMIT 1");
        if($o){
            $maintenances->next_kms         = $o->kms;
            $maintenances->next_date_item   = $o->date_item;
            $maintenances->next_amount      = $o->amount - $o->amount_stored;
        }

        // Mantenimientos Normal
        $maintenances_gas = new stdClass();
        $maintenances_gas->next_kms         = 0; // Siguientes kms
        $maintenances_gas->next_date_item   = '---'; // Siguiente fecha
        $maintenances_gas->next_amount      = ''; // Siguiente monto

        $o = $this->db->o("SELECT * FROM maintenances WHERE id_driver = $driver->id AND type = 2 AND state = 1 ORDER BY date_item DESC LIMIT 1");
        if($o){
            $maintenances_gas->next_kms         = $o->kms;
            $maintenances_gas->next_date_item   = $o->date_item;
            $maintenances_gas->next_amount      = $o->amount - $o->amount_stored;
        }

        // Oblicaciones (proyeccion de gastos)
        $obligations = new stdClass();
        $obligations->total_items = 0;
        $obligations->last_amount = ''; // Ultimo registro
        $obligations->last_date_pay = ''; // Ultima fecha pagada

        $os = $this->db->get("SELECT * FROM obligations WHERE id_driver = $driver->id AND state = 1");
        while($o = $os->fetch_object()){
            $obligations->total_items += 1;
            $obligations->last_amount = $o->amount;
            $obligations->last_date_pay = $o->date_end;
        }

        $ui->assign('page_title', 'Conductor');
        $ui->assign('driver', $driver);
        $ui->assign('rental', $rental);
        $ui->assign('sale', $sale);
        $ui->assign('expenses', $expenses);
        $ui->assign('loans', $loans);
        $ui->assign('maintenances', $maintenances);
        $ui->assign('maintenances_gas', $maintenances_gas);
        $ui->assign('obligations', $obligations);
        $ui->assign('rental_started', $rental_started);

        $ui->display('driver.tpl');
    }

    public function pager(){
        $max 		= isset($_POST['max']) 		&& is_numeric($_POST['max'])	? $_POST['max'] 	: 10;
        $page 		= isset($_POST['page'])		&& is_numeric($_POST['page'])	? $_POST['page']	: 1;
        $date_from	= isset($_POST['date_from'])	? trim($_POST['date_from'])	: '';
        $date_to 	= isset($_POST['date_to']) 		? trim($_POST['date_to'])	: '';
        $id_fleet   = _POST_INT('id_fleet');
        $word		= isset($_POST['word'])			? trim($_POST['word'])		: '';
        $state		= isset($_POST['state'])		? trim($_POST['state'])		: '';

        $offset = ($page - 1) * $max; // Offet

        $this->rsp['total'] = 0;

        $WHERE = "dr.state != 0";

        if($id_fleet > 0){
            $WHERE .= " AND dr.id_fleet = $id_fleet";
        }
        if(!empty($date_from) && !empty($date_to)){
            $WHERE .= " AND DATE(dr.date_added) between '$date_from' and '$date_to'";
        }
        if(!empty($word)){
            $word = '%'.str_replace(' ', '%', $word).'%';
            $WHERE .= " AND (CONCAT(dr.name,dr.surname) LIKE '$word')";
        }
        if(is_numeric($state)){
            $WHERE .= " AND dr.state = $state";
        }

        $canEdit = $this->canEdit();

        $SQL = "SELECT dr.*
                FROM drivers dr
                WHERE $WHERE
                GROUP BY dr.id
                ORDER BY dr.id DESC
                LIMIT $offset,$max";

        $os = $this->db->get($SQL);

        $table = '';
        $items = [];



        $today_date = date('Y-m-d');
        $today_time = strtotime($today_date);

        if($os){
            $this->rsp['total_items'] = $os->num_rows;

            $total_loans = 0;
            $total_expenses = 0;

            while($o = $os->fetch_object()){
                $o->total_loans = 0;
                $o->total_expenses = 0;

                $ol = $this->db->o("SELECT SUM(amount) amount_total FROM loans WHERE id_driver = $o->id AND state != 0 GROUP BY id_driver");
                if($ol) $o->total_loans = $ol->amount_total;

                $oe = $this->db->o("SELECT SUM(amount) amount_total FROM expenses WHERE id_driver = $o->id AND state != 0 GROUP BY id_driver");
                if($oe) $o->total_expenses = $oe->amount_total;

                $num_dues_rental_expired = $this->db->total("SELECT id FROM dues_rental WHERE id_driver = $o->id AND state = 1 AND date_due < CURDATE()");

                // Kilometraje
                $num_kms = 0;
                $num_kms_expired = 0;
                $num_kms_pendings = 0;
                $num_maints_paid = 0;
                $num_maints_expired = 0;
                $num_maints_pendings = 0;
                $oms = $this->db->get("SELECT * FROM maintenances WHERE id_driver = $o->id AND state != 0");
                while($om = $oms->fetch_object()){
                    $num_kms += $om->kms;

                    $due_time = strtotime($om->date_item);

                    if($om->state == 2){
                        $num_maints_paid += 1;

                    } else if($due_time < $today_time) {
                        $num_maints_expired += 1;
                        $num_kms_expired += $om->kms;

                    } else {
                        $num_maints_pendings += 1;
                        $num_kms_pendings += $om->kms;
                    }
                }



                // Obtener semanas de alquiler y deuda vencida
                $weeks_paid = 0; // Semanas pagadas
                $expired_debt = 0; // Deuda vencida

                $ols = $this->db->get("SELECT * FROM dues_rental WHERE id_driver = $o->id AND state != 0");
                while($ol = $ols->fetch_object()){
                    $due_time = strtotime($ol->date_due);

                    if($ol->state == 2 || $ol->state == 3){
                        $weeks_paid += 1;

                    } else if($due_time < $today_time) {
                        $expired_debt += $ol->amount_due;

                    } else {

                    }
                }

                $total_loans    += $o->total_loans;
                $total_expenses += $o->total_expenses;

                $link = 'drivers/'.$o->id;

                $items[''.$o->id] = $o;

                $table .= '<tr>';
                $table .= ' <td>';
                $table .= '  <a href="' . $link . '">' . $o->name . ' ' . $o->surname . ' </a>';

                if($num_dues_rental_expired > 0){
                    $table .= '  <div style="font-size:12px;color:red">Retraso de <b>'.$num_dues_rental_expired.'</b> cuotas</div>';
                }

                $table .= ' </td>';
                $table .= ' <td> ' . $o->vh_plate . ' </td>';


                $table .= ' <td> '.$this->stg->coin.number_format($o->total_expenses,2,'.','').' </td>';
                $table .= ' <td> '.$this->stg->coin.number_format($o->total_loans,2,'.','').' </td>';


                $table .= ' <td>';
                $table .= '  '.number_format($o->pmx_kms).' km';
                //$table .= '  '.number_format($num_kms).' km';
                if($num_maints_expired > 0){
                    $table .= '  <br><span class="badge bg-red-mint">Vencido '.number_format($num_kms_expired).' KM</span>';
                } else if($num_maints_pendings > 0){
                    $table .= '  <br><span class="badge bg-yellow-crusta">Pendiente '.number_format($num_kms_pendings).' KM</span>';
                } else if($num_maints_paid > 0) {
                    $table .= '  <br><span class="badge bg-green-jungle">Realizado</span>';
                }
                $table .= ' </td>';

                $table .= ' <td> ' . date('d/m/Y',strtotime($o->date_added)) . ' </td>';
                $table .= ' <td> '.$weeks_paid.' </td>';
                $table .= ' <td> '.$this->stg->coin.number_format($expired_debt,2,'.','').' </td>';
                $table .= ' <td class="nowrap">';
                $table .= '  <a href="' . $link . '" class="btn btn-outline btn-circle dark btn-sm font-md"><i class="fa fa-eye"></i></a>';
                $table .= '  <a href="dues_rental/' . $o->id . '" class="btn btn-outline btn-circle dark btn-sm font-md"><i class="fa fa-bar-chart"></i></a>';

                if($canEdit){
                    $table .= '  <span class="btn btn-outline btn-circle dark btn-sm font-md" onclick="MDriver.edit(Pager.items[' . $o->id . ']);">';
                    $table .= '   <i class="fa fa-pencil"></i>';
                    $table .= '  </span>';
                }

                $table .= ' </td>';
                $table .= '</tr>';
            }

            $table .= '<tr>';
            $table .= ' <th colspan="2"></th>';
            $table .= ' <th>'.$this->stg->coin.number_format($total_expenses,2,'.','').'</th>';
            $table .= ' <th>'.$this->stg->coin.number_format($total_loans,2,'.','').'</th>';
            $table .= ' <th colspan="100%"></th>';
            $table .= '</tr>';
        }

        $this->rsp['data'] = $table;
        $this->rsp['items'] = $items;
        $this->rsp();
    }

    public function finish_driver(){
        $id = _POST_INT('id');
        if($id <= 0){
            $this->rsp['msg'] = 'ID incorrecto';

        } else {
            if($this->db->update('drivers', ['state'=>2,'date_finish'=>'NOW()'], $id)){
                $this->rsp['ok'] = true;

            } else $this->rsp['msg'] = 'Error interno :: DB';
        }

        $this->rsp();
    }

    public function add(){
        $this->checkEditPerms();

        $id = _POST_INT('id');

        $isEdit = (is_numeric($id) && $id > 0);

        $data = array();
        $data['id_fleet'] 		    = _POST_INT('id_fleet');
        $data['name'] 			    = _POST('name');
        $data['surname'] 			= _POST('surname');
        $data['date_birth'] 		= _POST('date_birth');
        $data['dni'] 			    = _POST('dni');
        $data['ruc'] 			    = _POST('ruc');
        $data['driver_licence'] 	= _POST('driver_licence');
        $data['city'] 			    = _POST('city');
        $data['district'] 			= _POST('district');
        $data['address'] 			= _POST('address');
        $data['phone_cell'] 		= _POST('phone_cell');
        $data['phone_house'] 		= _POST('phone_house');
        $data['email'] 			    = _POST('email');
        $data['civil_status'] 		= _POST('civil_status');
        $data['wife_name'] 			= _POST('wife_name');
        $data['wife_dni'] 			= _POST('wife_dni');
        $data['bank_name'] 			= _POST('bank_name');
        $data['bank_account'] 		= _POST('bank_account');

        $data['gt_name'] 			= _POST('gt_name');
        $data['gt_dni'] 			= _POST('gt_dni');
        $data['gt_city'] 		    = _POST('gt_city');
        $data['gt_district'] 		= _POST('gt_district');
        $data['gt_address'] 		= _POST('gt_address');
        $data['gt_phone'] 			= _POST('gt_phone');
        $data['gt_email'] 			= _POST('gt_email');
        $data['gt_job_place'] 		= _POST('gt_job_place');
        $data['gt_job_role'] 		= _POST('gt_job_role');
        $data['gt_job_address'] 	= _POST('gt_job_address');
        $data['gt_job_city'] 	    = _POST('gt_job_city');
        $data['gt_job_district'] 	= _POST('gt_job_district');
        $data['gt_job_phone'] 		= _POST('gt_job_phone');
        $data['gt_job_boss_name'] 	= _POST('gt_job_boss_name');
        $data['gt_job_boss_role'] 	= _POST('gt_job_boss_role');
        $data['gt_job_boss_email'] 	= _POST('gt_job_boss_email');

        $data['id_brand'] 			= _POST('id_brand');
        $data['id_model'] 			= _POST('id_model');
        $data['vh_plate'] 			= _POST('vh_plate');
        $data['vh_year'] 			= _POST('vh_year');
        $data['vh_color'] 			= _POST('vh_color');
        $data['vh_engine_number'] 	= _POST('vh_engine_number');
        $data['vh_serial_chassis'] 	= _POST('vh_serial_chassis');
        $data['vh_fuel'] 			= _POST('vh_fuel');
        $data['vh_gps_number'] 		= _POST('vh_gps_number');
        $data['state']			    = _POST('state', 1);

        if($data['id_fleet'] <= 0){
            $this->rsp['msg'] = 'Elige la <b>Flota</b>';

        } else if(empty($data['name'])){
            $this->rsp['msg'] = '<b>Nombre</b> incorrecto';

        } else if(empty($data['surname'])){
            $this->rsp['msg'] = '<b>Apellido</b> incorrecto';

        } else if(strlen($data['dni']) != 8){
            $this->rsp['msg'] = '<b>DNI</b> incorrecto';

        } else if(strlen($data['driver_licence']) < 8){
            $this->rsp['msg'] = 'Número de <b>Licencia de conducir</b> incorrecta';

        } else {
            $password = _POST('password');
            if(!empty($password)){
                $data['password'] = md5($password);
            }

            if($isEdit){
                if($this->db->update('drivers', $data, $id)){
                    $this->rsp['ok'] = true;
                    $this->rsp['id'] = $id;
                } else {
                    $this->rsp['msg'] = 'Se produjo un error al editar';
                }
            } else {
                $data['id_user'] = $this->user->id;
                if($this->db->insert('drivers', $data)){
                    $this->rsp['ok'] = true;
                    $this->rsp['id'] = $this->db->lastID();
                } else {
                    $this->rsp['msg'] = 'Se produjo un error al registrar';
                }
            }

            $id = $this->rsp['id'];
            if($this->rsp['ok'] && $id > 0){

                $this->uploadPicToDriverByFile($id, @$_FILES['photo']);

            }
        }
        $this->rsp();
    }

    public function remove(){
        $id = _POST_INT('id');
        if(is_numeric($id) && $id > 0)
        {
            if($this->db->query("UPDATE drivers SET state = 0 WHERE id = $id"))
            {
                $this->rsp['ok'] = true;

            } else $this->rsp['msg'] = 'Error DB :: No se pudo eliminar';
        } else $this->rsp['msg'] = 'No se puede reconocer';
        $this->rsp();
    }

    public function get_models_brand($id_brand){
        $models = [];

        $os = $this->db->get("SELECT * FROM vh_models WHERE id_brand = $id_brand AND state = 1");
        while($o = $os->fetch_object()){
            $models[] = [
                'id' => $o->id,
                'name' => $o->name
            ];
        }

        $this->rsp['models']    = $models;
        $this->rsp['ok']        = true;
        $this->rsp();
    }

    /**
     * @param $id
     * @param $photo
     * @return array
     */
    private function uploadPicToDriverByFile($id, $photo){
        $rsp = [
            'ok' => false,
            'msg' => '---'
        ];

        if($id <= 0){
            $rsp['msg'] = 'No se reconoce el registro';

        } else if(empty($photo['name'])){
            $rsp['msg'] = 'No se ha seleccionado la imágen';

        } else {
            require_once('inc/plugins/ImageResize.php');
            $ext = pathinfo($photo['name'], PATHINFO_EXTENSION);
            $pic = md5(uniqid($id)).'.'.$ext;

            $image = new ImageResize($photo['tmp_name']);
            $image->width(600);
            $image->height(600);
            $image->resize();
            if($image->save('uploads/'.$pic)){
                if($this->db->update('drivers', ['pic'=>$pic], $id)){
                    $rsp['pic'] = $pic;
                    $rsp['ok'] = true;

                } else {
                    $rsp['msg'] = 'Error interno :: DB';
                }
            } else {
                $rsp['msg'] = 'Error al guardar la imágen';
            }

        }

        return $rsp;
    }

    public function upload_pic(){
        $this->checkEditPerms();

        $id = _POST_INT('id');

        $photo = (isset($_FILES['photo']) ? $_FILES['photo'] : '' );

        $this->rsp['photo'] = $photo;

        $this->rsp = $this->uploadPicToDriverByFile($id, $photo);

        $this->rsp();
    }

    public function import_cabify(){

        $csv = $this->uu->getDataCSV(@$_FILES['file']['tmp_name']);

        $ix_dni     = -1;
        $ix_amount  = -1;

        // Obtener los indices de las columnas para identificar datos
        foreach($csv->cols as $ix => $col){
            if($col == 'No. DNI')           $ix_dni     = $ix;
            if($col == 'Cuota recaudada')   $ix_amount  = $ix;
        }

        // Indices de columnas corectos
        if($ix_dni != -1 && $ix_amount != -1){

            $items = [];

            foreach($csv->rows as $row){
                $item = [];
                $dni    = trim(@$row[$ix_dni]);
                $amount = (float) @$row[$ix_amount];

                $item['ok'] = false;
                $item['dni'] = $dni;
                $item['amount'] = $amount;

                if(empty($dni)){
                    $item['msg'] = 'DNI incorrecto';

                } else if($amount < 0){
                    $item['msg'] = 'Monto incorrecto';

                } else{
                    $dv = $this->db->o('drivers','dni',$dni);
                    if($dv){
                        $item['driver'] = [
                            'id' => (int) $dv->id,
                            'name' => $dv->name.' '.$dv->surname
                        ];

                        $SQL = "SELECT *
                                FROM dues_rental
                                WHERE id_driver = $dv->id AND WEEKOFYEAR(date_due) = WEEKOFYEAR(NOW())
                                      AND state != 0
                                LIMIT 1";
                        $du = $this->db->o($SQL);
                        if($du){
                            $item['due'] = [
                                'id' => (int) $du->id,
                                'num_due' => (int) $du->num_due,
                                'date_due' => $du->date_due
                            ];

                            if($this->db->update('dues_rental', ['amount_cabify'=>$amount], $du->id)){

                                // esto es porlas desde la ultima actualizacion
                                if($this->db->update('drivers',['dni'=>$dni], $dv->id)){
                                    $item['ok'] = true;

                                } else {
                                    $item['msg'] = 'Error interno :: Actualizar ID Cabify';
                                }

                            } else {
                                $item['msg'] = 'Error interno :: DB';
                            }
                        } else {
                            $item['msg'] = 'No hay cuota para esta semana';
                        }
                    } else {
                        $item['msg'] = 'Conductor no existe';
                    }
                }

                $items[] = $item;
            }
            $this->rsp['items'] = $items;
            $this->rsp['ok'] = true;

        } else {
            $this->rsp['msg'] = 'Indices incorrectos';
        }

        //$this->rsp['$csv'] = $csv;

        $this->rsp();
    }

    public function generate_cabify_template(){
        header('Content-Type: application/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename="modelo_cabify.csv";');

        echo 'No. DNI,Nombre Completo,Cuota Mensual,Cuota recaudada';

        $os = $this->db->get("SELECT * FROM drivers WHERE state != 0");
        while($o = $os->fetch_object()){
            echo "\n";
            echo $o->dni.','.$o->name.' '.$o->surname.',, ';
        }

    }

    function debug($data) {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
    function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES);
    }
}