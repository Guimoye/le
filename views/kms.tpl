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
                        <span class="btn btn-circle blue" onclick="MKm.add(1);"> <i class="fa fa-plus"></i> Registrar </span>
                    {/if}
                </div>
            </div>

            <div class="portlet-body">

                <!--<div class="date-picker" data-date-format="mm/dd/yyyy"></div>-->

                {if empty($items_1)}
                    <div class="alert alert-warning">
                        No hay datos disponibles.
                    </div>
                {else}
                    <table class="table table-striped table-bordered table-hover dt-responsive mdl-td" style="margin-top:10px">
                        <thead>
                        <tr>
                            <th> Kilómetros </th>
                            <th width="1%"> </th>
                        </tr>
                        </thead>
                        <tbody id="pager_content">
                        {foreach key=i item=o from=$items_1}
                            <tr>
                                <td> <div style="font-size:24px"> {$o.km|number_format} km </div> </td>
                                <td class="nowrap">

                                    {if $can_edit}
                                        <span onclick="MKm.edit(items_1[{$i}]);"
                                              class="btn btn-outline btn-circle dark btn-sm font-md">
                                            <i class="fa fa-pencil"></i>
                                        </span>

                                        <span onclick="MKm.remove({$o.id});"
                                              class="btn btn-outline btn-circle dark btn-sm font-md">
                                            <i class="fa fa-trash"></i>
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

    <div class="col-md-6">

        <div class="portlet light">

            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject font-dark bold uppercase">Mantenimiento de GAS</span>
                </div>
                <div class="actions">
                    {if $can_edit}
                        <span class="btn btn-circle blue" onclick="MKm.add(2);"> <i class="fa fa-plus"></i> Registrar </span>
                    {/if}
                </div>
            </div>

            <div class="portlet-body">

                <!--<div class="date-picker" data-date-format="mm/dd/yyyy"></div>-->

                {if empty($items_2)}
                    <div class="alert alert-warning">
                        No hay datos disponibles.
                    </div>
                {else}
                    <table class="table table-striped table-bordered table-hover dt-responsive mdl-td" style="margin-top:10px">
                        <thead>
                        <tr>
                            <th> Kilómetros </th>
                            <th width="1%"> </th>
                        </tr>
                        </thead>
                        <tbody id="pager_content">
                        {foreach key=i item=o from=$items_2}
                            <tr>
                                <td> <div style="font-size:24px"> {$o.km|number_format} km </div> </td>
                                <td class="nowrap">

                                    {if $can_edit}
                                        <span onclick="MKm.edit(items_2[{$i}]);"
                                              class="btn btn-outline btn-circle dark btn-sm font-md">
                                            <i class="fa fa-pencil"></i>
                                        </span>

                                        <span onclick="MKm.remove({$o.id});"
                                              class="btn btn-outline btn-circle dark btn-sm font-md">
                                            <i class="fa fa-trash"></i>
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
<div id="modal_add_km" class="modal fade modal-scroll" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h4 class="modal-title">---</h4>
            </div>
            <div class="modal-body">

                <form class="form-horizontal">
                    <input type="hidden" name="id" value="">
                    <input type="hidden" name="type" value="{$type}">

                    <div class="form-group">
                        <label class="col-md-4 control-label">Kilómetros</label>
                        <div class="col-md-6">
                            <input class="form-control input-lg" name="km" placeholder="0">
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

    var items_1 = {$items_1|@json_encode};
    var items_2 = {$items_2|@json_encode};

    {literal}
    function $Ready(){
        MKm.init();
        //MKm.add();
    }
    {/literal}
</script>

{include file='_footer.tpl' js=[
    'assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js',
    'assets/global/plugins/jquery.form.min.js',
    'views/js/m_km.js'
]}