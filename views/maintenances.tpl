{include file='_header.tpl' css=[
    'assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css'
]}

<div class="portlet light">

    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject font-dark bold uppercase">{$page_title}</span>
        </div>
        <div class="actions">
            <!--<span class="btn btn-circle blue" onclick="MMaintenance.add();"> <i class="fa fa-plus"></i> Registrar </span>-->
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
                    <th> Mantenimiento </th>
                    <th> Fecha de mantenimiento </th>
                    <th> Kilómetros recorridos </th>
                    <th> Costo </th>
                    <th> Acumulado </th>
                    <th> Monto a pagar </th>
                    <th width="1%"></th>
                    <!--<th width="1%">Opciones</th>-->
                </tr>
                </thead>
                <tbody id="pager_content">
                {foreach key=i item=o from=$items}
                    <tr>
                        <td> {$o.id} </td>
                        <td> {$o.kms|number_format} km </td>
                        <td> {$o.date_item} </td>
                        <td> {$o.kms_daily|string_format:"%.1f"} km diarios </td>
                        <td> {$stg->coin}{$o.amount} </td>
                        <td> {$stg->coin}{$o.amount_stored} </td>
                        <td> {$stg->coin}{$o.amount_total} </td>
                        <td>
                            {if $o.pay_state == 'paid'}
                                <span {*onclick="MMaintenance.setUnpaid({$o.id});"*}
                                      class="btn btn-xs green-jungle">Pagado</span>

                            {elseif $o.pay_state == 'pending'}
                                <span
                                        {if $can_edit}
                                            onclick="MMaintenance.setPaid({$o.id});"
                                        {/if}

                                      class="btn btn-xs yellow-crusta">Pendiente</span>

                            {elseif $o.pay_state == 'expired'}
                                <span
                                        {if $can_edit}
                                            onclick="MMaintenance.setPaid({$o.id});"
                                        {/if}

                                      class="btn btn-xs red-mint">Vencido</span>

                            {/if}
                        </td>
                        <!--<td class="nowrap">

							<span onclick="MMaintenance.edit(items[{$i}]);"
                                  class="btn btn-outline btn-circle dark btn-sm font-md">
								<i class="fa fa-pencil"></i>
							</span>

							<span onclick="MMaintenance.remove({$o.id});"
                                  class="btn btn-outline btn-circle dark btn-sm font-md">
								<i class="fa fa-trash"></i>
							</span>

                        </td>-->
                    </tr>
                {/foreach}

                </tbody>
            </table>
        {/if}

    </div>

</div>

<!-- MODAL -->
<div id="modal_add_maintenance" class="modal fade modal-scroll" data-backdrop="static" data-keyboard="false">
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
                        <label class="col-md-4 control-label">Kilómetros</label>
                        <div class="col-md-7">
                            <input class="form-control" name="kms" placeholder="Escribir...">
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label">Costo</label>
                        <div class="col-md-7">
                            <div class="input-group">
                                <span class="input-group-addon">{$stg->coin}</span>
                                <input class="form-control" name="amount" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label">Acumulado</label>
                        <div class="col-md-7">
                            <div class="input-group">
                                <span class="input-group-addon">{$stg->coin}</span>
                                <input class="form-control" name="amount_stored" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label">Fecha de mantenimiento</label>
                        <div class="col-md-7">
                            <input class="form-control" name="date_item" type="date">
                        </div>
                    </div>

                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn red pull-left remove">Eliminar</button>
                <button type="button" data-dismiss="modal" class="btn btn-outline btn-default cancel hide">Cancelar</button>
                <button type="button" class="btn blue save">Guardar</button>
            </div>
        </div>
    </div>
</div>
<!-- END MODAL -->

<script>

    var items = {$items|@json_encode};

    {literal}
    function $Ready(){
        MMaintenance.init();

        {/literal}
        {if $can_edit && empty($items)}
            MMaintenance.add();
        {/if}
        {literal}

    }
    {/literal}
</script>

{include file='_footer.tpl' js=[
    'assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js',
    'assets/global/plugins/jquery.form.min.js',
    'views/js/m_maintenance.js'
]}