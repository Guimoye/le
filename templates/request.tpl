{include file='_header.tpl' css=[
    'assets/global/plugins/jquery-ui-autocomplete/jquery-ui.min.css',
    'assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.css'
]}
<div class="row noselect">

    <div class="col-xs-12 col-sm-5" id="rooms">

        <div class="portlet light">

            {if empty($rooms)}
                <div class="alert alert-warning"> No se han creado salas todavía. </div>
            {else}

                <div class="portlet-title tabbable-line">

                    <ul class="nav nav-tabs pull-left">
                        {foreach key=i item=o from=$rooms}
                            <li class="{if $i==0}active{/if}">
                                <a href="#room_{$o.id}" data-toggle="tab"> {$o.name} </a>
                            </li>
                        {/foreach}
                    </ul>

                    <div class="actions">
                        {*<button class="btn dark btn-circle reload" onclick="RQST.loadTables();" style="min-width:30px;padding-left:0;padding-right:0">
                            <i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i>
                        </button>*}
                        <button class="btn dark btn-circle reload" onclick="RQST.loadTables();" style="min-width:30px;padding-left:0;padding-right:0">
                            <i class="fa fa-refresh"></i>
                        </button>
                    </div>

                </div>

                <div class="portlet-body">
                    <div class="tab-content">
                        {foreach key=i item=o from=$rooms}
                            <div class="tab-pane {if $i==0}active{/if}" id="room_{$o.id}">
                                <div class="clearfix tables room_{$o.id}">

                                    <i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i> Obteniendo mesas...

                                    {*{if empty($o.tables)}
                                        <div class="alert alert-warning"> No hay mesas. </div>
                                    {else}
                                        {foreach key=x item=t from=$o.tables}
                                            <div class="item ready" id="table_{$t.id}" onclick="RQST.setTable({$t.id},'{$t.name}');">
                                                <span>{$t.name}</span>
                                                <b>0</b>
                                            </div>
                                        {/foreach}
                                    {/if}*}
                                </div>
                            </div>
                        {/foreach}
                    </div>
                </div>
            {/if}

        </div>

    </div>


    <div class="col-xs-12 col-sm-7" id="order" style="padding-left:10px;padding-right:10px;display:none">

        <div class="portlet light">

            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject uppercase">Categorías favoritas</span>
                </div>
            </div>
            <div class="portlet-body util-btn-margin-bottom-5">
                {foreach key=i item=o from=$categories_fav}
                    <a class="btn btn-circle font-white" style="background:{$o.color}" onclick="RQST.searchProducts({$o.id},'{$o.name}');">
                        {$o.name}
                    </a>
                {/foreach}

                <a class="btn btn-circle btn-default" onclick="RQST.chooseCategory();"> <i class="fa fa-plus"></i> Ver más </a>
            </div>
        </div>

        <div class="portlet light" id="products">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject uppercase prods_title">Productos</span>
                </div>



                <div class="form-inline margin-bottom-15 pull-right">
                    <div class="form-group">
                        <div class="input-icon right">
                            <i class="fa fa-search"></i>
                            <input type="text" class="form-control query" placeholder="Buscar producto...">
                        </div>
                    </div>
                    <div class="input-group" style="max-width:150px">
                        <input type="text" class="quantity ctr" placeholder="Cantidad" style="display:block">
                    </div>
                    {*<button type="submit" class="btn blue"><i class="fa fa-check"></i></button>*}
                </div>
            </div>
            <div class="portlet-body">

                <div class="list util-btn-margin-bottom-5">

                    {*<a class="btn btn-circle grey-gallery"> Ceviche </a>

                    <div class="btn-group">
                        <a class="btn btn-circle grey-gallery" data-toggle="dropdown">
                            Pescado <i class="fa fa-angle-down"></i>
                        </a>
                        <ul class="dropdown-menu">
                            <li> <a href="javascript:;"> Make admin </a> </li>
                        </ul>
                    </div>

                    <a class="btn btn-circle grey-gallery"> Lentejas </a>
                    <a class="btn btn-circle grey-gallery"> Chicharron </a>
                    <a class="btn btn-circle grey-gallery"> Chicharron </a>
                    <a class="btn btn-circle grey-gallery"> Lentejas </a>
                    <a class="btn btn-circle grey-gallery"> Ceviche </a>
                    <a class="btn btn-circle grey-gallery"> Lentejas </a>
                    <a class="btn btn-circle grey-gallery"> Chicharron </a>
                    <a class="btn btn-circle grey-gallery"> Chicharron </a>*}
                </div>

            </div>
        </div>

        <div class="portlet light" id="table_order">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject bold font-yellow-gold uppercase">
                        Mesa: <span class="tbl_ord_title">---</span>
                    </span>
                </div>
                <div class="actions">
                    <span class="btn btn-circle green-jungle add_note_tbl"> Egregar nota </span>
                    <span class="btn btn-circle btn-default enable_table"> Liberar mesa </span>
                </div>
            </div>
            <div class="portlet-body table-scrollable">

                <table class="table table-bordered mdl-td">
                    <thead>
                    <tr>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th width="140px">Cantidad</th>
                        <th>Importe</th>
                        <th colspan="2" width="1%">Opciones</th>
                    </tr>
                    </thead>
                    <tbody>
                    {*<tr>
                        <td> Ceviche de pescado </td>
                        <td> S/. 15.00 </td>
                        <td> <input type="text" name="quantity" class="quantity ctr" value="1"> </td>
                        <td> S/. 20.00 </td>
                        <td> <button class="btn btn-circle green-jungle btn-block"> <i class="fa fa-sticky-note"></i> </button> </td>
                        <td> <button class="btn btn-circle btn-default btn-block"> <i class="fa fa-trash"></i> </button> </td>
                    </tr>*}
                    </tbody>
                    <tfoot>
                    <tr>
                        <td colspan="3" class="uppercase"> Importe total </td>
                        <td> S/. <b class="total">0.00</b> </td>
                        <td colspan="2">
                            <button class="btn btn-circle green-jungle uppercase btn-block send">Enviar</button>
                        </td>
                    </tr>
                    </tfoot>
                </table>

            </div>
        </div>

    </div>

</div>

<script>

    var categories = {$categories|@json_encode};
    var products = {$products|@json_encode};

    function $Ready(){
        RQST.init();
        //MProduct.edit(products[0]);

        //chooseCategory();
    }
</script>

{include file='_footer.tpl' js=[
    'assets/global/plugins/jquery-ui-autocomplete/jquery-ui.min.js',
    'assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js',
    'js/m_request.js'
]}