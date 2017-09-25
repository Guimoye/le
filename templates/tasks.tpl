{include file='_header.tpl' css=[
    'assets/global/plugins/jquery-ui-autocomplete/jquery-ui.min.css',
    'css/dispatch.css'
]}

<div class="portlet light">

    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject font-dark bold uppercase">{$page_title}</span>
        </div>
        <div class="actions">
            <span class="btn btn-circle green btn-outline" onclick="Task.add();"> <i class="fa fa-plus"></i> Nuevo </span>
        </div>
    </div>

    <div class="portlet-body">

        <!-- FILTERS -->
        <form class="form-inline" id="filters" action="ajax/tasks.php">
            <div class="form-group">
                <label>Rango de fechas</label><br>
                <div class="input-group input-large date-picker input-daterange" data-date-format="yyyy-mm-dd" data-date-end-date="+0d">
                    <input type="date" class="form-control" name="date_from" value="">
                    <span class="input-group-addon"> to </span>
                    <input type="date" class="form-control" name="date_to" value="">
                </div>
            </div>
            <div class="form-group">
                <label>Consulta</label><br>
                <input class="form-control" name="word" placeholder="nombre"/>
            </div>
            <div class="form-group">
                <label>Resultados</label><br>
                <select class="form-control" name="max">
                    <option>10</option>
                    <option>20</option>
                    <option>50</option>
                    <option>100</option>
                    <option>200</option>
                    <option>500</option>
                    <option>1000</option>
                </select>
            </div>
            <div class="form-group">
                <label>Estado</label><br>
                <select class="form-control" name="state">
                    <option value="">Todos</option>
                    {foreach key=key item=val from=$st_driver}
                        <option value="{$key}">{$val}</option>
                    {/foreach}
                </select>
            </div>
            <div class="form-group">
                <label>---</label><br>
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
                <th> Cliente </th>
                <th> Conductor </th>
                <th> Fecha programada </th>
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

