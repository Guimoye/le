{include file='_header.tpl' css=[
    'assets/global/plugins/jquery-ui-autocomplete/jquery-ui.min.css',
    'assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.css'
]}

<style type="text/css">

    .amt{ background:#26C281; border-radius:4px!important; color:white; padding:5px 10px; display:flex;
        justify-content:space-between }
    .amt.blue{ background:#1991EB; }
    .amt.red{ background:#ED1C24; }
    .amt.total_cont{ cursor:pointer; }
    .amt.total_cont:hover{ box-shadow:5px 5px 10px rgba(0,0,0,.2)}
    .amt.total_cont:active{ box-shadow:inset 5px 5px 10px rgba(0,0,0,.2)}

    .tbl_calc{ width:100%}
    .tbl_calc table{ width:100% }
    .tbl_calc tr td{ padding:5px }

    .table tfoot tr td{ padding:8px!important; }

</style>

<div class="portlet light" style="padding:5px;" id="acts">

    <div class="form-inline clearfix">

        <div class="pull-left" style="line-height:32px">
            <i class="fa fa-info-circle"></i> Caja <b>{$rb.bo_name}</b> abierta desde <b>{$rb.date_added}</b>
        </div>

        <div class="pull-right">

            <button class="btn dark btn-circle ---" onclick="MAnnul.open({$rb.id})"> <i class="fa fa-close"></i> Anular Ventas </button>
            <button class="btn dark btn-circle hide"> <i class="fa fa-gavel"></i> Dividir Cuentas </button>
            <button class="btn dark btn-circle show_tables" style="display:none">
                <i class="fa fa-arrow-left"></i> Regresar a las Mesas
            </button>

        </div>

    </div>

</div>

<div class="portlet light" style="display:block" id="rooms">

    {if empty($rooms)}
        <div class="alert alert-warning"> No se han creado salas todavía. </div>
    {else}

        <div class="portlet-title tabbable-line">

            <ul class="nav nav-tabs pull-left">
                {foreach key=i item=o from=$rooms}
                    <li class="{if $i==0}active{/if}">
                        <a href="#room_{$o.id}" data-toggle="tab"> {$o.name} </a>
                    </li>
                {/foreach}
            </ul>

            <div class="actions">
                {*<button class="btn dark btn-circle reload" onclick="RQST.loadTables();" style="min-width:30px;padding-left:0;padding-right:0">
                    <i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
                </button>*}
                <button class="btn dark btn-circle reload" style="min-width:30px;padding-left:0;padding-right:0">
                    <i class="fa fa-refresh"></i>
                </button>
            </div>

        </div>

        <div class="portlet-body">
            <div class="tab-content">
                {foreach key=i item=o from=$rooms}
                    <div class="tab-pane {if $i==0}active{/if}" id="room_{$o.id}">
                        <div class="clearfix tables room room_{$o.id}" style="margin-left:0">

                            <i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i> Obteniendo mesas...

                            {*{if empty($o.tables)}
                                <div class="alert alert-warning"> No hay mesas. </div>
                            {else}
                                {foreach key=x item=t from=$o.tables}
                                    <div class="item ready" id="table_{$t.id}" onclick="RQST.setTable({$t.id},'{$t.name}');">
                                        <span>{$t.name}</span>
                                        <b>0</b>
                                    </div>
                                {/foreach}
                            {/if}*}
                        </div>
                    </div>
                {/foreach}
            </div>
        </div>
    {/if}

</div>

<div class="row" id="order" style="display:none">

    <div class="col-md-6">


        <div class="portlet light hide">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject uppercase bold font-yellow-gold">Mesa: M01</span>
                    <span class="caption-helper uppercase">Detalle de mesa</span>
                </div>
            </div>
            <div class="portlet-body">

                <table class="table table-bordered mdl-td">
                    <thead>
                    <tr>
                        <th>Descripción</th>
                        <th width="85px" class="ctr">Precio</th>
                        <th width="1%" class="ctr">Cant.</th>
                        <th width="85px" class="ctr"> Desc. </th>
                        <th width="90px" class="ctr"> Total </th>
                        <th width="1%"></th>
                    </tr>
                    </thead>
                    <tbody class="list"></tbody>
                    <tfoot>
                    <tr>
                        <td class="ctr">
                            <button class="btn yellow-gold btn-circle hide">
                                <i class="fa fa-refresh"></i> Agrupar peridos
                            </button>
                            <button class="btn blue-madison btn-circle">
                                <i class="fa fa-print"></i> Imprimir Precuenta
                            </button>
                        </td>
                        <td colspan="2" class="uppercase"> Total </td>
                        <td class="ctr pdg_h_0"> {$stg->coin}<b class="total">0.00</b> </td>
                        <td class="ctr pdg_h_0"> {$stg->coin}<b class="order_total_price">9999.00</b> </td>
                        <td> </td>
                    </tr>
                    </tfoot>
                </table>

            </div>
        </div>

        <div id="accounts"></div>

        <button class="btn green-jungle btn-circle btn-block add_subaccount"> Agregar Subcuenta </button>

    </div>


    <div class="col-md-6">
        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject uppercase bold font-yellow-gold form_title">Mesa: M01</span>
                    <span class="caption-helper uppercase">Datos de pago</span>
                </div>
            </div>
            <div class="portlet-body">

                <form class="form" id="form_box" onsubmit="return false;">
                    <input type="hidden" name="action" value="pay">
                    <input type="hidden" name="account">
                    <input type="hidden" name="id_regbox" value="{$rb.id}">
                    <input type="hidden" name="id_order">
                    <input type="hidden" name="id_promo">
                    <input type="hidden" name="price" value="25">

                    <div class="form-group client_cont">
                        <label>Cliente</label>
                        <div class="input-group">
                            <input type="hidden" name="id_client">
                            <input type="text" name="client_name" class="form-control" placeholder="Público en General">
                            <span class="input-group-btn" style="width:10px"></span>
                            <span class="input-group-btn">
                                <button class="btn btn-default btn-circle">Crear Cliente</button>
                            </span>
                        </div>
                    </div>

                    <div class="row">

                        <div class="col-md-6">

                            <div class="form-group">
                                <label>Comprobante de pago</label>
                                <select name="id_proof" class="form-control">
                                    {foreach item=o from=$proofs}
                                        <option value="{$o.id}">{$o.name}</option>
                                    {/foreach}
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Propina Mozo</label>
                                <input type="text" name="tip" class="form-control" placeholder="0.00">
                            </div>

                            <div class="form-group">
                                <label>Descuento</label>
                                <div class="input-group">
                                    <input type="text" name="promo_amt" class="form-control" placeholder="{$stg->coin}">
                                    <span class="input-group-btn" style="width:10px"></span>
                                    <input type="text" name="promo_pct" class="form-control" placeholder="%">
                                    <span class="input-group-btn" style="width:10px"></span>
                                    <span class="input-group-btn">
                                        <button class="btn btn-default btn-circle promo_code_btn">CÓDIGO</button>
                                    </span>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="uppercase font-sm bold">Importe total</label>
                                <div class="amt total_cont">
                                    <div></div>
                                    <div>
                                        {$stg->coin}
                                        <input type="hidden" name="total" value="0">
                                        <b class="total">90.00</b>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label>
                                    <input type="checkbox" name="with_card"> Tarjeta de Crédigo
                                </label>
                            </div>

                            <div class="form-group" style="display:none">
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <select name="type_card" class="form-control" style="width:110px">
                                            <option value="1">Visa</option>
                                            <option value="1">MasterCard</option>
                                        </select>
                                    </span>
                                    <span class="input-group-btn" style="width:10px"></span>
                                    <span class="input-group-addon" style="border-right:0">{$stg->coin}</span>
                                    <input type="number" name="card" class="form-control" placeholder="0.00">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Efectivo</label>
                                <div class="input-group">
                                    <span class="input-group-btn">
                                        <select name="tc" class="form-control" style="width:110px">
                                            <option value="1">SOLES</option>
                                            <option value="1">DOLARES</option>
                                        </select>
                                    </span>
                                    <span class="input-group-btn" style="width:10px"></span>
                                    <span class="input-group-addon" style="border-right:0">{$stg->coin}</span>
                                    <input type="text" name="cash" class="form-control" placeholder="0.00">
                                </div>
                            </div>


                            <div class="amt blue give">
                                <div> <i class="fa fa-info-circle"></i> <span class="font-sm text">VUELTO</span> </div>
                                <div class="bold">{$stg->coin}<span class="amount">-.--</span></div>
                            </div>

                        </div>

                        <div class="col-md-6">

                            <table class="tbl_calc">

                                <tr>
                                    <td> <button class="btn dark btn-block btn-circle kbd_btn">500</button> </td>
                                    <td> <button class="btn dark btn-block btn-circle kbd_btn">200</button> </td>
                                    <td> <button class="btn dark btn-block btn-circle kbd_btn">100</button> </td>
                                </tr>
                                <tr>
                                    <td> <button class="btn dark btn-block btn-circle kbd_btn">50</button> </td>
                                    <td> <button class="btn dark btn-block btn-circle kbd_btn">20</button> </td>
                                    <td> <button class="btn dark btn-block btn-circle kbd_btn">10</button> </td>
                                </tr>
                                <tr>
                                    <td> </td>
                                    <td colspan="2" style="padding:0" width="66.66%">
                                        <table style="margin:0">
                                            <tr>
                                                <td> <button class="btn dark btn-block btn-circle kbd_btn">7</button> </td>
                                                <td> <button class="btn dark btn-block btn-circle kbd_btn">8</button> </td>
                                                <td> <button class="btn dark btn-block btn-circle kbd_btn">9</button> </td>
                                            </tr>
                                            <tr>
                                                <td> <button class="btn dark btn-block btn-circle kbd_btn">4</button> </td>
                                                <td> <button class="btn dark btn-block btn-circle kbd_btn">5</button> </td>
                                                <td> <button class="btn dark btn-block btn-circle kbd_btn">6</button> </td>
                                            </tr>
                                            <tr>
                                                <td> <button class="btn dark btn-block btn-circle kbd_btn">1</button> </td>
                                                <td> <button class="btn dark btn-block btn-circle kbd_btn">2</button> </td>
                                                <td> <button class="btn dark btn-block btn-circle kbd_btn">3</button> </td>
                                            </tr>
                                            <tr>
                                                <td> <button class="btn dark btn-block btn-circle kbd_btn">0</button> </td>
                                                <td> <button class="btn dark btn-block btn-circle kbd_btn">.</button> </td>
                                                <td> <button class="btn red-mint btn-block btn-circle kbd_btn">C</button> </td>
                                            </tr>
                                            <tr>
                                                <td colspan="4">
                                                    <button class="btn dark btn-block btn-circle green-jungle send_btn">
                                                        ENVIAR
                                                    </button>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>

                            </table>

                        </div>

                    </div>

                </form>

            </div>
        </div>
    </div>

</div>

<script>

    var categories = {$categories|@json_encode};
    var products = {$products|@json_encode};

    function $Ready(){
        BOX.init();
        //MAnnul.open({$rb.id});
        //MProduct.edit(products[0]);

        //chooseCategory();
    }
</script>

{include file='_footer.tpl' js=[
    'assets/global/plugins/jquery-ui-autocomplete/jquery-ui.min.js',
    'assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js',
    'js/box.js',
    'js/m_client.js'
]}