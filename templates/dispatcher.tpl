{include file='_header.tpl' css=[
    'assets/global/plugins/jquery-ui-autocomplete/jquery-ui.min.css',
    'assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.css',
    'css/dispatcher.css'
]}

<div class="dispatcher">

    <div class="lft">

        <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-user"></i></span>
                <input type="text" class="form-control" id="name" placeholder="Cliente">
                <span class="input-group-btn">
                    <button class="btn btn-default tooltips" title="Nuevo" data-placement="left" onclick="MClient.add();">
                        <i class="fa fa-plus font-grey-mint"></i>
                    </button>
                </span>
            </div>
        </div>

        <div class="form-group">


            <div class="panel-group accordion">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a class="accordion-toggle" data-toggle="collapse" href="#collapse_1_1"> Destinos del cliente </a>
                        </h4>
                    </div>
                    <div id="collapse_1_1" class="panel-collapse collapse in points">
                        <table class="table">
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>

        <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon tooltips" title="Origen" data-placement="right"><i class="fa fa-map-marker font-green-jungle"></i></span>
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

        <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon tooltips" title="Destino" data-placement="right"><i class="fa fa-map-marker font-red-flamingo"></i></span>
                <input type="text" class="form-control" placeholder="Destino" id="dst" data-lat="0" data-lng="0">
                <span class="input-group-btn">
                    <a class="btn btn-default search" style="padding:6px;border-left:none;border-right:none" onclick="AAdr.geocode(false)">
                        <i class="fa fa-search font-grey-mint font-sm"></i>
                    </a>
                </span>
                <span class="input-group-btn">
                    <a class="btn btn-default tooltips" style="padding:6px" title="Guardar&nbsp;favorito" data-placement="left" onclick="Race.savePoint(false)">
                        <i class="fa fa-heart font-grey-mint font-sm"></i>
                    </a>
                </span>
            </div>
        </div>

        <div class="form-group">
            <div class="btn-group btn-group-justified" data-toggle="buttons">
                <label class="btn btn-default active"><input type="radio" class="toggle" name="type" value="0" checked> Normal </label>
                <label class="btn btn-default"><input type="radio" class="toggle" name="type" value="1"> Ejecutivo </label>
                <label class="btn btn-default"><input type="radio" class="toggle" name="type" value="2"> Grande </label>
            </div>
        </div>

        <div class="form-group" >
            <div class="input-group">
                <span class="input-group-addon tooltips" title="Conductor asignado" data-placement="right">
                    <i class="fa fa-car"></i>
                </span>
                <input type="text" class="form-control" placeholder="Sin conductor" id="driver" readonly>
                <span class="input-group-btn">
                    <a class="btn btn-default search tooltips driver_mk" title="Ubicar&nbsp;en&nbsp;el&nbsp;mapa"
                       data-placement="left" style="padding:6px;border-left:none;border-right:none;display:none" onclick="Race.driverOnMap()">
                        <i class="fa fa-map-marker font-grey-mint font-sm"></i>
                    </a>
                </span>
                <span class="input-group-btn">
                    <a class="btn btn-default search tooltips driver_x" title="Eliminar&nbsp;conductor"
                       data-placement="left" style="padding:6px;display:none" onclick="Race.removeDriver()">
                        <i class="fa fa-times font-grey-mint font-sm"></i>
                    </a>
                </span>
            </div>
        </div>

        <div class="form-group clearfix">
            <div style="max-width:140px;float:left">
                <div class="input-group">
                    <span class="input-group-addon font-lg">{$stg->coin}</span>
                    <input type="text" class="form-control input-lg" placeholder="0.00" id="cost" readonly>
                </div>
            </div>
            <div style="line-height:46px;margin-left:5px;float:left;display:none" id="cost_load"><i class="fa fa-spinner fa-spin fa-1x fa-fw"></i></div>
            <div style="line-height:46px;margin-left:5px;float:left;display:none;color:#EF3F3F;font-size:12px" id="cost_msg">Fuera de cobertura</div>
        </div>

        <div class="form-group">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="prog" value="11" id="prog"> Servicio programado
                </label>
            </div>

            <div class="form-horizontal" id="prog_box" style="display:none">
                <div class="form-group">
                    <div class="col-md-7">
                        <input type="date" class="form-control" id="prog_date">
                    </div>
                    <div class="col-md-5">
                        <input type="time" class="form-control" id="prog_time">
                    </div>
                </div>
            </div>

        </div>

        <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-gift"></i></span>
                <input type="text" class="form-control" id="promo" placeholder="Código promocional">
            </div>
        </div>

        <div class="form-group">
            <div class="input-group">
                <span class="input-group-addon"><i class="fa fa-comments"></i></span>
                <textarea class="form-control" id="notes" placeholder="Notas para el conductor"></textarea>
            </div>
        </div>

        <div class="form-actions pull-right">
            <button type="submit" class="btn green-jungle" onclick="Race.add();">Crear servicio</button>
        </div>

    </div>

    <div class="rgt">
        <div id="map"></div>
    </div>

</div>
{*
"https://maps.googleapis.com/maps/api/js?key={$stg->key_maps}",
*}
{include file='_footer.tpl' js=[
"https://maps.googleapis.com/maps/api/js?key={$stg->key_maps}",
    'assets/global/plugins/jquery-ui-autocomplete/jquery-ui.min.js',
    'assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js',
    'js/mclient.js',
    'js/dispatcher.js'
]}