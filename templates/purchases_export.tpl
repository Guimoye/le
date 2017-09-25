{include file='_header.tpl'}

<div class="portlet light">

    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject bold uppercase">{$page_title}</span>
        </div>
    </div>

    <div class="portlet-body">

        {if empty($purchases)}
            <div class="alert alert-warning">
                No hay registros.
            </div>
        {else}
            <div style="overflow-x:auto">
                <table class="table table-bordered" id="table_export">
                    <thead>
                    <tr>
                        <th rowspan="3">FECHA DE EMISION DEL COMPROBANTE DE PAGO O DOCUMENTO</th>
                        <th rowspan="3">FECHA DE VENCIMIENTO O FECHA DE PAGO</th>
                        <th colspan="3" rowspan="2">COMPROBANTE DE PAGO O DOCUMENTO</th>
                        <th rowspan="3">N° COMPROBANTE PAGO</th>
                        <th colspan="3">INFORMACION PROVEEDOR</th>
                        <th colspan="2" rowspan="2">ADQUISICIONES GRAVADAS DESTINADAS A OPERACIONES GRAVADAS Y/O EXPORTACION </th>
                        <th colspan="2" rowspan="2">ADQUISICIONES GRAVADAS DESTINADAS A OPERACIONES GRAVADAS Y/O DE EXPORTACION Y A OPERACIONES NO GRAVADAS </th>
                        <th colspan="2" rowspan="2">ADQUISICIONES GRAVADAS DESTINADAS A OPERACIONES NO GRAVADAS</th>
                        <th rowspan="3">VALOR DE LAS ADQUISICIONES NO GRAVADAS</th>
                        <th rowspan="3">ISC</th>
                        <th rowspan="3">OTROS TRIBUTOS Y CARGOS</th>
                        <th rowspan="3">IMPORTE TOTAL</th>
                        <th rowspan="3">N° COMPROBANTE DE PAGO EMITIDO POR SUJETO NO DOMICILIADO</th>
                        <th colspan="2" rowspan="2">CONSTANCIA DE DEPOSITO DE DETRACCION</th>
                        <th rowspan="3">TIPO DE CAMBIO</th>
                        <th colspan="4" rowspan="2">REFERENCIA DEL COMPROBANTE DE PAGO O DOCUMENTO ORIGINAL QUE SE MODIFICA </th>
                        <th rowspan="3">MONEDA</th>
                        <th rowspan="3">EQUIVALENTE EN DOLARES AMERICANOS</th>
                        <th rowspan="3">FECHA VENCIMIENTO</th>
                        <th rowspan="3">CONDICION CONTADO/CREDITO</th>
                        <th rowspan="3">CUENTA CONTABLE BASE IMPONIBLE</th>
                        <th rowspan="3">CUENTA CONTABLE OTROS TRIBUTOS Y CARGOS</th>
                        <th rowspan="3">CUENTA CONTABLE TOTAL</th>
                        <th rowspan="3">CODIGO CENTRO DE COSTOS</th>
                        <th rowspan="3">CODIGO CENTRO DE COSTOS 2</th>
                        <th rowspan="3">REGIMEN ESPECIAL</th>
                        <th rowspan="3">PORCENTAJE REGIMEN ESPECIAL</th>
                        <th rowspan="3">IMPORTE REGIMEN ESPECIAL</th>
                        <th rowspan="3">SERIE DOCUMENTO REGIMEN ESPECIAL</th>
                        <th rowspan="3">NUMERO DOCUMENTO REGIMEN ESPECIAL</th>
                        <th rowspan="3">FECHA DOCUMENTO REGIMEN ESPECIAL</th>
                        <th rowspan="3">CODIGO PRESUPUESTO</th>
                        <th rowspan="3">PORCENTAJE I.G.V.</th>
                        <th rowspan="3">GLOSA</th>
                        <th rowspan="3">CONDICIÓN DE PERCEPCIÓN</th>
                        <th rowspan="3">IMPORTE RÉGIMEN ESPECIAL</th>
                    </tr>
                    <tr>
                        <th colspan="2">DOCUMENTO IDENTIDAD</th>
                        <th rowspan="2">APELLIDOS Y NOMBRES, DENOMINACION O RAZON SOCIAL</th>
                    </tr>
                    <tr>
                        <th>TIPO</th>
                        <th>SERIE/CODIGO ADUANA</th>
                        <th>AÑO EMISION DUA O DSI</th>
                        <th>TIPO</th>
                        <th>NUMERO</th>
                        <th>BASE IMPONIBLE</th>
                        <th>IGV</th>
                        <th>BASE IMPONIBLE</th>
                        <th>IGV</th>
                        <th>BASE IMPONIBLE</th>
                        <th>IGV</th>
                        <th>NUMERO</th>
                        <th>FECHA EMISION</th>
                        <th>FECHA</th>
                        <th>TIPO</th>
                        <th>SERIE</th>
                        <th>N° COMPROBANTE PAGO O DOCUMENTO</th>
                    </tr>
                    </thead>
                    <tbody>

                    {foreach key=i item=o from=$purchases}
                        <tr>
                            <td> {$o.date_added|date_format:"%d/%m/%Y"} </td>
                            <td> {$o.date_added|date_format:"%d/%m/%Y"} </td>
                            <td> {$o.pf_code} </td>
                            <td>  </td>
                            <td> {$o.date_added|date_format:"%Y"} </td>
                            <td> {$o.num_doc} </td>
                            <td> 6 </td>
                            <td> {$o.pr_ruc} </td>
                            <td> {$o.pr_name} </td>
                            <td> {$o.adq_0_base|string_format:"%.2f"} </td>
                            <td> {$o.adq_0_igv|string_format:"%.2f"} </td>
                            <td> {$o.adq_1_base|string_format:"%.2f"} </td>
                            <td> {$o.adq_1_igv|string_format:"%.2f"} </td>
                            <td> {$o.adq_2_base|string_format:"%.2f"} </td>
                            <td> {$o.adq_2_igv|string_format:"%.2f"} </td>
                            <td> </td>
                            <td> {$o.isc} </td>
                            <td> </td>
                            <td> {$o.total|string_format:"%.2f"} </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> {$stg->tc} </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> S </td>
                            <td> </td>
                            <td> {$o.date_added|date_format:"%d/%m/%Y"} </td>
                            <td> {$o.condicion} </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> </td>
                            <td> {$stg->igv} </td>
                            <td> {$o.glosa} </td>
                            <td> </td>
                            <td> </td>
                        </tr>
                    {/foreach}
                    </tbody>
                </table>
            </div>
        {/if}

    </div>

</div>

<script>
{literal}
    function $Ready() {
        $('#table_export').tableExport({
            type:'excel',
            fileName: 'compras'
        });
        window.history.back();

    }
{/literal}
</script>

{include file='_footer.tpl' js=[
    'js/ext/tableExport.js'
]}