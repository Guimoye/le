{include file='_header.tpl'}

<div class="portlet light">

    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject font-dark bold uppercase">{$page_title}</span>
        </div>
        <div class="actions">
            <span class="btn btn-circle blue" onclick="MDriver.add();"> <i class="fa fa-plus"></i> Registrar </span>
        </div>
    </div>

    <div class="portlet-body">

        <!-- FILTERS -->
        <form class="form-inline" id="filters" action="ajax/drivers.php">
            <div class="form-group">
                <label>Consulta</label><br>
                <input class="form-control" name="word" placeholder="nombre,email,celular"/>
            </div>
            <div class="form-group">
                <label>&nbsp;</label><br>
                <a href="?" class="btn grey">Reiniciar</a>
                <button type="button" class="btn blue apply">Aplicar</button>
            </div>
        </form>
        <!-- END FILTERS -->

        <table class="table table-striped table-bordered table-hover dt-responsive" style="margin-top:10px">
            <thead>
            <tr>
                <th width="1%"> # </th>
                <th> Nombre </th>
                <th width="1%"> Email </th>
                <th> Celular </th>
                <th> Fecha de registro </th>
                <th width="1%"> Estado </th>
                <th width="1%" colspan="2"></th>
            </tr>
            </thead>
            <tbody id="pager_content"></tbody>
        </table>

        <div id="pager">
            <ul class="pagination">
                <li class="prev"><a><i class="fa fa-angle-left"></i> Anterior</a></li>
                <li class="disabled"><span class="curr"> 0 </span></li>
                <li class="next"><a>Siguiente <i class="fa fa-angle-right"></i></a></li>
            </ul>
        </div>

    </div>

</div>

<!-- MODAL -->
<div id="modal_add_driver" class="modal fade modal-scroll" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h4 class="modal-title">---</h4>
            </div>
            <div class="modal-body">

                <form class="form-horizontal">
                    <input type="hidden" name="action" value="add">
                    <input type="hidden" name="id" value="">

                    <div class="tabbable-line">
                        <ul class="nav nav-tabs ">
                            <li class="active">
                                <a href="#tab_15_1" data-toggle="tab"> Datos del Conductor </a>
                            </li>
                            <li>
                                <a href="#tab_15_2" data-toggle="tab"> Datos del Aval </a>
                            </li>
                            <li>
                                <a href="#tab_15_3" data-toggle="tab"> Datos de Vehículo </a>
                            </li>
                        </ul>
                        <div class="tab-content" style="padding-bottom:0">

                            <div class="tab-pane active" id="tab_15_1">
                                <div class="row">

                                    <div class="col-md-6">

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Nombres</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Apellidos</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Fecha de nacimiento</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">DNI</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">RUC</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Licencia de conducir</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Ciudad</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Distrito</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-md-6">

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Teléfono</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Casa</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Correo</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Estado civil</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Nombre conyugue</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">DNI</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Banco</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Número de cuenta</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>

                            <div class="tab-pane" id="tab_15_2">
                                <div class="row">

                                    <div class="col-md-6">

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Nombre</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">DNI</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Distrito</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Dirección</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Teléfono</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Correo</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Lugar de trabajo</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Puesto</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-md-6">

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Dirección trabajo</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Distrito trabajo</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Teléfono trabajo</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Jefe directo</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Puesto jefe directo</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Correo</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>

                            <div class="tab-pane" id="tab_15_3">
                                ccc
                            </div>
                        </div>
                    </div>

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn red pull-left remove">Eliminar</button>
                <button type="button" data-dismiss="modal" class="btn btn-outline btn-default cancel">Cancelar</button>
                <button type="button" class="btn blue save">Guardar</button>
            </div>
        </div>
    </div>
</div>
<!-- END MODAL -->


{literal}
<script>
    function $Ready(){
        MDriver.init();
        MDriver.add();
        //MCar.add(1);
    }
</script>
{/literal}

{include file='_footer.tpl' js=[
    'js/mdriver.js',
    'js/pager.js'
]}