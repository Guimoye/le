{include file='_header.tpl' css=[
    'assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css',
    'assets/global/plugins/fancybox/source/jquery.fancybox.css'
]}

<div class="portlet light">

    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject font-dark bold uppercase">{$page_title}</span>
            <br><a class="caption-helper" href="drivers/{$driver->id}">{$driver->name} {$driver->surname}</a>
        </div>
        <div class="actions">
            <!--span class="btn btn-circle blue" onclick="MDuesRental.add();"> <i class="fa fa-plus"></i> Registrar </span>-->
        </div>
    </div>

    <div class="portlet-body">

        <!--<div class="date-picker" data-date-format="mm/dd/yyyy"></div>-->

        {if empty($items)}
            <div class="alert alert-warning">
                No hay datos disponibles.
            </div>
        {else}
            <table class="table table-striped table-bordered table-hover dt-responsive mdl-td" style="margin-top:10px">
                <thead>
                <tr>
                    <th width="1%"> # </th>
                    <th width="1%"> Fecha </th>
                    <th> Alquiler <span class="font-xs">c. IGV</span> </th>
                    <th> Pozo </th>
                    <th> Cabify </th>
                    <th> Mora </th>
                    <th> Dsctos </th>
                    <th> Préstamos </th>
                    <th> Anterior </th>
                    <th> Saldo por pagar </th>
                    <th> Pago directo </th>
                    <th width="1%" class="nowrap"> Fecha de pago </th>
                    <th width="1%">Opciones</th>
                </tr>
                </thead>
                <tbody id="pager_content">
                {foreach key=i item=o from=$items}
                    <tr id="num_due_{$o.num_due}">
                        <td> {$i+1} </td>
                        <td class="nowrap"> {$o.date_due|date_format:"%d-%m-%Y"} </td>
                        <td> {$stg->coin}{$o.amount_due|string_format:"%.2f"} </td>
                        <td> {$stg->coin}{$o.amount_pit|string_format:"%.2f"} </td>
                        <td> {$stg->coin}{$o.amount_cabify|string_format:"%.2f"} </td>
                        <td> {$stg->coin}{$o.amount_penalty|string_format:"%.2f"} </td>
                        <td> {$stg->coin}{$o.amount_discount|string_format:"%.2f"} </td>
                        <td> {$stg->coin}{$o.amount_loans|string_format:"%.2f"} </td>
                        <td> {$stg->coin}{$o.amount_previous|string_format:"%.2f"} </td>
                        <td> {$stg->coin}{$o.amount_total|string_format:"%.2f"} </td>
                        <td> {$stg->coin}{$o.amount_paid|string_format:"%.2f"} </td>
                        <td class="nowrap">
                            {if !empty($o.date_paid)}{$o.date_paid|date_format:"%d-%m-%Y"}<br>{/if}
                            {if $o.pay_state == 'paid'}

                                {if $o.all_paid}
                                    <span {if $can_edit}onclick="MDuesRentalPay.open(items[{$i}]);"{/if}
                                            class="btn btn-xs green-jungle">Pagado</span>
                                {else}
                                    <span {if $can_edit}onclick="MDuesRentalPay.open(items[{$i}]);"{/if}
                                            class="btn btn-xs green">Pago parcial</span>
                                {/if}

                                <span onclick="MDays.open(items[{$i}],true);" class="btn btn-xs grey-salsa">{$o.worked_days_text}</span>

                            {elseif $o.pay_state == 'pending'}
                                <span {if $can_edit}onclick="MDuesRentalPay.open(items[{$i}]);"{/if}
                                      class="btn btn-xs yellow-crusta">Pendiente</span>

                            {elseif $o.pay_state == 'expired'}
                                <span {if $can_edit}onclick="MDuesRentalPay.open(items[{$i}]);"{/if}
                                      class="btn btn-xs red-mint">Vencido</span>

                            {/if}
                        </td>
                        <td class="nowrap">

                            {if $can_edit}
                                <span class="btn btn-outline btn-circle dark btn-sm font-md" onclick="MVoucher.open(1, {$o.id});">
                                    <i class="fa fa-paperclip"></i>
                                </span>

                                <span class="btn btn-outline btn-circle dark btn-sm font-md" onclick="MDays.open(items[{$i}]);">
                                    <i class="fa fa-calendar-o"></i>
                                </span>
                            {/if}

                        </td>
                    </tr>
                {/foreach}

                {if $can_edit}
                    <tr style="background:#e7ecf1">
                        <td colspan="2"></td>
                        <th>{$stg->coin}{$tts.total_amount_due|string_format:"%.2f"}</th>
                        <th>{$stg->coin}{$tts.total_amount_pit|string_format:"%.2f"}</th>
                        <th>{$stg->coin}{$tts.total_amount_cabify|string_format:"%.2f"}</th>
                        <th>{$stg->coin}{$tts.total_amount_penalty|string_format:"%.2f"}</th>
                        <th>{$stg->coin}{$tts.total_amount_discount|string_format:"%.2f"}</th>
                        <th>{$stg->coin}{$tts.total_amount_loans|string_format:"%.2f"}</th>
                        <th>{$stg->coin}{$tts.total_amount_previous|string_format:"%.2f"}</th>
                        <th>{$stg->coin}{$tts.total_amount_total|string_format:"%.2f"}</th>
                        <th>{$stg->coin}{$tts.total_amount_paid|string_format:"%.2f"}</th>
                        <td colspan="1"></td>
                        <th>
                        <span class="btn btn-outline btn-circle red btn-xs font-md tooltips"
                              title="Eliminar cronograma de alquiler"
                              onclick="MDuesRental.removeAll({$driver->id});">
                            <i class="fa fa-trash"></i> Eliminar
                        </span>
                        </th>
                    </tr>
                {/if}

                </tbody>
            </table>
        {/if}

    </div>

