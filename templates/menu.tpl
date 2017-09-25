{include file='_header.tpl' css=[
    'assets/global/plugins/jquery-nestable/jquery.nestable.css'
]}

<div class="row">

    <div class="col-md-6">

        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject font-dark bold uppercase">{$page_title}</span>
                </div>
                <div class="actions">
                    <a class="btn btn-circle red btn-outline" onclick="MMenu.add();"> <i class="fa fa-plus"></i> </a>
                    <a class="btn btn-circle green btn-outline" onclick="MMenu.reOrder();"> <i class="fa fa-check"></i> Guardar </a>
                </div>
            </div>

            <div class="portlet-body">

                <div class="dd" id="list">

                    {function mkMenu2 level=0}
                        <ol class="dd-list">
                            {foreach $data as $m}
                                <li class="dd-item dd3-item" data-id="{$m.id}">
                                    <div class="dd-handle dd3-handle"></div>
                                    <div class="dd3-content">
                                        {$m.name}
                                        <a class="btn red btn-xs pull-right" onclick="MMenu.edit({$m.id},'{$m.name}','{$m.url}','{$m.icon}',{$m.root});">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                    </div>
                                    {if !empty($m.sub)}
                                        {mkMenu2 data=$m.sub level=$level+1}
                                    {/if}
                                </li>
                            {/foreach}
                        </ol>
                    {/function}
                    {mkMenu2 data=$menu_all}

                </div>

            </div>
        </div>

    </div>

</div>

<!-- MODAL MENU -->
<div class="modal fade" id="modal_add_menu" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" style="max-width:350px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">---</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal">
                    <input type="hidden" name="action" value="add_menu">
                    <input type="hidden" name="id" value="">
                    <div class="form-group">
                        <label class="col-md-3 control-label">Nombre</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="name" placeholder="Escribir...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Url</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="url" placeholder="Escribir...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Icono</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="icon" placeholder="Escribir...">
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
<!-- MODAL MENU -->

<script>

    function $Ready(){

        MMenu.init();
        //MMenu.add();

    }

</script>

{include file='_footer.tpl' js=[
    'assets/global/plugins/jquery-nestable/jquery.nestable.js',
    'js/m_menu.js'
]}