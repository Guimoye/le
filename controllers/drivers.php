<?php class drivers extends _base{

    public function __construct(){
        parent::__construct();
    }

    public function index(){
        $ui = $this->ui();
        $ui->assign('page_title', 'Conductores');
        $ui->assign('brands', $this->db->arr("SELECT * FROM vh_brands WHERE state = 1"));
        $ui->display($this->module.'.tpl');
    }

    public function item($id){
        $this->inc('data');
        global $arr_months,$arr_days;

        $driver = $this->db->o('drivers', $id);

        if($driver == FALSE){
            $this->exitUI('El conductor no existe');
        }

        $ui = $this->ui();

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

            $driver->rental_age_weeks = $this->uu->datediffInWeeks($driver->rental_date,date('Y-m-d'));

        }

        // Alquiler
        $rental = new stdClass();
        $rental->total_due = 0;
        $rental->total_paid = 0;
        $rental->date_next_pay = null; // Fecha de siguiente pago, para detectar si ha vencido
        $rental->weeks_late = 0; // Semanas de retraso
        $rental->last_date_paid = ''; // Ultima fecha pagada

        $os = $this->db->get("SELECT * FROM dues_rental WHERE id_driver = $driver->id ORDER BY date_due");
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

        $os = $this->db->get("SELECT * FROM dues_sale WHERE id_driver = $driver->id ORDER BY date_due");
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

        // Mantenimientos
        $maintenances = new stdClass();
        $maintenances->next_kms         = 0; // Siguientes kms
        $maintenances->next_date_item   = '---'; // Siguiente fecha
        $maintenances->next_amount      = ''; // Siguiente monto

        $o = $this->db->o("SELECT * FROM maintenances WHERE id_driver = $driver->id AND state = 1 ORDER BY date_item DESC LIMIT 1");
        if($o){
            $maintenances->next_kms         = $o->kms;
            $maintenances->next_date_item   = $o->date_item;
            $maintenances->next_amount      = $o->amount - $o->amount_stored;
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
        $ui->assign('maintenances', $maintenances);
        $ui->assign('obligations', $obligations);
        $ui->assign('rental_started', $rental_started);

        $ui->display('driver.tpl');
    }

    public function pager(){
        $max 		= isset($_POST['max']) 		&& is_numeric($_POST['max'])	? $_POST['max'] 	: 10;
        $page 		= isset($_POST['page'])		&& is_numeric($_POST['page'])	? $_POST['page']	: 1;
        $date_from	= isset($_POST['date_from'])	? trim($_POST['date_from'])	: '';
        $date_to 	= isset($_POST['date_to']) 		? trim($_POST['date_to'])	: '';
        $word		= isset($_POST['word'])			? trim($_POST['word'])		: '';
        $state		= isset($_POST['state'])		? trim($_POST['state'])		: '';

        $offset = ($page - 1) * $max; // Offet

        $this->rsp['total'] = 0;

        $WHERE = "state > 0";

        if(!empty($date_from) && !empty($date_to)){
            $WHERE .= " AND DATE(date_added) between '$date_from' and '$date_to'";
        }
        if(!empty($word)){
            $word = '%'.str_replace(' ', '%', $word).'%';
            $WHERE .= " AND (CONCAT(name,surname) LIKE '$word')";
        }
        if(is_numeric($state)){
            $WHERE .= " AND state = $state";
        }

        $SQL = "SELECT * FROM drivers WHERE $WHERE ORDER BY id DESC LIMIT $offset,$max";
        $os = $this->db->get($SQL);

        $table = '';
        $items = [];

        if($os){
            $this->rsp['total_items'] = $os->num_rows;
            while($o = $os->fetch_object()){
                $link = 'drivers/'.$o->id;

                $items[''.$o->id] = $o;

                $table .= '
					<tr>
						<td>
						    <a href="'.$link.'">'.$o->name.' '.$o->surname.'</a>
						    <div style="font-size:12px;color:red">Mant. 20,000km</div>
						</td>
						<td> '.$o->vh_plate.' </td>
						<td> --- </td>
						<td> '.$o->date_added.' </td>
						<td> --- </td>
						<td> '.$this->stg->coin.' -.-- </td>
						<td class="nowrap">
						    <a href="'.$link.'" class="btn btn-outline btn-circle dark btn-sm font-md"><i class="fa fa-eye"></i></a>
						    <a href="dues_rental.php?id='.$o->id.'" class="btn btn-outline btn-circle dark btn-sm font-md"><i class="fa fa-bar-chart"></i></a>
						    
							<span class="btn btn-outline btn-circle dark btn-sm font-md" onclick="MDriver.edit(Pager.items['.$o->id.']);">
								<i class="fa fa-pencil"></i>
							</span>
						</td>
					</tr>
				';
            }
        }

        $this->rsp['data'] = $table;
        $this->rsp['items'] = $items;
        $this->rsp();
    }

    public function add(){
        $this->checkEditPerms('drivers');

        $id = _POST_INT('id');

        $isEdit = (is_numeric($id) && $id > 0);

        $data = array();
        $data['name'] 			    = _POST('name');
        $data['surname'] 			= _POST('surname');
        $data['date_birth'] 		= _POST('date_birth');
        $data['dni'] 			    = _POST('dni');
        $data['ruc'] 			    = _POST('ruc');
        $data['driver_licence'] 	= _POST('driver_licence');
        $data['city'] 			    = _POST('city');
        $data['district'] 			= _POST('district');
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

        if(empty($data['name'])){
            $this->rsp['msg'] = '<b>Nombre</b> incorrecto';

        } else if(empty($data['surname'])){
            $this->rsp['msg'] = '<b>Apellido</b> incorrecto';

        } else if(strlen($data['dni']) != 8){
            $this->rsp['msg'] = '<b>DNI</b> incorrecto';

        } else if(strlen($data['driver_licence']) < 8){
            $this->rsp['msg'] = 'Número de <b>Licencia de conducir</b> incorrecta';

        } else {
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

}