</div>

<!-- MODAL -->
<div id="modal_add_dues_rental" class="modal fade modal-scroll" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <!--<button type="button" class="close" data-dismiss="modal"></button>-->
                <h4 class="modal-title">---</h4>
            </div>
            <div class="modal-body">

                <form class="form-horizontal">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="id" value="">
                    <input type="hidden" name="id_driver" value="{$driver->id}">

                    <div class="form-group">
                        <label class="col-md-4 control-label">Número de cuotas</label>
                        <div class="col-md-6">
                            <input class="form-control input-lg" name="dues" placeholder="0">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label">Monto de cuota</label>
                        <div class="col-md-6">
                            <div class="input-group input-group-lg">
                                <span class="input-group-addon">{$stg->coin}</span>
                                <input class="form-control" name="amount" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label">Pozo de mantenimiento</label>
                        <div class="col-md-6">
                            <div class="input-group input-group-lg">
                                <span class="input-group-addon">{$stg->coin}</span>
                                <input class="form-control" name="amount_pit" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label">Fecha de inicio</label>
                        <div class="col-md-6">
                            <input class="form-control input-lg" name="date" type="date">
                        </div>
                    </div>

                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn red pull-left remove">Eliminar</button>
                <button type="button" data-dismiss="modal" class="btn btn-outline btn-default cancel hide">Cancelar</button>
                <button type="button" class="btn blue save">Generar</button>
            </div>
        </div>
    </div>
</div>
<!-- END MODAL -->

