{include file='_header.tpl' css=[
    'assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css'
]}

<div class="portlet light">

    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject font-dark bold uppercase">{$page_title}</span>
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
                    <th> Fecha </th>
                    <th> Alquiler </th>
                    <th> Multa/Dscto </th>
                    <th> Anterior </th>
                    <th> Monto Total </th>
                    <th> Monto Pagado </th>
                    <th> Fecha de pago </th>
                    <th width="1%">Opciones</th>
                </tr>
                </thead>
                <tbody id="pager_content">
                {foreach key=i item=o from=$items}
                    <tr>
                        <td> {$o.id} </td>
                        <td> {$o.date_due} </td>
                        <td> {$stg->coin}{$o.amount_due} </td>
                        <td> {$stg->coin}{$o.amount_penalty} </td>
                        <td> {$stg->coin}{$o.amount_previous} </td>
                        <td> {$stg->coin}{$o.amount_total} </td>
                        <td> {$stg->coin}{$o.amount_paid} </td>
                        <td>
                            {$o.date_paid}
                            {if $o.pay_state == 'paid'}
                                <span {*onclick="MDuesRental.setDueUnpaid({$o.id});"*}
                                      class="btn btn-xs green-jungle">Pagado</span>

                            {elseif $o.pay_state == 'pending'}
                                <span
                                        {if $can_edit}
                                            onclick="MDuesRental.setDuePaid({$o.id},{$o.amount_total});"
                                        {/if}

                                      class="btn btn-xs yellow-crusta">Pendiente</span>

                            {elseif $o.pay_state == 'expired'}
                                <span
                                        {if $can_edit}
                                            onclick="MDuesRental.setDuePaid({$o.id},{$o.amount_total});"
                                        {/if}

                                      class="btn btn-xs red-mint">Vencido</span>

                            {/if}
                        </td>
                        <td class="nowrap">

                            {if $can_edit}
                                <span class="btn btn-outline btn-circle dark btn-sm font-md" onclick="MVoucher.open({$o.id},'{$o.pic_voucher}');">
                                    <i class="fa fa-paperclip"></i>
                                </span>

                                <span class="btn btn-outline btn-circle dark btn-sm font-md" onclick="MDays.open({$o.id}, '{$o.free_days}');">
                                    <i class="fa fa-calendar-o"></i>
                                </span>
                            {/if}

                        </td>
                    </tr>
                {/foreach}


                <tr style="background:#e7ecf1">
                    <td colspan="2"></td>
                    <th>{$stg->coin}{$total_amount_due}</th>
                    <td colspan="6"></td>
                </tr>

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

<!-- MODAL VOUCHER -->
<div id="modal_add_voucher" class="modal fade modal-scroll" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h4 class="modal-title">---</h4>
            </div>
            <div class="modal-body">

                <form class="form-horizontal" action="dues_rental/upload_voucher" method="post">
                    <input type="hidden" name="action" value="upload_voucher">
                    <input type="hidden" name="id" value="">

                    <div class="fileinput fileinput-new" data-provides="fileinput">
                        <span class="btn green btn-file">
                            <span class="fileinput-new"> Elegir imagen... </span>
                            <span class="fileinput-exists"> Cambiar... </span>
                            <input type="file" name="photo">
                        </span>
                        <span class="fileinput-filename"> </span> &nbsp;
                        <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"> </a>
                    </div>

                    <img class="image" src="" style="max-width:100%; margin-top:10px">

                </form>

            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-outline btn-default cancel">Cerrar</button>
                <button type="button" class="btn blue save hide">Generar</button>
            </div>
        </div>
    </div>
</div>
<!-- END MODAL VOUCHER -->

<!-- MODAL VOUCHER -->
<div id="modal_add_days" class="modal fade modal-scroll" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h4 class="modal-title">---</h4>
            </div>
            <div class="modal-body">

                <form class="form-horizontal">
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
                        </tr>
                    </table>

                </form>

            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-outline btn-default cancel">Cancelar</button>
                <button type="button" class="btn blue save">Guardar</button>
            </div>
        </div>
    </div>
</div>
<!-- END MODAL VOUCHER -->


{literal}
<script>
    function $Ready(){
        MDuesRental.init();
        MVoucher.init();
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
    }
</script>
{/literal}

{include file='_footer.tpl' js=[
    'assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js',
    'assets/global/plugins/jquery.form.min.js',
    'views/js/m_dues_rental.js'
]}