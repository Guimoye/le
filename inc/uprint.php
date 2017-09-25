<?php
class UPrint{

	public function __construct(){}

	public function cancel(){}

	// Configuracion base
	private static function getBase(){
        global $db,$user,$stg;

        $branch = $db->o('branches',$user->id_branch);

        // Buscamos la caja abierta para este usuario
        $SQL = "SELECT bo.*
                FROM regboxes re
                  INNER JOIN boxes bo ON bo.id = re.id_box
                WHERE re.id_user = $user->id AND re.state = 1";
        $box = $db->o($SQL);

        if(!$box){ // En caso de no haber caja abierta, elegimos alguna que tenga impresora
            $box = $db->o("SELECT * FROM boxes WHERE printer_ip != '' AND state != 0");
        }

        $data = [];

        $data['branch']     = @$branch->name;
        $data['address']    = @$branch->address;
        $data['phone']      = @$branch->phone;

        $data['box']        = @$box->name;

        $data['coin_sign']  = $stg->coin;
        $data['coin_name']  = $stg->coin_name;
        $data['comp_name']  = $stg->comp_name;
        $data['comp_ruc']   = $stg->comp_ruc;

        return $data;
	}

	private static function getBox(){
	    global $db,$user;
        // Buscamos la caja abierta para este usuario
        $SQL = "SELECT bo.*
                FROM regboxes re
                  INNER JOIN boxes bo ON bo.id = re.id_box
                WHERE re.id_user = $user->id AND re.state = 1";
        $box = $db->o($SQL);

        if(!$box){ // En caso de no haber caja abierta, elegimos alguna que tenga impresora
            $box = $db->o("SELECT * FROM boxes WHERE printer_ip != '' AND state != 0");
        }

        return $box;
    }

	// Pre cuenta
	public static function precuenta($id_order,$total,$ordpros){
		global $db;
		$data = self::getBase();

		$box = self::getBox();
        $data['line_letters']   = @$box->printer_line_letters;
        $data['printer_ip']     = @$box->printer_ip;
        $data['printer_name']   = @$box->printer_name;
        $data['printer_serial'] = @$box->printer_serial;

        $SQL = "SELECT od.*,
                       us.name us_name,
                       us.surname us_surname,
                       ta.name ta_name,
                       ro.name ro_name
                FROM orders od
                  LEFT JOIN users us ON us.id = od.id_user
                  LEFT JOIN tables ta ON ta.id = od.id_table
                    LEFT JOIN rooms ro ON ro.id = ta.id_room
                WHERE od.id = $id_order";
        $ord = $db->o($SQL);

        if($ord && is_array($ordpros)){

            $items = [];
            foreach($ordpros as $op){
                $items[] = [
                    'name' => sprintf('%02d', @$op['quantity']).' '.@$op['product'].' - '.@$op['propre'],
                    'value' => number_format(@$op['price_total'], 2, '.', '')
                ];
            }

            $data['type']           = 'precuenta';
            $data['waiter']         = $ord->us_name.' '.$ord->us_surname;
            $data['table']          = $ord->ro_name.' - '.$ord->ta_name;
            $data['num_service']    = sprintf('%010d', $ord->id);
            $data['price_total']    = number_format($total, 2, '.', '');
            $data['items']          = $items;
        }

		return $data;
	}

    /**
     * Imprimit una boleta
     * @param $id_transaction : ID de la transaccion
     * @return array : data_print
     */
    public static function transaction($id_transaction){
        global $db;
        $data = self::getBase();

        $box = self::getBox();
        $data['line_letters']   = @$box->printer2_line_letters;
        $data['printer_ip']     = @$box->printer2_ip;
        $data['printer_name']   = @$box->printer2_name;
        $data['printer_serial'] = @$box->printer2_serial;

        $SQL = "SELECT tr.*,
                       od.id od_id,
                       us.name us_name,
                       us.surname us_surname,
                       ta.name ta_name,
                       ro.name ro_name,
                       cl.name cl_name,
                       cl.dni cl_dni,
                       cl.address cl_address,
                       pr.code pr_code
                FROM transactions tr
                  LEFT JOIN orders od ON od.id = tr.id_ref
                    LEFT JOIN users us ON us.id = od.id_user
                    LEFT JOIN tables ta ON ta.id = od.id_table
                      LEFT JOIN rooms ro ON ro.id = ta.id_room
                  LEFT JOIN clients cl ON cl.id = tr.id_client
                  LEFT JOIN proofs pr ON pr.id = tr.id_proof
                WHERE tr.id = $id_transaction";
        $o = $db->o($SQL);

        $items = [];

        // Obtener pedidos de esta transaccion
        $SQL = "SELECT op.*,
                       pp.name pp_name,
                       pr.name pr_name
                FROM ordpros op
                  LEFT JOIN propres pp ON pp.id = op.id_propre
                    LEFT JOIN products pr ON pr.id = pp.id_product
                WHERE id_transaction = $id_transaction";
        $ops = $db->get($SQL);
        while($op = $ops->fetch_object()){
            $items[] = [
                'name' => sprintf('%02d', $op->quantity).' '.$op->pr_name.' - '.$op->pp_name,
                'value' => number_format($op->price_total, 2, '.', '')
            ];
        }

        // Definit tipo de impresion segun Codigo de comprobante SUNAT
        switch($o->pr_code){
            case '03': $data['type'] = 'boleta'; break;
            case '01': $data['type'] = 'factura'; break;
            case '12': $data['type'] = 'voucher'; break;
            //default:   $data['type'] = 'otro'; break;
        }

        // En caso de ruc
        $data['client_name']    = $o->cl_name;
        $data['client_ruc']     = $o->cl_dni;
        $data['client_address'] = $o->cl_address;

        $data['waiter']         = $o->us_name.' '.$o->us_surname;
        $data['table']          = $o->ro_name.' - '.$o->ta_name;
        $data['num_service']    = sprintf('%010d', $o->od_id);
        $data['num_voucher']    = sprintf('%010d', $o->id);
        $data['price_total']    = number_format($o->total, 2, '.', '');
        $data['price_base']     = number_format($o->base, 2, '.', '');
        $data['price_igv']      = number_format($o->igv, 2, '.', '');
        $data['items']          = $items;

        return $data;
    }

}