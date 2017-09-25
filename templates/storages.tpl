{include file='_header.tpl'}

<div class="row">

    <div class="col-md-6">

        <div class="portlet light">

            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject bold uppercase">{$page_title}</span>
                </div>
                <div class="actions">
                    {if $can_storages}
                        <span class="btn btn-circle green btn-outline" onclick="MStorage.add();"> <i class="fa fa-plus"></i> Nuevo </span>
                    {/if}
                </div>
            </div>

            <div class="portlet-body">

                {if empty($storages)}
                    <div class="alert alert-warning">
                        Todavía no se han creado almacenes para suministros.
                    </div>
                {else}
                    <table class="table table-striped table-bordered table-hover dt-responsive mdl-td">
                        <thead>
                        <tr>
                            <th width="1%"> # </th>
                            <th> Nombre </th>
                            <th width="155px"> Fecha de creación </th>
                            <th width="1%"></th>
                        </tr>
                        </thead>
                        <tbody id="pager_content">
                        {foreach key=i item=o from=$storages}
                            <tr>
                                <td>{$o.id}</td>
                                <td>{$o.name}</td>
                                <td>{$o.date_added|date_format:"%d-%m-%Y %I:%M %p"}</td>
                                <td>
                                    {if $can_storages}
                                        <span class="btn btn-outline btn-circle dark btn-sm btn-block" onclick="MStorage.edit(storages[{$i}]);">
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
<div class="modal fade" id="modal_add_branch" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" style="max-width:450px">
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
                        <label class="col-md-4 control-label">Nombre</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="name" placeholder="Escribir...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Área de producción</label>
                        <div class="col-md-8">
                            <select name="id_area" class="form-control">
                                <option value="">Elegir...</option>
                                {foreach item=o from=$areas}
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
<!-- MODAL UNIMED -->

<script>
    var storages = {$storages|@json_encode};

    function $Ready(){
        MStorage.init();
        //MStorage.add();
    }
</script>

{include file='_footer.tpl' js=[
    'js/m_storage.js'
]}