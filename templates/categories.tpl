{include file='_header.tpl' css=[
    'assets/global/plugins/jquery-nestable/jquery.nestable.css',
    'assets/global/plugins/jquery-minicolors/jquery.minicolors.css'
]}

<div class="row">

    <div class="col-md-6">

        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject font-dark bold uppercase">{$page_title}</span>
                </div>
                <div class="actions">
                    {if $can_categories}
                        <a class="btn btn-circle red btn-outline" onclick="MCateg.add();"> <i class="fa fa-plus"></i> </a>
                        <a class="btn btn-circle green btn-outline" onclick="MCateg.reOrder();"> <i class="fa fa-check"></i> Guardar </a>
                    {/if}
                </div>
            </div>

            <div class="portlet-body">

                <div class="dd" id="list">

                    {function mkCategs level=0}
                        <ol class="dd-list">
                            {foreach $data as $m}
                                <li class="dd-item dd3-item" data-id="{$m.id}">
                                    {if $can_categories}
                                        <div class="dd-handle dd3-handle" style="background:{$m.color}"></div>
                                    {/if}
                                    <div class="dd3-content">
                                        {$m.name}
                                        {if $can_categories}
                                            <a class="btn red btn-xs pull-right" onclick="MCateg.edit({$m.id},'{$m.name}','{$m.color}',{$m.sort},{$m.in_deli},{$m.favorite});">
                                                <i class="fa fa-pencil"></i>
                                            </a>
                                        {/if}
                                    </div>
                                    {if !empty($m.sub)}
                                        {mkCategs data=$m.sub level=$level+1}
                                    {/if}
                                </li>
                            {/foreach}
                        </ol>
                    {/function}
                    {mkCategs data=$categories}

                </div>

            </div>
        </div>

    </div>

</div>

<!-- MODAL MENU -->
<div class="modal fade" id="modal_add_category" tabindex="-1" aria-hidden="true">
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
                        <label class="col-md-4 control-label">Descrición</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="name" placeholder="Escribir...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Color</label>
                        <div class="col-md-8">
                            {*<input type="text" name="color" class="form-control" data-control="hue" value="#ff6161">*}
                            <input type="hidden" name="color" value="#db913d">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Número de orden</label>
                        <div class="col-md-4">
                            <input type="number" class="form-control" name="sort" placeholder="Número...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label"></label>
                        <div class="col-md-8">
                            <label><input type="checkbox" name="in_deli"> Mostrar en delivery</label>
                            <br><label><input type="checkbox" name="favorite"> Favorito</label>
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

        MCateg.init();
        //MCateg.add();

    }

</script>

{include file='_footer.tpl' js=[
    'assets/global/plugins/jquery-nestable/jquery.nestable.js',
    'assets/global/plugins/jquery-minicolors/jquery.minicolors.min.js',
    'js/m_categories.js'
]}