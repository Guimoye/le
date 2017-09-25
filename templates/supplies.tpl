{include file='_header.tpl'}

<div class="row">

    <div class="col-md-8">

        <div class="portlet light">

            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject bold uppercase">{$page_title}</span>
                </div>
                <div class="actions">
                    {if $can_supplies}
                        <span class="btn btn-circle green btn-outline" onclick="MSupply.add();"> <i class="fa fa-plus"></i> Nuevo </span>
                    {/if}
                </div>
            </div>

            <div class="portlet-body">

                {if empty($supplies)}
                    <div class="alert alert-warning">
                        No se han añadido insumos.
                    </div>
                {else}
                    <table class="table table-bordered table-hover dt-responsive mdl-td">
                        <thead>
                        <tr>
                            <th width="1%"> # </th>
                            <th> Nombre </th>
                            <th> Costo </th>
                            <th width="1%"> Stock </th>
                            <th width="1%"> UM </th>
                            <th width="1%"></th>
                        </tr>
                        </thead>
                        <tbody id="pager_content">
                        {foreach key=i item=o from=$supplies}
                            <tr {if $o.stock <= $o.stock_min}style="background:rgba(255,0,0,.03)"{/if}>
                                <td>{$o.id}</td>
                                <td>{$o.name}</td>
                                <td>{$stg->coin}{$o.cost|string_format:"%.2f"}</td>
                                <td>{$o.stock|string_format:"%.2f"}</td>
                                <td>{$o.un_name}</td>
                                <td>
                                    {if $can_supplies}
                                        <span class="btn btn-outline btn-circle dark btn-sm" onclick="MSupply.edit(supplies[{$i}]);">
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

    </div>

</div>


<!-- MODAL UNIMED -->
<div class="modal fade" id="modal_add_supply" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" style="max-width:500px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">---</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="id" value="">
                    <div class="form-group">
                        <label class="col-md-5 control-label">Nombre</label>
                        <div class="col-md-7">
                            <input type="text" class="form-control" name="name" placeholder="Escribir...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-5 control-label">Unidad de medida</label>
                        <div class="col-md-7">
                            <select class="form-control" name="id_unimed"></select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-5 control-label">Costo por <span class="cost_x">x</span></label>
                        <div class="col-md-7">
                            <input type="number" class="form-control" name="cost" placeholder="0.00">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-5 control-label">Costo máximo por <span class="cost_x">x</span></label>
                        <div class="col-md-7">
                            <input type="number" class="form-control" name="cost_max" placeholder="0.00">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-5 control-label">Stock mínimo</label>
                        <div class="col-md-7">
                            <input type="number" class="form-control" name="stock_min" placeholder="0">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-5 control-label">Tipo de adquisición</label>
                        <div class="col-md-7">
                            <select class="form-control" name="tipo_adq">
                                <option value="0">Gravada</option>
                                <option value="1">Gravada Exportación</option>
                                <option value="2">No Gravada</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group hide">
                        <label class="col-md-5 control-label bold">Stock por almacén</label>
                    </div>
                    <div class="storages hide"></div>
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
    var supplies = {$supplies|@json_encode};

    function $Ready(){
        MSupply.init();
        //MSupply.add();
    }
</script>

{include file='_footer.tpl' js=[
    'js/m_supply.js'
]}