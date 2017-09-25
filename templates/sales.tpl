{include file='_header.tpl'}

<div class="portlet light">

    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject bold uppercase">{$page_title}</span>
        </div>
        <div class="actions">
            <a class="btn btn-circle green-jungle btn-outline" onclick="goExport();">
                <i class="fa fa-file-excel-o"></i> Exportar
            </a>
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
                    <th> Cliente </th>
                    <th width="1%"> Items</th>
                    <th> Total</th>
                    <th width="1%"> Doc.</th>
                    <th width="1%"> N° Doc.</th>
                    <th width="1%"></th>
                    <th width="1%"></th>
                </tr>
                </thead>
                <tbody id="pager_content">
                {foreach key=i item=o from=$purchases}
                    <tr>
                        <td>{$o.id}</td>
                        <td>{$o.date_added|date_format:"%d-%m-%Y %I:%M %p"}</td>
                        <td>{if empty($o.cl_name)}Público en General{else}{$o.cl_name}{/if}</td>
                        <td>---</td>
                        <td>{$stg->coin}{$o.total|string_format:"%.2f"}</td>
                        <td>{$o.pf_name}</td>
                        <td>{$o.id|string_format:"%07d"}</td>
                        <td style="padding:3px">
                            <span class="btn btn-outline btn-circle dark btn-sm tooltips" title="Imprimir comprobante"
                                  onclick="Print.transaction({$o.id});" style="margin:0">
                                <i class="fa fa-print"></i>
                            </span>
                        </td>
                        <td style="padding:3px">
                            <span class="btn btn-outline btn-circle dark btn-sm tooltips" title="Detalle de venta"
                                  onclick="showPropres({$o.id});" style="margin:0">
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

<!-- MODAL DETALLE VENTA -->
<div class="modal fade" id="modal_items_sups" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title">Detalle de venta</h4>
            </div>
            <div class="modal-body">

                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th> Nombre</th>
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
<!-- MODAL DETALLE VENTA -->

<script>
{literal}
    function $Ready() {
        Buy.init();
        //Buy.add();
    }

    var $mdi = null;
    function showPropres(id_transaction){
        if($mdi == null){
            $mdi = $('#modal_items_sups');
            $mdi.list = $('.list', $mdi);
        }
        api('ajax/box.php', {action:'get_ordpros_transaction',id_transaction:id_transaction}, function(rsp){
            if(rsp.ok){
                var html = '';
                rsp.items.forEach(function(o,i){
                    html += '<tr>';
                    html += ' <td> '+o.product+' - '+o.propre+' </td>';
                    html += ' <td> '+o.quantity+' </td>';
                    html += ' <td> '+stg.coin+num(o.price_unit,2)+' </td>';
                    html += ' <td> '+stg.coin+num(o.price_total,2)+' </td>';
                    html += '</tr>';
                });
                $mdi.list.html(html);
                $mdi.modal('show');
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Obteniendo productos...', true);
    }

    function goExport(){
        var data = $('#filters').serialize();
        location.href = 'sales_export.php?'+data;
    }
{/literal}
</script>

{include file='_footer.tpl' js=[
    'js/c_supply.js',
    'js/buy.js'
]}