{include file='_header.tpl'}

<div class="portlet light">

    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject bold uppercase">{$page_title}</span>
        </div>
        <div class="actions">
            {if $can_expenses}
                <span class="btn btn-circle green btn-outline" onclick="MExpense.add();"> <i class="fa fa-plus"></i> Nuevo </span>
            {/if}
        </div>
    </div>

    <div class="portlet-body">

        {if empty($expenses)}
            <div class="alert alert-warning">
                Las sucursales aún no se han establecido.
            </div>
        {else}
            <table class="table table-striped table-bordered table-hover dt-responsive mdl-td">
                <thead>
                <tr>
                    <th width="1%"> # </th>
                    <th> Notas </th>
                    <th width="1%"> Moneda </th>
                    <th> Monto </th>
                    <th width="155px"> Fecha de registro </th>
                    <th width="1%"></th>
                </tr>
                </thead>
                <tbody id="pager_content">
                {foreach key=i item=o from=$expenses}
                    <tr>
                        <td>{$o.id}</td>
                        <td>{$o.notes}</td>
                        <td>
                            {if $o.coin==1}DOLARES{else}SOLES{/if}
                        </td>
                        <td>{$stg->coin}{$o.total|string_format:"%.2f"}</td>
                        <td>{$o.date_added|date_format:"%d-%m-%Y %I:%M %p"}</td>
                        <td>
                            {if $can_expenses}
                                <span class="btn btn-outline btn-circle dark btn-sm" onclick="MExpense.edit(expenses[{$i}]);">
                                    <i class="fa fa-pencil"></i>
                                </span>
                            {/if}
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        {/if}

    </div>

</div>


<!-- MODAL UNIMED -->
<div class="modal fade" id="modal_add_expense" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" style="max-width:450px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">---</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <input type="hidden" name="action" value="register_expense">
                    <input type="hidden" name="id" value="">
                    <div class="form-group">
                        <label class="col-md-3 control-label">Notas</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="notes" placeholder="Escribir...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Monto</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="total" placeholder="Escribir...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Moneda</label>
                        <div class="col-md-9">
                            <select name="coin" class="form-control">
                                <option value="0">SOLES</option>
                                <option value="1">DOLARES</option>
                            </select>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn red remove pull-left">Eliminar</button>
                <button type="button" class="btn default cancel" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn green save">Guardar</button>
            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- MODAL UNIMED -->

<script>
    var expenses = {$expenses|@json_encode};

    function $Ready(){
        MExpense.init();
        //MExpense.add();
    }
</script>

{include file='_footer.tpl' js=[
    'js/m_expense.js'
]}