{include file='_header.tpl'}

<div class="portlet light">

    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject bold uppercase">{$page_title}</span>
        </div>
        <div class="actions">
            <a class="btn btn-circle green-jungle btn-outline" onclick="Buy.goExport();">
                <i class="fa fa-file-excel-o"></i> Exportar
            </a>
            {if $can_purchases}
                <span class="btn btn-circle green btn-outline" onclick="Buy.add();"> <i class="fa fa-plus"></i> Nueva compra </span>
            {/if}
        </div>
    </div>

    <div class="portlet-body">

        <!-- FILTERS -->
        <form class="form-inline" id="filters" style="margin-bottom:10px">
            <div class="form-group">
                <label>Rango de fechas</label><br>
                <div class="input-group input-large date-picker input-daterange" data-date-format="yyyy-mm-dd" data-date-end-date="+0d">
                    <input type="date" class="form-control" name="date_from" value="{$fs->date_from}">
                    <span class="input-group-addon"> to </span>
                    <input type="date" class="form-control" name="date_to" value="{$fs->date_to}">
                </div>
            </div>
            <div class="form-group">
                <label>Proveedor</label><br>
                <select class="form-control" name="id_provider">
                    <option value="">Todos</option>
                    {foreach item=o from=$providers}
                        <option value="{$o.id}" {if $fs->id_provider==$o.id}selected{/if}>{$o.name}</option>
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

        {if empty($purchases)}
            <div class="alert alert-warning">
                No se han realizado compras aún.
            </div>
        {else}
            <table class="table table-striped table-bordered table-hover dt-responsive">
                <thead>
                <tr>
                    <th width="1%"> #</th>
                    <th width="155px"> Fecha de registro</th>
                    <th> Almacén </th>
                    <th> Proveedor</th>
                    <th width="1%"> Items</th>
                    <th> Total</th>
                    <th width="1%"> Doc.</th>
                    <th width="1%"> N° Doc.</th>
                    <th width="1%"></th>
                </tr>
                </thead>
                <tbody id="pager_content">
                {foreach key=i item=o from=$purchases}
                    <tr>
                        <td>{$o.id}</td>
                        <td>{$o.date_added|date_format:"%d-%m-%Y %I:%M %p"}</td>
                        <td>{$o.st_name}</td>
                        <td>{$o.pr_name}</td>
                        <td>{$o.total_items}</td>
                        <td>{$stg->coin}{$o.total|string_format:"%.2f"}</td>
                        <td>{$o.pf_name}</td>
                        <td>{$o.num_doc}</td>
                        <td>
                            <span class="btn btn-outline btn-circle dark btn-sm tooltips" title="Detalle de compra"
                                  onclick="Buy.show({$o.id});">
                                <i class="fa fa-bars"></i>
                            </span>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        {/if}

    </div>

</div>