<!-- DESPACHO -->
<div class="dispatch modal fade modal-scroll" tabindex="-1"  data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog" style="">
        <div class="modal-content">

            <!-- LEFT -->
            <div class="lft">

                <div class="head noselect">
                    <div class="dsp_tit">Despachar</div>
                </div>

                <div class="lft_scroll scroller" data-height="100%">

                    <div class="form-group client">
                        <div class="input-group">
                            <span class="input-group-addon iga_ph tooltips" title="Selecciona el cliente" data-placement="right">
                                <i class="fa fa-user"></i>
                            </span>
                            <input type="text" class="form-control cli_name" placeholder="Cliente">
                            <span class="input-group-btn">
                                <button class="btn btn-default iga_rgt tooltips" title="Nuevo" data-placement="left" onclick="MClient.add();">
                                    <i class="fa fa-plus font-grey-mint"></i>
                                </button>
                            </span>
                        </div>
                        <div class="input-group _cl_info" style="display:none">
                            <div class="form-control clearfix _items">
                                <div class="_item" style="width:35%"> <i class="fa fa-phone"></i> <label class="phone">xx</label> </div>
                                <div class="_item" style="width:65%"> <i class="fa fa-envelope"></i> <label class="email">xx</label> </div>
                            </div>
                            <span class="input-group-btn">
                                <button class="btn btn-default iga_rgt tooltips cl_edit" title="Editar&nbsp;cliente" data-placement="left">
                                    <i class="fa fa-pencil font-grey-mint"></i>
                                </button>
                            </span>
                        </div>
                    </div>

                    <div class="form-group driver">
                        <div class="input-group driver">
                            <span class="input-group-addon iga_ph tooltips" title="Conductor encargado" data-placement="right">
                                <i class="fa fa-tachometer "></i>
                            </span>
                            <input type="text" class="form-control" placeholder="Conductor" id="driver">
                            <span class="input-group-btn">
                                <a class="btn btn-default search iga_rgt tooltips driver_mk" title="Ubicar&nbsp;en&nbsp;el&nbsp;mapa"
                                   data-placement="left">
                                    <i class="fa fa-map-marker font-grey-mint"></i>
                                </a>
                            </span>
                        </div>
                    </div>

                    <div class="form-group" >
                        <div class="input-group">
                            <span class="input-group-addon iga_ph tooltips" title="Nombre de la tarea" data-placement="right">
                                <i class="fa fa-tag"></i>
                            </span>
                            <input type="text" class="form-control" placeholder="Nombre de la tarea" id="name">
                        </div>
                    </div>

                    <div class="form-group org hide">
                        <div class="input-group">
                            <span class="input-group-addon iga_ph tooltips" title="Origen" data-placement="right"><i class="fa fa-map-marker font-green-jungle"></i></span>
                            <input type="text" class="form-control" placeholder="Origen" id="org" data-lat="0" data-lng="0">
                            <span class="input-group-btn font-grey-mint font-sm">
                                <a class="btn btn-default search" style="padding:6px;border-left:none;border-right:none" onclick="AAdr.geocode(true)">
                                    <i class="fa fa-search"></i>
                                </a>
                            </span>
                            <span class="input-group-btn font-grey-mint">
                                <a class="btn btn-default tooltips" style="padding:6px" title="Guardar&nbsp;favorito" data-placement="left" onclick="Race.savePoint(true)">
                                    <i class="fa fa-heart font-sm"></i>
                                </a>
                            </span>
                        </div>
                    </div>

                    <div class="form-group dst">
                        <div class="input-group">
                            <span class="input-group-addon iga_ph tooltips" title="Destino" data-placement="right"><i class="fa fa-map-marker font-red-flamingo"></i></span>
                            <input type="text" class="form-control" placeholder="Destino" id="dst" data-lat="0" data-lng="0">
                            <span class="input-group-btn">
                                <a class="btn btn-default iga_rgt search dst_search" style="border-left:none;border-right:none">
                                    <i class="fa fa-search font-grey-mint font-sm"></i>
                                </a>
                            </span>
                            <span class="input-group-btn">
                                <a class="btn btn-default iga_rgt tooltips dst_add" style="padding:6px" title="Guardar&nbsp;como&nbsp;dirección&nbsp;principal" data-placement="left">
                                    <i class="fa fa-save font-grey-mint"></i>
                                </a>
                            </span>
                        </div>
                    </div>

                    <div class="form-group prog">
                        <div class="input-group">
                            <span class="input-group-addon iga_ph tooltips" title="Fecha y hora de entrega" data-placement="right">
                                <i class="fa fa-calendar"></i>
                            </span>
                            <input type="date" class="form-control tooltips prog_date" title="Elegir Fecha" style="width:55%">
                            <input type="time" class="form-control tooltips prog_time" title="Elegir Hora" style="width:45%">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="input-group">
                            <span class="input-group-addon iga_ph"><i class="fa fa-pencil-square-o"></i></span>
                            <textarea class="form-control" id="notes" placeholder="Notas para el conductor"></textarea>
                        </div>
                    </div>

                    {*<div style="height:1000px"></div>*}

                </div>

                <div class="acts">
                    <button class="btn btn-default cancel">Cancelar</button>
                    <button class="btn btn-primary save">Asignar</button>
                </div>
            </div>
            <!-- END LEFT -->

            <!-- LEFT -->
            <div class="rgt">

                <div id="dsp_map" style="height:100%;"></div>

            </div>
            <!-- END LEFT -->

        </div>
    </div>
</div>
<!-- EMD DESPACHO -->

{literal}
<script>
    function $Ready(){
        Task.init();
        //Task.add();
    }
</script>
{/literal}

{include file='_footer.tpl' js=[
    'assets/global/plugins/jquery-ui-autocomplete/jquery-ui.min.js',
    'js/mclient.js',
    'js/task.js',
    'js/pager.js'
]}