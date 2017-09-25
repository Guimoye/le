{include file='_header.tpl'}

<div class="portlet light">

    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject bold uppercase">{$page_title}</span>
        </div>
    </div>

    <div class="portlet-body">

        <!-- FILTERS -->
        <form class="form-inline" id="filters" style="margin-bottom:10px">
            <div class="form-group">
                <div class="input-group input-large date-picker input-daterange" data-date-format="yyyy-mm-dd" data-date-end-date="+0d">
                    <span class="input-group-addon"> Desde </span>
                    <input type="date" class="form-control" name="date_from" value="{$fs->date_from}">
                    <span class="input-group-addon"> hasta </span>
                    <input type="date" class="form-control" name="date_to" value="{$fs->date_to}">
                </div>
            </div>
            <div class="form-group">
                <button type="submit" class="btn blue apply">Aplicar</button>
            </div>
        </form>
        <!-- END FILTERS -->

        {if empty($purchases)}
            <div class="alert alert-warning">
                No se han realizado ventas aún.
            </div>
        {else}
            <table class="table table-striped table-bordered table-hover dt-responsive mdl-td">
                <thead>
                <tr>
                    <th width="1%"> #</th>
                    <th width="155px"> Fecha de registro</th>
                    <th width="1%"> Doc.</th>
                    <th width="1%"> N° Doc.</th>
                    <th> Cliente </th>
                    <th> Total</th>
                    <th width="1%"></th>
                </tr>
                </thead>
                <tbody id="pager_content">
                {foreach key=i item=o from=$purchases}
                    <tr>
                        <td>{$o.id}</td>
                        <td>{$o.date_added|date_format:"%d-%m-%Y %I:%M %p"}</td>
                        <td>{$o.pf_name}</td>
                        <td>{$o.id|string_format:"%07d"}</td>
                        <td>{if empty($o.cl_name)}Público en General{else}{$o.cl_name}{/if}</td>
                        <td>{$stg->coin}{$o.total|string_format:"%.2f"}</td>
                        <td>
                            <span class="btn btn-circle red" onclick="annul({$o.id});">
                                Anular
                            </span>
                        </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        {/if}

    </div>

</div>

<script>

    function $Ready() {

    }

    {literal}
    function annul(id){
        bootbox.confirm({
            message: '¿Seguro que quieres anular esta venta?',
            buttons: {
                cancel: {label:'Cancelar'},
                confirm: {label:'Si, anular', className: 'btn-danger'}
            },
            callback: function(result){
                if(!result) return;
                bootbox.prompt({
                    title: 'Motivo de anulación',
                    placeholder: 'Indique el motivo',
                    buttons: {
                        cancel: {label:'Cancelar'},
                        confirm: {label:'Listo'}
                    },
                    callback: function(reason){
                        if(reason==null) return;
                        api('ajax/box.php', {action:'annul_transaction', id:id, reason:reason}, function(rsp){
                            if(rsp.ok){
                                toastr.success('Guardado correctamente');
                                location.reload();
                            } else {
                                bootbox.alert(rsp.msg);
                            }
                        }, 'Anulando venta...');
                    }
                });
            }
        });
    }
    {/literal}
</script>

{include file='_footer.tpl' js=[

]}