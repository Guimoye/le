{include file='_header.tpl' css=[
    'assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css'
]}

<div class="portlet light">

    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject font-dark bold uppercase">{$page_title}</span>
        </div>
        <div class="actions">
            <span class="btn btn-circle blue" onclick="MObligation.add();"> <i class="fa fa-plus"></i> Registrar </span>
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
                    <th> Descripción </th>
                    <th> Fecha </th>
                    <th> Monto </th>
                    <th width="1%">Opciones</th>
                </tr>
                </thead>
                <tbody id="pager_content">
                {foreach key=i item=o from=$items}
                    <tr>
                        <td> {$o.id} </td>
                        <td> {$o.description} </td>
                        <td> {$o.date_end} </td>
                        <td> {$stg->coin}{$o.amount} </td>
                        <td class="nowrap">

							<span onclick="MObligation.edit(items[{$i}]);"
                                  class="btn btn-outline btn-circle dark btn-sm font-md">
								<i class="fa fa-pencil"></i>
							</span>

							<span onclick="MObligation.remove({$o.id});"
                                  class="btn btn-outline btn-circle dark btn-sm font-md">
								<i class="fa fa-trash"></i>
							</span>

                        </td>
                    </tr>
                {/foreach}


                <tr style="background:#e7ecf1">
                    <td colspan="3"></td>
                    <th>{$stg->coin}{$total_amount_due}</th>
                    <td colspan="5"></td>
                </tr>

                </tbody>
            </table>
        {/if}

    </div>

</div>

<!-- MODAL -->
<div id="modal_add_obligation" class="modal fade modal-scroll" data-backdrop="static" data-keyboard="false">
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
                            <input class="form-control" name="description" placeholder="Descripción...">
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
                        <label class="col-md-3 control-label">Fecha</label>
                        <div class="col-md-8">
                            <input class="form-control" name="date_end" type="date">
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

<script>

    var items = {$items|@json_encode};

    {literal}
    function $Ready(){
        MObligation.init();
        //MObligation.add();
    }
    {/literal}
</script>

{include file='_footer.tpl' js=[
    'assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js',
    'assets/global/plugins/jquery.form.min.js',
    'js/m_obligation.js'
]}