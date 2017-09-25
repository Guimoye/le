{include file='_header.tpl'}

<div class="row">

    <div class="col-md-12">

        <div class="portlet light">

            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject bold uppercase">{$page_title}</span>
                </div>
                <div class="actions">
                    {if $can_promos}
                        <span class="btn btn-circle green btn-outline" onclick="MPromo.add();"> <i class="fa fa-plus"></i> Nuevo </span>
                    {/if}
                </div>
            </div>

            <div class="portlet-body">

                {if empty($promos)}
                    <div class="alert alert-warning">
                        Todavía no se han creado ofertas.
                    </div>
                {else}
                    <table class="table table-striped table-bordered table-hover dt-responsive">
                        <thead>
                        <tr>
                            <th width="1%"> # </th>
                            <th width="1%"> Código </th>
                            <th width="1%"> PCT </th>
                            <th width="1%"> Valor&nbsp;max. </th>
                            <th> Descripción </th>
                            <th width="155px"> Fecha de expiración </th>
                            <th width="155px"> Fecha de creación </th>
                            <th width="1%"></th>
                        </tr>
                        </thead>
                        <tbody id="pager_content">
                        {foreach key=i item=o from=$promos}
                            <tr>
                                <td>{$o.id}</td>
                                <td><span class="label label-primary bold" style="background:black"> {$o.code} </span></td>
                                <td class="ctr">{$o.percent}%</td>
                                <td class="ctr">{$stg->coin}{$o.max_value}</td>
                                <td>{$o.name}</td>
                                <td>
                                    {if $o.date_end}
                                        {$o.date_end|date_format:"%d-%m-%Y %I:%M %p"}
                                    {else}
                                        <span class="badge badge-default badge-roundless"> No expira </span>
                                    {/if}
                                </td>
                                <td>{$o.date_added|date_format:"%d-%m-%Y %I:%M %p"}</td>
                                <td>
                                    {if $can_promos}
                                        <span class="btn btn-outline btn-circle dark btn-sm" onclick="MPromo.edit(promos[{$i}]);">
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
<div class="modal fade" id="modal_add_promo" tabindex="-1" aria-hidden="true">
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
                        <label class="col-md-4 control-label">Código</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="code" placeholder="Código promocional">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Porcentaje</label>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="number" name="percent" class="form-control" placeholder="0">
                                <span class="input-group-addon">%</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Valor máximo</label>
                        <div class="col-md-4">
                            <div class="input-group">
                                <input type="number" name="max_value" class="form-control" placeholder="0">
                                <span class="input-group-addon">{$stg->coin}</span>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Descripción</label>
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="name" placeholder="(opcional)">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-4 control-label">Finaliza</label>
                        <div class="col-md-8">

                            <div class="input-group">
                                <input type="date" name="date" class="form-control tooltips" title="Elegir Fecha" style="width:55%">
                                <input type="time" name="time" class="form-control tooltips" title="Elegir Hora" style="width:45%;border-left:0">
                            </div>

                            <span class="help-block"> Fecha de expiración (opcional) </span>

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
    var promos = {$promos|@json_encode};

    function $Ready(){
        MPromo.init();
        //MPromo.add();
    }
</script>

{include file='_footer.tpl' js=[
    'js/m_promo.js'
]}