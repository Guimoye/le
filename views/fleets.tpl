{include file='_header.tpl' css=[
    'assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css'
]}
<div class="row">

    <div class="col-md-6">

        <div class="portlet light">

            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject font-dark bold uppercase">{$page_title}</span>
                </div>
                <div class="actions">
                    {if $can_edit}
                        <span class="btn btn-circle blue" onclick="MFleet.add();"> <i class="fa fa-plus"></i> Registrar </span>
                    {/if}
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
                            <th> Nombre </th>
                            <th width="1%"> </th>
                        </tr>
                        </thead>
                        <tbody id="pager_content">
                        {foreach key=i item=o from=$items}
                            <tr>
                                <td> {$o.name} </td>
                                <td class="nowrap">

                                    {if $can_edit}
                                        <span onclick="MFleet.edit(items[{$i}]);"
                                              class="btn btn-outline btn-circle dark btn-sm">
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

<!-- MODAL -->
<div id="modal_add_fleet" class="modal fade modal-scroll" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h4 class="modal-title">---</h4>
            </div>
            <div class="modal-body">

                <form class="form-horizontal">
                    <input type="hidden" name="id" value="">

                    <div class="form-group">
                        <label class="col-md-4 control-label">Nombre</label>
                        <div class="col-md-6">
                            <input class="form-control" name="name" placeholder="Nombre de la flota">
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
        MFleet.init();
        //MFleet.add();
    }
    {/literal}
</script>

{include file='_footer.tpl' js=[
    'assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js',
    'assets/global/plugins/jquery.form.min.js',
    'views/js/m_fleet.js'
]}