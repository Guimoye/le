{include file='_header.tpl' css=[
    'assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.css'
]}

<div class="row">

    <div class="col-md-6">

        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject font-dark bold uppercase">{$page_title}</span>
                </div>
                <div class="actions">
                    {if $can_settings}
                        <a class="btn btn-circle green btn-outline" onclick="saveSetting('stg_general');"> <i class="fa fa-check"></i> Guardar </a>
                    {/if}
                </div>
            </div>

            <div class="portlet-body">
                <form class="form-horizontal" id="stg_general">
                    <input type="hidden" name="action" value="general">
                    <div class="form-body">
                        <div class="form-group">
                            <label class="col-md-6 control-label">Marca</label>
                            <div class="col-md-6">
                                <input class="form-control" name="brand" value="{$stg->brand}" {if !$can_settings}readonly{/if}>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-6 control-label">Símbolo de moneda</label>
                            <div class="col-md-6">
                                <input class="form-control" name="coin" value="{$stg->coin}" {if !$can_settings}readonly{/if}>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-6 control-label">Tipo de cambio</label>
                            <div class="col-md-6">
                                <input class="form-control" name="tc" value="{$stg->tc}" {if !$can_settings}readonly{/if}>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-6 control-label">IP Principal</label>
                            <div class="col-md-6">
                                <input class="form-control" name="ip_local_server" value="{$stg->ip_local_server}" {if !$can_settings}readonly{/if}>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-6"></label>
                            <div class="col-md-6 bold"> Empresa </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-6 control-label">Razón Social</label>
                            <div class="col-md-6">
                                <input class="form-control" name="comp_name" value="{$stg->comp_name}" {if !$can_settings}readonly{/if}>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-md-6 control-label">RUC</label>
                            <div class="col-md-6">
                                <input class="form-control" name="comp_ruc" value="{$stg->comp_ruc}" {if !$can_settings}readonly{/if}>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>

</div>


{literal}
<script>

    function saveSetting(id_form){
        api('ajax/settings.php', $('#'+id_form).serializeObject(), function(rsp){
            if(rsp.ok){
                toastr.success('Guardado correctamente')
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Guardando ajustes...');
    }

    function $Ready(){
        $("input.stg").TouchSpin({
            verticalbuttons:!0,
            forcestepdivisibility:'none'
        });
    }
</script>

{/literal}

{include file='_footer.tpl' js=[
    'assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.js'
]}