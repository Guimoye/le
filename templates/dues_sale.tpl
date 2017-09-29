{include file='_header.tpl' css=[
    'assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css'
]}

<div class="portlet light">

    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject font-dark bold uppercase">{$page_title}</span>
        </div>
        <div class="actions">
            <!--span class="btn btn-circle blue" onclick="MDuesSale.add();"> <i class="fa fa-plus"></i> Registrar </span>-->
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
                    <th> Amortización </th>
                    <th> Interés </th>
                    <th> Seguros </th>
                    <th> Saldo Anterior </th>
                    <th> Moras / Dscto </th>
                    <th> Pago Total </th>
                    <th> Pago Total (IGV) </th>
                    <th width="1%"></th>
                </tr>
                </thead>
                <tbody id="pager_content">
                {foreach key=i item=o from=$items}
                    <tr>
                        <td> {$o.id} </td>
                        <td> {$o.date_due} </td>
                        <td> {$stg->coin}{$o.amount_due|string_format:"%.2f"} </td>
                        <td> {$stg->coin}{$o.amount_interest|string_format:"%.2f"} </td>
                        <td> {$stg->coin}{$o.amount_insurance|string_format:"%.2f"} </td>
                        <td> {$stg->coin}{$o.amount_previous|string_format:"%.2f"} </td>
                        <td> {$stg->coin}{$o.amount_penalty|string_format:"%.2f"} </td>
                        <td> {$stg->coin}{$o.amount_total|string_format:"%.2f"} </td>
                        <td>
                            {$stg->coin}{$o.amount_paid|string_format:"%.2f"}
                            {if $o.pay_state == 'paid'}
                                <span {*onclick="MDuesSale.setDueUnpaid({$o.id});"*}
                                      class="btn btn-xs green-jungle">Pagado</span>

                            {elseif $o.pay_state == 'pending'}
                                <span onclick="MDuesSale.setDuePaid({$o.id},{$o.amount_total});"
                                      class="btn btn-xs yellow-crusta">Pendiente</span>

                            {elseif $o.pay_state == 'expired'}
                                <span onclick="MDuesSale.setDuePaid({$o.id},{$o.amount_total});"
                                      class="btn btn-xs red-mint">Vencido</span>

                            {/if}
                        </td>
                        <td class="nowrap">

							<span onclick="MVoucher.open({$o.id},'{$o.pic_voucher}');"
                                  class="btn btn-outline btn-circle dark btn-sm font-md">
								<i class="fa fa-paperclip"></i>
							</span>

							<span onclick="MEditDuesSale.open({$o.id},{$o.amount_penalty});"
                                  class="btn btn-outline btn-circle dark btn-sm font-md">
								<i class="fa fa-pencil"></i>
							</span>

                        </td>
                    </tr>
                {/foreach}


                <tr style="background:#e7ecf1">
                    <td colspan="2"></td>
                    <th>{$stg->coin}{$total_amount_due}</th>
                    <td colspan="7"></td>
                </tr>

                </tbody>
            </table>
        {/if}

    </div>

</div>

<!-- MODAL -->
<div id="modal_add_dues_sale" class="modal fade modal-scroll" data-backdrop="static" data-keyboard="false">
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
                        <label class="col-md-4 control-label">Interés</label>
                        <div class="col-md-6">
                            <div class="input-group input-group-lg">
                                <span class="input-group-addon">{$stg->coin}</span>
                                <input class="form-control" name="interest" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label">Seguros</label>
                        <div class="col-md-6">
                            <div class="input-group input-group-lg">
                                <span class="input-group-addon">{$stg->coin}</span>
                                <input class="form-control" name="insurance" placeholder="0.00">
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

                <form class="form-horizontal" action="dues_sale.php" method="post">
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
<div id="modal_edit_dues_sale" class="modal fade modal-scroll" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h4 class="modal-title">---</h4>
            </div>
            <div class="modal-body">

                <form class="form-horizontal">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="id" value="">

                    <div class="form-group">
                        <label class="col-md-4 control-label">Multa/Dsct</label>
                        <div class="col-md-6">
                            <div class="input-group input-group-lg">
                                <span class="input-group-addon">{$stg->coin}</span>
                                <input class="form-control" name="amount_penalty" placeholder="0.00">
                            </div>
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
<!-- END MODAL VOUCHER -->


{literal}
<script>
    function $Ready(){
        MDuesSale.init();
        MVoucher.init();
        MEditDuesSale.init();

        //MEditDuesSale.open(1, 0);
        //MVoucher.open(1,'xxx');

        {/literal}
        {if empty($items)}
            MDuesSale.add();
        {/if}
        {literal}
        //MCar.add(1);
    }
</script>
{/literal}

{include file='_footer.tpl' js=[
    'assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js',
    'assets/global/plugins/jquery.form.min.js',
    'js/m_dues_sale.js'
]}