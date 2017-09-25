{include file='_header.tpl'}

<div class="row">

    <div class="col-md-6">

        <div class="portlet light">

            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject bold uppercase">{$page_title}</span>
                </div>
                <div class="actions">
                    {if $can_turns}
                        <span class="btn btn-circle green btn-outline" onclick="MTurn.add();"> <i class="fa fa-plus"></i> Nuevo </span>
                    {/if}
                </div>
            </div>

            <div class="portlet-body">

                {if empty($turns)}
                    <div class="alert alert-warning">
                        Todavía no se han creado turnos.
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
                        {foreach key=i item=o from=$turns}
                            <tr>
                                <td>{$o.id}</td>
                                <td>{$o.name}</td>
                                <td>
                                    {if $can_turns}
                                        <span class="btn btn-outline btn-circle dark btn-sm" onclick="MTurn.edit(turns[{$i}]);">
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
<div class="modal fade" id="modal_add_turn" tabindex="-1" aria-hidden="true">
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

<script>
    var turns = {$turns|@json_encode};

    function $Ready(){
        MTurn.init();
        //MTurn.add();
    }
</script>

{include file='_footer.tpl' js=[
    'js/m_turn.js'
]}