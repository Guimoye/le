{include file='_header.tpl'}

<div class="portlet light">

    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject font-dark bold uppercase">{$page_title}</span>
        </div>
        <div class="actions">
            {if $can_edit}
                <span class="btn btn-circle blue" onclick="MDriver.add();"> <i class="fa fa-plus"></i> Registrar </span>
                <span class="btn btn-circle btn-outline green-jungle" onclick="MImportCabify.open();">
                    <i class="fa fa-file-excel-o"></i> Importar Cabify
                </span>
            {/if}
        </div>
    </div>

    <div class="portlet-body">

        <!-- FILTERS -->
        <form class="form-inline" id="filters" action="drivers/pager">
            <div class="form-group">
                <label>Flota</label><br>
                <select name="id_fleet" class="form-control">
                    <option value="">Todo...</option>
                    {foreach key=i item=o from=$fleets}
                        <option value="{$o.id}">{$o.name}</option>
                    {/foreach}
                </select>
            </div>
            <div class="form-group">
                <label>Consulta</label><br>
                <input class="form-control" name="word" placeholder="Nombre de conductor"/>
            </div>
            <div class="form-group">
                <label>&nbsp;</label><br>
                <a href="?" class="btn grey">Reiniciar</a>
                <button type="button" class="btn blue apply">Aplicar</button>
            </div>
        </form>
        <!-- END FILTERS -->

        <table class="table table-striped table-bordered table-hover dt-responsive mdl-td" style="margin-top:10px">
            <thead>
            <tr>
                <th> Nombres </th>
                <th> Placa </th>
                <th> Gastos </th>
                <th> Préstamos </th>
                <th> Kilometraje </th>
                <th> Fecha Inicio </th>
                <th> Semanas </th>
                <th> Deuda Vencida </th>
                <th width="1%">Opciones</th>
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
                                <a href="#tab_dv" data-toggle="tab"> Datos del Conductor </a>
                            </li>
                            <li>
                                <a href="#tab_gr" data-toggle="tab"> Datos del Aval </a>
                            </li>
                            <li>
                                <a href="#tab_vh" data-toggle="tab"> Datos de Vehículo </a>
                            </li>
                        </ul>
                        <div class="tab-content" style="padding-bottom:0">

                            <div id="tab_dv" class="tab-pane active">
                                <div class="row">

                                    <div class="col-md-6">

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Flota</label>
                                            <div class="col-md-7">
                                                <select class="form-control" name="id_fleet">
                                                    <option value="0">Elegir...</option>
                                                    {foreach key=i item=o from=$fleets}
                                                        <option value="{$o.id}">{$o.name}</option>
                                                    {/foreach}
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Nombres *</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Apellidos *</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="surname" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Fecha de nacimiento</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="date_birth" type="date" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">DNI *</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="dni" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">RUC</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="ruc" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Licencia de conducir *</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="driver_licence" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Ciudad</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="city" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Distrito</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="district" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Dirección</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="address" placeholder="Escribir...">
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-md-6">

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Teléfono</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="phone_cell" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Casa</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="phone_house" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Correo</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="email" type="email" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Contraseña</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="password" type="password" placeholder="Escribe una contraseña...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Estado civil</label>
                                            <div class="col-md-7">
                                                <select class="form-control" name="civil_status">
                                                    <option>Soltero</option>
                                                    <option>Casado</option>
                                                    <option>Conviviente</option>
                                                    <option>Divorciado</option>
                                                    <option>Viudo</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Nombre conyugue</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="wife_name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">DNI</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="wife_dni" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Banco</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="bank_name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Número de cuenta</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="bank_account" placeholder="Escribir...">
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>

                            <div id="tab_gr" class="tab-pane">
                                <div class="row">

                                    <div class="col-md-6">

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Nombre</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="gt_name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">DNI</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="gt_dni" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Ciudad</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="gt_city" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Distrito</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="gt_district" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Dirección</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="gt_address" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Teléfono</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="gt_phone" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Correo</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="gt_email" type="email" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Lugar de trabajo</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="gt_job_place" placeholder="Escribir...">
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-md-6">

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Puesto</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="gt_job_role" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Ciudad trabajo</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="gt_job_city" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Distrito trabajo</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="gt_job_district" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Dirección trabajo</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="gt_job_address" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Teléfono trabajo</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="gt_job_phone" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Jefe directo</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="gt_job_boss_name" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Puesto jefe directo</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="gt_job_boss_role" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Correo</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="gt_job_boss_email" placeholder="Escribir...">
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>

                            <div id="tab_vh" class="tab-pane">
                                <div class="row">

                                    <div class="col-md-6">

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Marca</label>
                                            <div class="col-md-7">
                                                <select class="form-control" name="id_brand">
                                                    <option value="">Elegir...</option>
                                                    {foreach key=i item=o from=$brands}
                                                        <option value="{$o.id}">{$o.name}</option>
                                                    {/foreach}
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Modelo</label>
                                            <div class="col-md-7">
                                                <select class="form-control" name="id_model" disabled>
                                                    <option value="">---</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Placa</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="vh_plate" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Año de fabricación</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="vh_year" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Color</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="vh_color" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">N. Motor</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="vh_engine_number" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">N. Seria Chasis</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="vh_serial_chassis" placeholder="Escribir...">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">Combustible</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="vh_fuel" placeholder="Escribir...">
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-md-6">

                                        <div class="form-group">
                                            <label class="col-md-5 control-label">N. GPS</label>
                                            <div class="col-md-7">
                                                <input class="form-control" name="vh_gps_number" placeholder="Escribir...">
                                            </div>
                                        </div>

                                    </div>

                                </div>

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
        //MDriver.add();

        //MImportCabify.open();
    }
</script>
{/literal}

{include file='_footer.tpl' js=[
    'assets/global/plugins/jquery.form.min.js',
    'views/js/m_import_cabify.js',
    'views/js/m_driver.js',
    'views/js/pager.js'
]}