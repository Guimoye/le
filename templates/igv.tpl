{include file='_header.tpl' css=[
    'assets/global/plugins/bootstrap-touchspin/bootstrap.touchspin.css'
]}

<div class="row">

    <div class="col-md-5">

        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject font-dark bold uppercase">{$page_title}</span>
                </div>
                <div class="actions">
                    {if $can_igv}
                        <a class="btn btn-circle green btn-outline" onclick="saveSetting('stg_general');"> <i class="fa fa-check"></i> Guardar </a>
                    {/if}
                </div>
            </div>

            <div class="portlet-body">
                <form class="form-horizontal" id="stg_general">
                    <input type="hidden" name="action" value="igv">
                    <div class="form-body">
                        <div class="form-group">
                            <label class="col-md-4 control-label">IGV</label>
                            <div class="col-md-8" style="max-width:192px">
                                <input type="text" class="form-control stg" name="igv" value="{$stg->igv}"
                                       data-postfix="%" data-min="0" data-step="1">
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