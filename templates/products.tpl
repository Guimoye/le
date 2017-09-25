{include file='_header.tpl' css=[
    'assets/global/plugins/jquery-ui-autocomplete/jquery-ui.min.css'
]}

<div class="row">

    <div class="col-md-12">

        <div class="portlet light">

            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject bold uppercase">{$page_title}</span>
                </div>
                <div class="actions">
                    {if $can_products}
                        <span class="btn btn-circle green btn-outline" onclick="MProduct.add();"> <i class="fa fa-plus"></i> Nuevo </span>
                    {/if}
                </div>
            </div>

            <div class="portlet-body">

                <!-- FILTERS -->
                <form class="form-inline" style="margin-bottom:10px">
                    <div class="form-group">
                        <label>Consulta</label><br>
                        <input class="form-control" name="word" placeholder="nombre" value="{$fils.word}"/>
                    </div>
                    <div class="form-group">
                        <label>Área</label><br>
                        <select class="form-control" name="id_area">
                            <option value="">Todos</option>
                            {foreach item=o from=$areas}
                                <option value="{$o.id}" {if $fils.id_area==$o.id}selected{/if}>{$o.name}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Resultados</label><br>
                        <select class="form-control" name="max">
                            {$maxs = [10,20,50,100,200,500,1000,2000,5000,10000]}
                            {foreach item=o from=$maxs}
                                <option {if $fils.max==$o}selected{/if}>{$o}</option>
                            {/foreach}
                        </select>
                    </div>
                    <div class="form-group">
                        <label>&nbsp;</label><br>
                        <a href="?" class="btn grey">Reiniciar</a>
                        <button type="submit" class="btn blue apply">Aplicar</button>
                    </div>
                </form>
                <!-- END FILTERS -->

                {if empty($products)}
                    <div class="alert alert-warning">
                        No se han añadido productos.
                    </div>
                {else}
                    <table class="table table-striped table-bordered table-hover dt-responsive">
                        <thead>
                        <tr>
                            <th width="1%"> # </th>
                            <th> Nombre </th>
                            <th> Area </th>
                            <th> Categoría </th>
                            <th> Unidad de medida </th>
                            <th width="1%"></th>
                        </tr>
                        </thead>
                        <tbody id="pager_content">
                        {foreach key=i item=o from=$products}
                            <tr>
                                <td>{$o.id}</td>
                                <td>{$o.name}</td>
                                <td>{$o.ar_name}</td>
                                <td>{$o.ca_name}</td>
                                <td>{$o.un_name}</td>
                                <td>
                                    {if $can_products}
                                        <span class="btn btn-outline btn-circle dark btn-sm" onclick="MProduct.edit(products[{$i}]);">
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
<div class="modal fade" id="modal_add_area" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-full" style="max-width:900px">
        <div class="modal-content">

            <div class="row">

                <!-- PRODUCT -->
                <div class="col-md-6" style="border-right:2px solid #EEE">

                    <div class="modal-header">
                        <h4 class="modal-title prod">---</h4>
                    </div>
                    <div class="modal-body">
                        <form class="form-horizontal frm_data">
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
                                    <textarea class="form-control" name="description" placeholder="Escribir..."></textarea>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-3 control-label">Notas</label>
                                <div class="col-md-9">
                                    <input type="text" class="form-control" name="notes" placeholder="Escribir...">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-5 control-label">Area de producción</label>
                                <div class="col-md-7">
                                    <select class="form-control" name="id_area"></select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-5 control-label">Categoría</label>
                                <div class="col-md-7">
                                    <select class="form-control" name="id_category"></select>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-5 control-label">Unidad de medida</label>
                                <div class="col-md-7">
                                    <select class="form-control" name="id_unimed"></select>
                                </div>
                            </div>

                            <div class="modal-footer">
                                <button type="button" class="btn red remove pull-left">Eliminar</button>
                                <button type="button" class="btn default cancel" data-dismiss="modal">Cancelar</button>
                                <button type="button" class="btn green save">Guardar</button>
                            </div>

                        </form>
                    </div>

                </div>
                <!-- END PRODUCT -->

                <!-- PROPRES -->
                <div class="col-md-6" style="border-left:2px solid #EEE;margin-left:-2px">

                    <div class="modal-header">
                        <h4 class="modal-title">Presentaciones</h4>
                    </div>
                    <div class="modal-body propres">

                        <div class="tabbable">
                            <ul class="nav nav-tabs"></ul>
                            <div class="tab-content"></div>
                        </div>

                    </div>

                </div>
                <!-- END PROPRES -->

            </div>

        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- MODAL UNIMED -->

<script>
    var products = {$products|@json_encode};
    var unimeds = {$unimeds|@json_encode};
    var storages = {$storages|@json_encode};

    function $Ready(){
        MProduct.init();
        //MProduct.edit(products[0]);
    }
</script>

{include file='_footer.tpl' js=[
    'assets/global/plugins/jquery-ui-autocomplete/jquery-ui.min.js',
    'js/m_product.js'
]}