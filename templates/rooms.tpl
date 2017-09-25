{include file='_header.tpl'}

<style>

    .room_panel_edit{ margin-top:4px; margin-right:4px}

</style>

<div class="portlet light" style="max-width:502px">

    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject bold uppercase">{$page_title}</span>
        </div>
        <div class="actions">
            {if $can_rooms}
                <span class="btn btn-circle green btn-outline" onclick="MRoom.add();"> <i class="fa fa-plus"></i> Nuevo </span>
            {/if}
        </div>
    </div>

    <div class="portlet-body">

        {if empty($rooms)}
            <div class="alert alert-warning">
                No se han creado salas todavía.
            </div>
        {else}
            <div class="panel-group accordion" id="accordion1">

                {foreach key=i item=o from=$rooms}
                    <div class="panel panel-default room" id="room_{$o.id}" data-id="{$o.id}">
                        <div class="panel-heading">
                            <div class="btn-group pull-right room_panel_edit">
                                {if $can_rooms}
                                    <a href="#" class="btn btn-default btn-sm tooltips" title="Editar&nbsp;sala" onclick="MRoom.edit(rooms[{$i}]);">
                                        <div class="fa fa-pencil"></div>
                                    </a>
                                {/if}
                            </div>
                            <h4 class="panel-title">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion1" href="#collapse_{$i}"> {$o.name} </a>
                            </h4>
                        </div>
                        <div id="collapse_{$i}" class="panel-collapse {if isset($smarty.cookies.id_room) && $smarty.cookies.id_room == $o.id}in{else}collapse{/if}">
                            <div class="panel-body">
                                {if !empty($o.description)}
                                    <p> {$o.description} </p>
                                {/if}
                                <h5 class="bold">Mesas:</h5>
                                <div class="tables clearfix">

                                    {foreach key=x item=t from=$o.tables}
                                        <div class="item" id="table_{$t.id}"

                                            {if $can_rooms}
                                                onclick="MTable.edit(rooms[{$i}].tables[{$x}]);"
                                            {/if}
                                        >
                                            <span>{$t.name}</span>
                                        </div>
                                    {/foreach}

                                    {if $can_rooms}
                                        <div class="item tooltips add" title="Agregar mesa" onclick="MTable.add({$o.id});">
                                            <span><i class="fa fa-plus"></i></span>
                                        </div>
                                    {/if}

                                </div>

                            </div>
                        </div>
                    </div>
                {/foreach}

            </div>
        {/if}

    </div>

</div>


<!-- MODAL -->
<div class="modal fade" id="modal_add_room" tabindex="-1" aria-hidden="true">
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
                    <div class="form-group">
                        <label class="col-md-3 control-label">Descripción</label>
                        <div class="col-md-9">
                            <textarea class="form-control" name="description" placeholder="(opcional)"></textarea>
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
<!-- MODAL -->

<!-- MODAL -->
<div class="modal fade" id="modal_add_table" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" style="max-width:450px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">---</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <input type="hidden" name="action" value="add_table">
                    <input type="hidden" name="id" value="">
                    <input type="hidden" name="id_room" value="">
                    <div class="form-group">
                        <label class="col-md-3 control-label">Nombre</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="name" placeholder="Opcional, se agregará por defecto: Mxx">
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
<!-- MODAL -->

<script>
    var rooms = {$rooms|@json_encode};

    function $Ready(){
        MRoom.init();
        //MRoom.add();
        MTable.init();
        //MTable.add();
    }
</script>

{include file='_footer.tpl' js=[
    'assets/global/plugins/js.cookie.min.js',
    'js/m_room.js'
]}