<!-- MODAL BUY -->
<div class="modal fade" id="modal_add_buy" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Comprar</h4>
            </div>
            <div class="modal-body">

                <div class="row">

                    <div class="col-md-7">

                        <div class="panel panel-default">
                            <div class="panel-heading"> Insumo</div>
                            <div class="panel-body">
                                <form class="form_supply clearfix" onsubmit="return false;">
                                    <input type="hidden" name="id">

                                    <div class="form-group">
                                        <div class="input-group">
                                            <input type="text" name="name" class="form-control" placeholder="Sin elegir" readonly>
                                            <span class="input-group-btn" style="margin:210px"></span>
                                            <span class="input-group-btn">
                                                <button class="btn btn-default btn-circle" onclick="CSupply.choose();">Elegir</button>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="form-inline">
                                        <div class="form-group">
                                            <label>Cantidad</label><br>
                                            <div class="input-group">
                                                <input type="text" name="quantity" class="form-control" style="max-width:70px">
                                                <span class="input-group-addon unimed">---</span>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Precio x <span class="unimed">---</span></label><br>
                                            <div class="input-group">
                                                <span class="input-group-addon">{$stg->coin}</span>
                                                <input type="text" name="price" class="form-control" style="max-width:80px">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Stock</label><br>
                                            <input type="text" name="stock" class="form-control" style="max-width:70px"
                                                   readonly>
                                        </div>
                                        <div class="form-group">
                                            <label>&nbsp;</label><br>
                                            <a class="btn green btn-circle add"> <i class="fa fa-shopping-cart"></i> Agregar </a>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>

                        <div class="panel panel-default">
                            <div class="panel-heading"> Carro de compra</div>
                            <div class="panel-body">

                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th> Nombre</th>
                                        <th> Unidad</th>
                                        <th width="1%"> Cant.</th>
                                        <th> P. Und.</th>
                                        <th> P. Total</th>
                                        <th width="1%" colspan="2"></th>
                                    </tr>
                                    </thead>
                                    <tbody class="cart_list"></tbody>
                                </table>

                                <div>
                                    <a class="btn btn-danger btn-circle" onclick="Buy.cleanCart();">
                                        Limpiar Compra
                                    </a>
                                </div>

                            </div>
                        </div>

                    </div>

                    <div class="col-md-5">

                        <div class="panel panel-default">
                            <div class="panel-heading"> Compra</div>
                            <div class="panel-body">
                                <form class="form-horizontal form_cart" onsubmit="return false;">

                                    <div class="form-group">
                                        <label for="inputEmail12" class="col-md-3 control-label">Almacén</label>
                                        <div class="col-md-9">
                                            <select name="id_storage" class="form-control">
                                                {foreach item=o from=$storages}
                                                    <option value="{$o.id}">{$o.name}</option>
                                                {/foreach}
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="inputEmail12" class="col-md-3 control-label">Proveedor</label>
                                        <div class="col-md-9">
                                            <select name="id_provider" class="form-control">
                                                <option value="">Elegir...</option>
                                                {foreach item=o from=$providers}
                                                    <option value="{$o.id}">{$o.name}</option>
                                                {/foreach}
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="inputEmail12" class="col-md-3 control-label">Recibo</label>
                                        <div class="col-md-9">


                                            <div class="input-group">
                                                <span class="input-group-btn">
                                                    <select type="text" name="id_proof" class="form-control" style="width:120px">
                                                        {foreach item=o from=$proofs}
                                                            <option value="{$o.id}">{$o.name}</option>
                                                        {/foreach}
                                                    </select>
                                                </span>
                                                <span class="input-group-btn" style="width:10px"></span>
                                                <input type="text" name="num_doc" class="form-control" placeholder="Número">
                                            </div>

                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="inputEmail12" class="col-md-3 control-label">ISC</label>
                                        <div class="col-md-9">
                                            <input type="text" name="isc" class="form-control">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="inputEmail12" class="col-md-3 control-label">Glosa</label>
                                        <div class="col-md-9">
                                            <textarea name="glosa" class="form-control"></textarea>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="inputEmail12" class="col-md-3 control-label">Condición</label>
                                        <div class="col-md-9">
                                            <select name="condicion" class="form-control">
                                                <option value="CON">Contado</option>
                                                <option value="CRE">Crédito</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="inputEmail12" class="col-md-3 control-label">Total</label>
                                        <div class="col-md-9">
                                            <div class="input-group input-group-lg">
                                                <span class="input-group-addon">{$stg->coin}</span>
                                                <input type="text" name="total" class="form-control bold" value="0.00"
                                                       readonly>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <div class="col-md-offset-3 col-md-9">
                                            <a class="btn green btn-circle" onclick="Buy.save();">Guardar</a>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>

                    </div>

                </div>

            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- MODAL BUY -->

<!-- MODAL BUY -->
<div class="modal fade" id="modal_items_sups" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Detalle de compra</h4>
            </div>
            <div class="modal-body">

                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th> Nombre</th>
                        <th> <span class="tooltips" title="Unidad de medida">U. M.</span> </th>
                        <th width="1%"> Cant.</th>
                        <th> <span class="tooltips" title="Precio por unidad">P. Und.</span> </th>
                        <th> P. Total</th>
                    </tr>
                    </thead>
                    <tbody class="list"></tbody>
                </table>

            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- MODAL BUY -->

<script>

    function $Ready() {
        Buy.init();
        //Buy.add();
    }
</script>

{include file='_footer.tpl' js=[
    'js/c_supply.js',
    'js/buy.js'
]}