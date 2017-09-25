{include file='_header.tpl'}

<div class="portlet light">

    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject bold uppercase">{$page_title}</span>
        </div>
        <div class="actions"></div>
    </div>

    <div class="portlet-body">

        {if empty($supplies)}
            <div class="panel-body">
                No hay datos.
            </div>
        {else}
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th width="1%"> # </th>
                    <th> Insumo </th>
                    <th> Costo </th>
                    <th width="1%"> Stock </th>
                    <th> <span class="tooltips" title="Unidad de medida">UM</span> </th>
                </tr>
                </thead>
                <tbody>
                {foreach key=i item=o from=$supplies}
                    <tr {if $o.stock <= $o.stock_min}style="background:rgba(255,0,0,.02)"{/if}>
                        <td> {$o.id} </td>
                        <td> {$o.name} </td>
                        <td> {$stg->coin}{$o.cost|string_format:"%.2f"} </td>
                        <td class="ctr"> <b>{$o.stock|round:"2"}</b> </td>
                        <td> {$o.un_name} </td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        {/if}

    </div>

</div>

{include file='_footer.tpl'}