{include file='_header.tpl'}

<div class="portlet light">

    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject bold uppercase">{$page_title}</span>
        </div>
        <div class="actions">

        </div>
    </div>

    <div class="portlet-body table-scrollable">

        <table class="table table-bordered">
            <thead>
            <tr>
                <th class="ctr mdl" rowspan="3">FECHA DE EMISION DEL COMPROBANTE DE PAGO O DOCUMENTO</th>
                <th class="ctr mdl" rowspan="3">FECHA DE VENCIMIENTO O FECHA DE PAGO</th>
                <th class="ctr mdl" colspan="3" rowspan="2">COMPROBANTE DE PAGO O DOCUMENTO</th>
                <th class="ctr mdl" colspan="3">INFORMACION DEL CLIENTE</th>
                <th class="ctr mdl" rowspan="3">VALOR FACTURADO DE LA EXPORTACION</th>
                <th class="ctr mdl" rowspan="3">BASE IMPONIBLE DE LA OPERACION GRAVADA</th>
                <th class="ctr mdl" colspan="2" rowspan="2">IMPORTE TOTAL DE LA OPERACION EXONERADA O INAFECTA</th>
                <th class="ctr mdl" rowspan="3">ISC</th>
                <th class="ctr mdl" rowspan="3">IGV Y/O IPM</th>
                <th class="ctr mdl" rowspan="3">OTROS TRIBUTOS Y CARGOS QUE NO FORMAN PARTE DE LA BASE IMPONIBLE</th>
                <th class="ctr mdl" rowspan="3">IMPORTE TOTAL DEL COMPROBANTE DE PAGO</th>
                <th class="ctr mdl" rowspan="3">TIPO DE CAMBIO</th>
                <th class="ctr mdl" colspan="4" rowspan="2">REFERENCIA DEL COMPROBANTE DE PAGO O DOCUMENTO ORIGINAL QUE SE MODIFICA</th>
                <th class="ctr mdl" rowspan="3">MONEDA</th>
                <th class="ctr mdl" rowspan="3">EQUIVALENTE EN DOLARES AMERICANOS</th>
                <th class="ctr mdl" rowspan="3">FECHA VENCIMIENTO</th>
                <th class="ctr mdl" rowspan="3">CONDICION CONTADO/CREDITO</th>
                <th class="ctr mdl" rowspan="3">CODIGO CENTRO DE COSTOS</th>
                <th class="ctr mdl" rowspan="3">CODIGO CENTRO DE COSTOS 2</th>
                <th class="ctr mdl" rowspan="3">CUENTA CONTABLE BASE IMPONIBLE</th>
                <th class="ctr mdl" rowspan="3">CUENTA CONTABLE OTROS TRIBUTOS Y CARGOS</th>
                <th class="ctr mdl" rowspan="3">CUENTA CONTABLE TOTAL</th>
                <th class="ctr mdl" rowspan="3">REGIMEN ESPECIAL</th>
                <th class="ctr mdl" rowspan="3">PORCENTAJE REGIMEN ESPECIAL</th>
                <th class="ctr mdl" rowspan="3">IMPORTE REGIMEN ESPECIAL</th>
                <th class="ctr mdl" rowspan="3">SERIE DOCUMENTO REGIMEN ESPECIAL</th>
                <th class="ctr mdl" rowspan="3">NUMERO DOCUMENTO REGIMEN ESPECIAL</th>
                <th class="ctr mdl" rowspan="3">FECHA DOCUMENTO REGIMEN ESPECIAL</th>
                <th class="ctr mdl" rowspan="3">CODIGO PRESUPUESTO</th>
                <th class="ctr mdl" rowspan="3">PORCENTAJE I.G.V.</th>
                <th class="ctr mdl" rowspan="3">GLOSA</th>
                <th class="ctr mdl" rowspan="3">MEDIO DE PAGO</th>
                <th class="ctr mdl" rowspan="3">CONDICIÓN DE PERCEPCIÓN</th>
                <th class="ctr mdl" rowspan="3">IMPORTE PARA CALCULO RÉGIMEN ESPECIAL</th>
            </tr>
            <tr>
                <th class="ctr mdl" colspan="2">DOCUMENTO IDENTIDAD</th>
                <th class="ctr mdl" rowspan="2">APELLIDOS Y NOMBRES, DENOMINACION O RAZON SOCIAL</th>
            </tr>
            <tr>
                <th class="ctr mdl">TIPO</th>
                <th class="ctr mdl">N° SERIE/N° SERIE MAQ REGIS</th>
                <th class="ctr mdl">NUMERO</th>
                <th class="ctr mdl">TIPO</th>
                <th class="ctr mdl">NUMERO</th>
                <th class="ctr mdl">EXONERADA</th>
                <th class="ctr mdl">INAFECTA</th>
                <th class="ctr mdl">FECHA</th>
                <th class="ctr mdl">TIPO</th>
                <th class="ctr mdl">SERIE</th>
                <th class="ctr mdl">N° COMPROBANTE PAGO O DOCUMENTO</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td>dd/mm/yyyy</td>
                <td>dd/mm/yyyy</td>
                <td>02 CARACTERES</td>
                <td>06 CARACTERES</td>
                <td>13 CARACTERES</td>
                <td>01 CARACTER</td>
                <td>11 CARACTERES</td>
                <td>60 CARACTERES</td>
                <td>(15,2) NUMERICO</td>
                <td>(15,2) NUMERICO</td>
                <td>(15,2) NUMERICO</td>
                <td>(15,2) NUMERICO</td>
                <td>(15,2) NUMERICO</td>
                <td>(15,2) NUMERICO</td>
                <td>(15,2) NUMERICO</td>
                <td>(15,2) NUMERICO</td>
                <td>(10,4) NUMERICO</td>
                <td>dd/mm/yyyy</td>
                <td>02 CARACTERES</td>
                <td>06 CARACTERES</td>
                <td>13 CARACTERES</td>
                <td>01 CARACTER</td>
                <td>(15,2) NUMERICO</td>
                <td>dd/mm/yyyy</td>
                <td>03 CARACTERES</td>
                <td>09 CARACTERES</td>
                <td>09 CARACTERES</td>
                <td>10 CARACTERES</td>
                <td>10 CARACTERES</td>
                <td>10 CARACTERES</td>
                <td>1 NUMERICO</td>
                <td>(5,2) NUMERICO</td>
                <td>(15,2) NUMERICO</td>
                <td>06 CARACTERES</td>
                <td>13 CARACTERES</td>
                <td>dd/mm/yyyy</td>
                <td>10 CARACTERES</td>
                <td>(5,2) NUMERICO</td>
                <td>60 CARACTERES</td>
                <td>03 CARACTERES</td>
                <td>01 CARACTER</td>
                <td>(15,2) NUMERICO</td>
            </tr>
            </tbody>
        </table>



    </div>

</div>


<!-- MODAL UNIMED -->
<div class="modal fade" id="modal_add_provider" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" style="max-width:450px">
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
                        <label class="col-md-3 control-label">Razón social</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="name" placeholder="Escribir...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">RUC</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="ruc" placeholder="Escribir...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Email</label>
                        <div class="col-md-9">
                            <input type="email" class="form-control" name="email" placeholder="Escribir...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Teléfono</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="phone" placeholder="Escribir...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Dirección</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="address" placeholder="Escribir...">
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
    var providers = {$providers|@json_encode};

    function $Ready(){
        MProvider.init();
        //MProvider.add();
    }
</script>

{include file='_footer.tpl' js=[
    'js/m_provider.js'
]}