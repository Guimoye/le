{include file='_header.tpl'}

<div class="portlet light">

    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject bold uppercase">{$page_title}</span>


            <select class="form-control" name="id_supply" id="id_supply">
                {if empty($supplies)}
                    <option value="">No hay insumos</option>
                {else}
                    {foreach key=i item=o from=$supplies}
                        <option value="{$o.id}" {if $o.id == $id_supply}selected{/if}>{$o.name}</option>
                    {/foreach}
                {/if}
            </select>
        </div>
        <div class="actions">


            <a class="btn btn-circle green-jungle btn-outline" onclick="exportExcel();">
                <i class="fa fa-file-excel-o"></i> Exportar
            </a>

        </div>
    </div>

    <div class="portlet-body">

        {if empty($kardexs)}
            <div class="panel-body">
                No hay datos.
            </div>
        {else}
            <table class="table table-bordered" id="table_export">
                <thead>
                <tr>
                    <th class="ctr upc" colspan="2" bgcolor="#eee"> {$supply->name} </th>
                    <th class="ctr" colspan="3"> Entradas </th>
                    <th class="ctr" colspan="3"> Salidas </th>
                    <th class="ctr" colspan="3"> Saldo </th>
                    {*<th class="ctr" rowspan="2" width="1%"> Stock </th>*}
                </tr>
                <tr>
                    <th width="1%"> Fecha </th>
                    <th> Descripción </th>

                    <th width="1%"> Cantidad </th>
                    <th width="1%"> V.&nbsp;Unit </th>
                    <th width="1%"> V.&nbsp;Total </th>

                    <th width="1%"> Cantidad </th>
                    <th width="1%"> V.&nbsp;Unit </th>
                    <th width="1%"> V.&nbsp;Total </th>

                    <th width="1%"> Cantidad </th>
                    <th width="1%"> V.&nbsp;Unit </th>
                    <th width="1%"> V.&nbsp;Total </th>
                </tr>
                </thead>
                <tbody>
                {foreach key=i item=o from=$kardexs}
                    <tr>
                        <td> {$o.date_added|date_format:"%d/%m/%Y"} </td>
                        <td>
                            {if $o.type == 1}
                                Ingreso ({$o.description})
                            {elseif $o.type == 2}
                                Salida ({$o.description})
                            {elseif $o.type == 3}
                                Saldo ({$o.description})
                            {else}
                                ---
                            {/if}
                        </td>

                        {if $o.type == 1}
                            <td> {$o.quantity} </td>
                            <td> {$o.v_unit|string_format:"%.2f"} </td>
                            <td> {$o.v_total|string_format:"%.2f"} </td>
                        {else} <td></td><td></td><td></td>{/if}

                        {if $o.type == 2}
                            <td> {$o.quantity} </td>
                            <td> {$o.v_unit|string_format:"%.2f"} </td>
                            <td> {$o.v_total|string_format:"%.2f"} </td>
                        {else} <td></td><td></td><td></td>{/if}

                        {*{if $o.type == 3}
                            <td> {$o.quantity} </td>
                            <td> {$o.v_unit|string_format:"%.2f"} </td>
                            <td> {$o.v_total|string_format:"%.2f"} </td>
                        {else} <td></td><td></td><td></td>{/if}*}

                        <td>{$o.balance}</td><td>-</td><td>-</td>

                        {*<td> {$o.stock} </td>*}

                    </tr>
                {/foreach}
                </tbody>
            </table>
        {/if}

    </div>

</div>

<script>
    function $Ready(){

        $('#id_supply').change(function(e){
            location.href = '?id_supply=' + this.value;
        });

    }

    function exportExcel(){
        $('#table_export').tableExport({
            type:'excel',
            fileName: 'compras'
        });
    }
</script>

{include file='_footer.tpl' js=[
'js/ext/tableExport.js'
]}