<!-- MODAL -->
<div id="modal_pay_dues_rental" class="modal fade modal-scroll" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <form class="modal-content form-horizontal">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h4 class="modal-title">---</h4>
            </div>
            <div class="modal-body">

                    <input type="hidden" name="id" value="">
                    <input type="hidden" name="amount_total" value="">

                    <div class="form-group">
                        <label class="col-md-4 control-label">Monto a pagar</label>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon">{$stg->coin}</span>
                                <input class="form-control" name="amount_paid" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label">Monto Cabify</label>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon">{$stg->coin}</span>
                                <input class="form-control" name="amount_cabify" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label">Moras</label>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon">{$stg->coin}</span>
                                <input class="form-control" name="amount_penalty" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label">Descuentos</label>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon">{$stg->coin}</span>
                                <input class="form-control" name="amount_discount" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label">Fecha de pago</label>
                        <div class="col-md-6">
                            <input class="form-control" name="date_paid" type="date">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label">Comprobante de Pago</label>
                        <div class="col-md-6">
                            <input class="form-control" name="voucher_code" placeholder="Código de comprobante de pago">
                        </div>
                    </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn red pull-left remove hide">Eliminar</button>
                <button type="button" data-dismiss="modal" class="btn btn-outline btn-default cancel">Cancelar</button>
                <button class="btn blue save">Generar</button>
            </div>
        </form>
    </div>
</div>
<!-- END MODAL -->

<!-- MODAL FREE DAYS -->
<div id="modal_add_days" class="modal fade modal-scroll" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h4 class="modal-title">---</h4>
            </div>
            <div class="modal-body">

                <form class="form">
                    <input type="hidden" name="action" value="set_free_days">
                    <input type="hidden" name="id" value="">

                    <table class="table table-bordered ctr-td mdl-td" style="margin-bottom:0">
                        <tr>
                            <td style="padding:0">
                                <label style="display:block;padding:8px">
                                    <div>LU</div>
                                    <input type="checkbox" name="days[]" value="0" class="day_0">
                                </label>
                            </td>
                            <td style="padding:0">
                                <label style="display:block;padding:8px">
                                    <div>MA</div>
                                    <input type="checkbox" name="days[]" value="1" class="day_1">
                                </label>
                            </td>
                            <td style="padding:0">
                                <label style="display:block;padding:8px">
                                    <div>MI</div>
                                    <input type="checkbox" name="days[]" value="2" class="day_2">
                                </label>
                            </td>
                            <td style="padding:0">
                                <label style="display:block;padding:8px">
                                    <div>JU</div>
                                    <input type="checkbox" name="days[]" value="3" class="day_3">
                                </label>
                            </td>
                            <td style="padding:0">
                                <label style="display:block;padding:8px">
                                    <div>VI</div>
                                    <input type="checkbox" name="days[]" value="4" class="day_4">
                                </label>
                            </td>
                            <td style="padding:0">
                                <label style="display:block;padding:8px">
                                    <div>SA</div>
                                    <input type="checkbox" name="days[]" value="5" class="day_5">
                                </label>
                            </td>
                            <td style="padding:0">
                                <label style="display:block;padding:8px">
                                    <div>DO</div>
                                    <input type="checkbox" name="days[]" value="6" class="day_6">
                                </label>
                            </td>
                        </tr>
                    </table>

                    <div class="form-group" style="margin:10px 0 0 0">
                        <label>Comentarios</label>
                        <div class="input-group">
                            <span class="input-group-addon">
                                <i class="fa fa-comment"></i>
                            </span>
                            <input name="notes" class="form-control" placeholder="">
                        </div>
                    </div>

                </form>

            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-outline btn-default cancel">Cancelar</button>
                <button type="button" class="btn blue save">Guardar</button>
            </div>
        </div>
    </div>
</div>
<!-- END MODAL FREE DAYS -->


<script>
var items = {$items|@json_encode};
{literal}
    function $Ready(){
        MDuesRental.init();
        MDays.init();

        //MDays.open(1, '0,2,3');
        //MVoucher.open(1,'xxx');

        {/literal}

        console.log('can_edit: {$can_edit}');

        {if $can_edit && empty($items)}
            MDuesRental.add();
        {/if}
        {literal}
        //MCar.add(1);
        //MDuesRentalPay.open(items[0]);
    }
{/literal}
</script>

{include file='_footer.tpl' js=[
    'assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js',
    'assets/global/plugins/fancybox/source/jquery.fancybox.pack.js',
    'assets/global/plugins/jquery.form.min.js',
    'views/js/m_voucher.js',
    'views/js/m_dues_rental.js'
]}