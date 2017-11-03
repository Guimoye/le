{include file='_header.tpl' css=[
    'assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css'
]}

<div class="portlet light">

    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject font-dark bold uppercase">{$page_title}</span>
        </div>
        <div class="actions">
            {if $can_edit}
                <span class="btn btn-circle blue" onclick="MLoan.add();"> <i class="fa fa-plus"></i> Registrar </span>
            {/if}
        </div>
    </div>

    <div class="portlet-body">

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
                    <th> Descripción </th>
                    <th width="1%"> Monto </th>
                    <th width="1%"> Fecha de Pago </th>
                    <th width="1%">Opciones</th>
                </tr>
                </thead>
                <tbody id="pager_content">
                {foreach key=i item=o from=$items}
                    <tr>
                        <td> {$o.id} </td>
                        <td class="nowrap"> {$o.date_pay|date_format:"%d-%m-%Y"} </td>
                        <td> {$o.description} </td>
                        <td class="nowrap"> {$stg->coin}{$o.amount} </td>
                        <td class="nowrap">
                            {$o.date_paid|date_format:"%d-%m-%Y"}
                            {if $o.pay_state == 'paid'}
                                <span {*onclick="MLoan.setUnpaid({$o.id});"*}
                                      class="btn btn-xs green-jungle">Pagado</span>

                            {elseif $o.pay_state == 'pending'}
                                <span
                                        {if $can_edit}
                                            onclick="MLoan.setPaid({$o.id});"
                                        {/if}

                                      class="btn btn-xs yellow-crusta">Pendiente</span>

                            {elseif $o.pay_state == 'expired'}
                                <span
                                        {if $can_edit}
                                            onclick="MLoan.setPaid({$o.id});"
                                        {/if}

                                      class="btn btn-xs red-mint">Vencido</span>

                            {/if}
                        </td>
                        <td class="nowrap">

                            {if $can_edit}
                                <span onclick="MLoanDues.open({$o.id});"
                                      class="btn btn-outline btn-circle dark btn-sm font-md">
                                    <i class="fa fa-bars"></i>
                                </span>
                                <!--<span onclick="MLoan.edit(items[{$i}]);"
                                      class="btn btn-outline btn-circle dark btn-sm font-md">
                                    <i class="fa fa-pencil"></i>
                                </span>-->

                                <span onclick="MLoan.remove({$o.id});"
                                      class="btn btn-outline btn-circle dark btn-sm font-md">
                                    <i class="fa fa-trash"></i>
                                </span>
                            {/if}


                        </td>
                    </tr>
                {/foreach}


                <tr style="background:#e7ecf1">
                    <td colspan="3"></td>
                    <th colspan="100%">{$stg->coin}{$total_amount_due}</th>
                </tr>

                </tbody>
            </table>
        {/if}

    </div>

</div>

<!-- MODAL -->
<div id="modal_add_loan" class="modal fade modal-scroll" data-backdrop="static" data-keyboard="false">
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
                        <label class="col-md-3 control-label">Descripción</label>
                        <div class="col-md-8">
                            <input class="form-control" name="description" placeholder="Describir préstamo...">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Número de cuotas</label>
                        <div class="col-md-8">
                            <input class="form-control" name="num_dues" placeholder="Cuotas...">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">TEA</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <input class="form-control" name="tea" placeholder="0">
                                <span class="input-group-addon">%</span>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Monto</label>
                        <div class="col-md-8">
                            <div class="input-group">
                                <span class="input-group-addon">{$stg->coin}</span>
                                <input class="form-control" name="amount" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-3 control-label">Fecha de inicio</label>
                        <div class="col-md-8">
                            <select class="form-control" name="date_pay">
                                <option value="">Elegir...</option>
                                {foreach key=i item=o from=$dates}
                                    <option value="{$o.date_due}">{$o.date_due|date_format:"%d-%m-%Y"}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>

                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn red pull-left remove">Eliminar</button>
                <button type="button" data-dismiss="modal" class="btn btn-outline btn-default cancel">Cancelar</button>
                <button type="button" class="btn blue save">Guardar</button>
            </div>
        </div>
    </div>
</div>
<!-- END MODAL -->

<!-- MODAL CUOTAS DE PRESTAMO -->
<div id="modal_loan_dues" class="modal fade modal-scroll" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Cuotas de préstamo</h4>
            </div>
            <div class="modal-body">

                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th width="1%">#</th>
                        <th>Fecha</th>
                        <th width="1%">Monto</th>
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>

            </div>
            <div class="modal-footer">
                <button type="button" data-dismiss="modal" class="btn btn-default cancel">Cerrar</button>
            </div>
        </div>
    </div>
</div>
<!-- END MODAL CUOTAS DE PRESTAMO -->

<script>

    var items = {$items|@json_encode};

    {literal}
    function $Ready(){
        MLoan.init();
        //MLoan.add();
    }
    {/literal}
</script>

{include file='_footer.tpl' js=[
    'assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js',
    'assets/global/plugins/jquery.form.min.js',
    'views/js/m_loan.js'
]}