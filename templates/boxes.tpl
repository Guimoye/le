{include file='_header.tpl'}


<div class="portlet light">

    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject bold uppercase">{$page_title}</span>
        </div>
        <div class="actions">
            {if $can_boxes}
                <span class="btn btn-circle green btn-outline" onclick="MBox.add();"> <i class="fa fa-plus"></i> Nuevo </span>
            {/if}
        </div>
    </div>

    <div class="portlet-body">

        {if empty($boxes)}
            <div class="alert alert-warning">
                Todavía no se han creado cajas.
            </div>
        {else}
            <table class="table table-striped table-bordered table-hover dt-responsive mdl-td">
                <thead>
                <tr>
                    <th rowspan="2" width="1%"> # </th>
                    <th rowspan="2"> Nombre </th>
                    <th colspan="3" class="ctr"> Impresora </th>
                    <th rowspan="2" width="155px"> Fecha de creación </th>
                    <th rowspan="2" width="1%"></th>
                </tr>
                <tr>
                    <th> IP </th>
                    <th> Nombre </th>
                    <th> Serie </th>
                </tr>
                </thead>
                <tbody id="pager_content">
                {foreach key=i item=o from=$boxes}
                    <tr>
                        <td>{$o.id}</td>
                        <td>{$o.name}</td>

                        <td>{$o.printer_ip}</td>
                        <td>{$o.printer_name}</td>
                        <td>{$o.printer_serial}</td>

                        <td>{$o.date_added|date_format:"%d-%m-%Y %I:%M %p"}</td>
                        <td>
                            {if $can_boxes}
                                <span class="btn btn-outline btn-circle dark btn-sm" onclick="MBox.edit(boxes[{$i}]);">
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


<!-- MODAL -->
<div class="modal fade" id="modal_add_box" tabindex="-1" aria-hidden="true">
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
                        <label class="col-md-3 control-label">Nombre</label>
                        <div class="col-md-9">
                            <input class="form-control" name="name" placeholder="Escribir...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3"></label>
                        <div class="col-md-9 bold">
                            Impresora Precuenta
                            <a class="btn btn-default btn-xs pull-right test_print"><i class="fa fa-print"></i> Probar</a>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">IP</label>
                        <div class="col-md-9">
                            <input class="form-control" name="printer_ip" placeholder="Dirección IP...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Nombre</label>
                        <div class="col-md-9">
                            <input class="form-control" name="printer_name" placeholder="Nombre compartido...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Nº Serie</label>
                        <div class="col-md-9">
                            <input class="form-control" name="printer_serial" placeholder="Serie...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Letras</label>
                        <div class="col-md-9">
                            <input class="form-control" name="printer_line_letters" placeholder="Letras por linea...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3"></label>
                        <div class="col-md-9 bold">
                            Impresora Boleta/Factura
                            <a class="btn btn-default btn-xs pull-right test_print2"><i class="fa fa-print"></i> Probar</a>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">IP</label>
                        <div class="col-md-9">
                            <input class="form-control" name="printer2_ip" placeholder="Dirección IP...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Nombre</label>
                        <div class="col-md-9">
                            <input class="form-control" name="printer2_name" placeholder="Nombre compartido...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Nº Serie</label>
                        <div class="col-md-9">
                            <input class="form-control" name="printer2_serial" placeholder="Serie...">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Letras</label>
                        <div class="col-md-9">
                            <input class="form-control" name="printer2_line_letters" placeholder="Letras por linea...">
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
<!-- MODAL -->

<script>
    var boxes = {$boxes|@json_encode};

    function $Ready(){
        MBox.init();
        //MBox.add();
    }
</script>

{include file='_footer.tpl' js=[
    'js/m_box.js'
]}