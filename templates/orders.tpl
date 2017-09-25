{include file='_header.tpl' css=[
    'assets/global/plugins/jquery-ui-autocomplete/jquery-ui.min.css',
    'assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.css',
    'css/orders.css'
]}

<div class="row noselect">

    <div class="col-sm-12" id="acts">

        <div class="portlet light" style="padding:10px;margin-bottom:5px!important;">

            <div class="form-inline">

                <div class="form-group">
                    <select class="form-control id_area" style="min-width:200px">
                        {if empty($areas)}
                            <option value="">Sin Áreas de producción</option>
                        {else}
                            {foreach key=i item=o from=$areas}
                                <option value="{$o.id}" {if isset($smarty.cookies.id_area) && $smarty.cookies.id_area == $o.id}selected{/if}>{$o.name}</option>
                            {/foreach}
                        {/if}
                    </select>
                </div>

                <button class="btn dark reload" style="margin-left:20px;margin-right:20px">Actualizar</button>

                <button class="btn green show_dispatched">Ver despachados</button>
                <!--<button class="btn dark">Agrupar</button>-->

            </div>

        </div>

    </div>

    <div class="col-sm-12" id="dispatched" style="display:none">
        <div class="portlet light" style="padding:10px">

            <table class="table table-bordered mdl-td orders" style="margin:0">
                <thead>
                <tr>
                    <th width="1%">#</th>
                    <th width="155px">Hora de pedido</th>
                    <th width="155px">Hora de despacho</th>
                    <th width="1%">Mozo</th>
                    <th width="1%">Mesa</th>
                    <th>Descripción</th>
                    <th width="1%">Cant</th>
                    <th>Nota</th>
                    <th width="1%"> </th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td colspan="9"> <i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i> Sincronizando... </td>
                </tr>
                </tbody>
            </table>

        </div>
    </div>

    <div class="col-sm-6">

        <div class="portlet light" style="padding:10px">

            <table class="table table-bordered mdl-td orders" style="margin:0">
                <thead>
                <tr>
                    <th width="1%">#</th>
                    <th width="1%">Mesa</th>
                    <th width="1%">Mozo</th>
                    <th width="1%">N°</th>
                    <th>Descripción</th>
                    <th width="1%" class="ctr"> <i class="fa fa-clock-o"></i> </th>
                </tr>
                </thead>
                <tbody id="pendings">
                <tr style="position:relative">
                    <td colspan="6"> <i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i> Sincronizando... </td>
                </tr>
                </tbody>
            </table>

        </div>

    </div>

    <div class="col-sm-6" style="padding-left:0!important;">

        <div class="portlet light" style="padding:10px">

            <table class="table table-bordered mdl-td orders" style="margin:0">
                <thead>
                <tr>
                    <th width="1%">#</th>
                    <th width="1%">Mesa</th>
                    <th width="1%">Mozo</th>
                    <th width="1%">N°</th>
                    <th>Descripción</th>
                    <th width="1%" class="ctr"> <i class="fa fa-clock-o"></i> </th>
                </tr>
                </thead>
                <tbody id="preparing">
                <tr>
                    <td colspan="6"> <i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i> Sincronizando... </td>
                </tr>
                </tbody>
            </table>

        </div>

    </div>

</div>

<!-- MODAL OPTIONS PENDINGS -->
<div class="modal fade noselect" id="modal_opts_pdg" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" style="max-width:615px">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"> --- </h4>
            </div>
            <div class="modal-body ctr">

                <button class="btn bold btn-lg time dark" data-time="1"> <i class="fa fa-clock-o"></i> 1 </button>
                <button class="btn bold btn-lg time red" data-time="2"> <i class="fa fa-clock-o"></i> 2 </button>
                <button class="btn bold btn-lg time yellow-gold" data-time="3"> <i class="fa fa-clock-o"></i> 3 </button>
                <button class="btn bold btn-lg time blue" data-time="4"> <i class="fa fa-clock-o"></i> 4 </button>
                <button class="btn bold btn-lg time green" data-time="5"> <i class="fa fa-clock-o"></i> 5 </button>
                <button class="btn bold btn-lg dispatch green-jungle"> Despachar </button>
                <button class="btn bold btn-lg remove red-mint"> Eliminar </button>

            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- MODAL OPTIONS PENDINGS -->

<!-- MODAL OPTIONS PREPARING -->
<div class="modal fade noselect" id="modal_opts_prp" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
                <h4 class="modal-title"> --- </h4>
            </div>
            <div class="modal-body ctr">

                <button class="btn bold btn-lg dispatch green-jungle"> Despachar </button>
                <button class="btn bold btn-lg remove red-mint"> Eliminar </button>

            </div>
        </div>
        <!-- /.modal-content -->
    </div>
    <!-- /.modal-dialog -->
</div>
<!-- MODAL OPTIONS PREPARING -->

<script>

    var categories = {$categories|@json_encode};
    var products = {$products|@json_encode};

    function $Ready(){
        KTC.init();
        //MProduct.edit(products[0]);

        //chooseCategory();
    }

</script>

{include file='_footer.tpl' js=[
    'assets/global/plugins/jquery-ui-autocomplete/jquery-ui.min.js',
    'assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js',
    'assets/global/plugins/js.cookie.min.js',
    'js/ktc.js'
]}