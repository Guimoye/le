{include file='_header.tpl'}

<div class="row">

    <div class="col-md-6">

        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject font-dark bold uppercase">{$page_title}</span>
                </div>
                <div class="actions">
                    {if $can_edit}
                        <a class="btn btn-circle red btn-outline" onclick="MLevel.add();"> <i class="fa fa-plus"></i> </a>
                    {/if}
                </div>
            </div>

            <div class="portlet-body">

                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <td width="1%">#</td>
                        <td> Nombre </td>
                        <td width="155px">Fecha de creación</td>
                        <td width="1%"></td>
                    </tr>
                    </thead>
                    <tbody>
                        {foreach item=m from=$levels}
                            <tr>
                                <td width="1%">{$m->id}</td>
                                <td>{$m->name}</td>
                                <td>{$m->date_added|date_format:"%d-%m-%Y %I:%M %p"}</td>
                                <td width="1%">
                                    {if $can_edit}
                                        <span class="btn btn-outline btn-circle dark btn-sm" onclick="MLevel.edit(levels[{$m->id}]);">
                                            <i class="fa fa-pencil"></i>
                                        </span>
                                    {/if}
                                </td>
                            </tr>
                        {/foreach}
                    </tbody>
                </table>

            </div>
        </div>

    </div>

</div>

<!-- MODAL -->
<div class="modal fade" id="modal_add_level" tabindex="-1" role="basic" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">---</h4>
            </div>
            <div class="modal-body">

                <form class="form-horizontal">
                    <input type="hidden" name="action" value="add_level">
                    <input type="hidden" name="id" value="">
                    <div class="form-group">
                        <label class="col-md-2 control-label">Nombre</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="name" placeholder="Escribir...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2 control-label">Permisos</label>
                        <div class="col-md-10">
                            <table class="table table-bordered">
                                <thead>
                                <tr>
                                    <th rowspan="2" style="vertical-align:middle">Modulo</th>
                                    <th width="1%"> Leer </th>
                                    <th width="1%"> Editar </th>
                                    <th rowspan="2" class="ctr" style="vertical-align:middle"><i class="fa fa-home"></i></th>
                                    <th rowspan="2" class="ctr" style="vertical-align:middle"><i class="fa fa-hand-o-up"></i></th>
                                </tr>
                                <tr>
                                    <th class="ctr"><input type="checkbox" class="tooltips see" title="Marcar&nbsp;/&nbsp;desmarcar&nbsp;todo"></th>
                                    <th class="ctr"><input type="checkbox" class="tooltips edit" title="Marcar&nbsp;/&nbsp;desmarcar&nbsp;todo"></th>
                                </tr>
                                </thead>
                                <tbody class="levels">


                                {function mkMenu3 level=0}
                                    {foreach $data as $m}
                                        <tr class="menu {if !empty($m.sub)}active{/if}">
                                            <td>
                                                {'&nbsp;&nbsp;&nbsp;&nbsp;'|str_repeat:$level}
                                                <i class="{$m.icon}"></i> {$m.name}
                                            </td>
                                            <td class="ctr">
                                                <input type="checkbox" name="see[]" value="{$m.id}" class="see id_{$m.id}" data-id="{$m.id}">
                                            </td>
                                            <td class="ctr">
                                                {if empty($m.sub)}
                                                    <input type="checkbox" name="edit[]" value="{$m.id}" class="edit id_{$m.id}" data-id="{$m.id}">
                                                {/if}
                                            </td>
                                            <td class="ctr">
                                                {if empty($m.sub)}
                                                    <input type="radio" name="home" value="{$m.id}" class="home id_{$m.id}">
                                                {/if}
                                            </td>
                                            <td class="ctr">
                                                {if empty($m.sub)}
                                                    <input type="checkbox" name="shortcut[]" value="{$m.id}" class="shortcut id_{$m.id}">
                                                {/if}
                                            </td>
                                        </tr>
                                        {if !empty($m.sub)}
                                            {mkMenu3 data=$m.sub level=$level+1}
                                        {/if}
                                    {/foreach}
                                {/function}
                                {mkMenu3 data=$menu_all}

                                </tbody>
                            </table>
                        </div>
                    </div>
                </form>

            </div>
            <div class="modal-footer">
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
    var levels = {$levels|@json_encode};

    function $Ready(){

        MLevel.init();
        //MLevel.edit(levels[1]);

    }

</script>

{include file='_footer.tpl' js=[
    'views/js/m_level.js'
]}