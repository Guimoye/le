{include file='_header.tpl'}

<div class="row">

    <div class="col-md-5">

        <div class="portlet light">

            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject bold uppercase">{$page_title}</span>
                </div>
                <div class="actions">
                    {if $can_unimeds}
                        <span class="btn btn-circle green btn-outline" onclick="MUnimed.add();"> <i class="fa fa-plus"></i> Nuevo </span>
                    {/if}
                </div>
            </div>

            <div class="portlet-body">

                {if empty($unimeds)}
                    <div class="alert alert-warning">
                        No se han registrado unidades de medida
                    </div>
                {else}
                    <table class="table table-striped table-bordered table-hover dt-responsive">
                        <thead>
                        <tr>
                            <th width="1%"> # </th>
                            <th> Nombre </th>
                            <th width="1%"></th>
                        </tr>
                        </thead>
                        <tbody id="pager_content">
                        {foreach key=i item=o from=$unimeds}
                            <tr>
                                <td>{$o.id}</td>
                                <td>{$o.name}</td>
                                <td>
                                    {if $can_unimeds}
                                        <span class="btn btn-outline btn-circle dark btn-sm" onclick="MUnimed.edit(unimeds[{$i}]);">
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

    <div class="col-md-7">

        <div class="portlet light">

            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject bold uppercase">Relación de unidades de medida</span>
                </div>
                <div class="actions">
                    {if $can_unimeds}
                        <span class="btn btn-circle green btn-outline" onclick="MUnimedRel.add();"> <i class="fa fa-plus"></i> Nuevo </span>
                    {/if}
                </div>
            </div>

            <div class="portlet-body">

                {if empty($unimeds_rel)}
                    <div class="alert alert-warning">
                        No hay relación de unidades de medida
                    </div>
                {else}
                    <table class="table table-striped table-bordered table-hover dt-responsive">
                        <thead>
                        <tr>
                            <th width="1%"> # </th>
                            <th> Unidad real </th>
                            <th> Unidad equivalente </th>
                            <th> Cantidad </th>
                            <th width="1%"></th>
                        </tr>
                        </thead>
                        <tbody id="pager_content">
                        {foreach key=i item=o from=$unimeds_rel}
                            <tr>
                                <td>{$o.id}</td>
                                <td>{$o.uno_name}</td>
                                <td>{$o.und_name}</td>
                                <td>{$o.quantity} ({$o.und_name})</td>
                                <td>
                                    {if $can_unimeds}
                                        <span class="btn btn-outline btn-circle dark btn-sm" onclick="MUnimedRel.edit(unimeds_rel[{$i}]);">
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
<div class="modal fade" id="modal_add_unimed" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" style="max-width:400px">
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
                        <label class="col-md-3 control-label">Nombre</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="name" placeholder="Escribir...">
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

<!-- MODAL UNIMED REL -->
<div class="modal fade" id="modal_add_unimed_rel" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" style="max-width:400px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">---</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <input type="hidden" name="action" value="add_rel">
                    <input type="hidden" name="id" value="">
                    <div class="form-group">
                        <label class="col-md-5 control-label">Unidad Real</label>
                        <div class="col-md-7">
                            <select class="form-control" name="id_unimed_org">
                                {foreach key=i item=o from=$unimeds}
                                    <option value="{$o.id}">{$o.name}</option>
                                {/foreach}
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-5 control-label">Cantidad</label>
                        <div class="col-md-7">
                            <input type="number" class="form-control" name="quantity" placeholder="Ej.: 1000">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-5 control-label">Unidad equivalente</label>
                        <div class="col-md-7">
                            <select class="form-control" name="id_unimed_dst">
                                {foreach key=i item=o from=$unimeds}
                                    <option value="{$o.id}">{$o.name}</option>
                                {/foreach}
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
<!-- MODAL UNIMED REL -->

<script>
    var unimeds = {$unimeds|@json_encode};
    var unimeds_rel = {$unimeds_rel|@json_encode};

    function $Ready(){
        MUnimed.init();
        //MUnimed.add();
        MUnimedRel.init();
        //MUnimedRel.add();
    }
</script>

{include file='_footer.tpl' js=[
    'js/m_unimed.js'
]}