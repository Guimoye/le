{include file='_header.tpl'}

<div class="row">

    <div class="col-md-6">

        <div class="portlet light">
            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject font-dark bold uppercase">{$page_title}</span>
                </div>
                <div class="actions"></div>
            </div>

            <div class="portlet-body">

                <form class="form-horizontal" id="form_open_box">

                    <div class="form-group action">
                        <label class="col-md-4 control-label">Motivo</label>
                        <div class="col-md-8">
                            <select name="action" class="form-control">
                                <option value="open">Apertura</option>
                                <option value="close">Cierre</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group id_box">
                        <label class="col-md-4 control-label">Caja</label>
                        <div class="col-md-8">
                            <select name="id_box" class="form-control">
                                {if empty($boxes)}
                                    <option value="">¡No hay cajas!</option>
                                {else}
                                    {foreach item=o from=$boxes}
                                        <option value="{$o.id}">{$o.name}</option>
                                    {/foreach}
                                {/if}
                            </select>
                        </div>
                    </div>

                    <div class="form-group id_turn">
                        <label class="col-md-4 control-label">Turno</label>
                        <div class="col-md-8">
                            <select name="id_turn" class="form-control">
                                {if empty($turns)}
                                    <option value="">¡No hay turnos!</option>
                                {else}
                                    {foreach item=o from=$turns}
                                        <option value="{$o.id}">{$o.name}</option>
                                    {/foreach}
                                {/if}
                            </select>
                        </div>
                    </div>

                    <div class="form-group amount">
                        <label class="col-md-4 control-label">Monto</label>
                        <div class="col-md-8">
                            <input type="number" name="amount" value="0" class="form-control">
                        </div>
                    </div>

                    <div class="form-group notes">
                        <label class="col-md-4 control-label">Notas</label>
                        <div class="col-md-8">
                            <textarea name="notes" class="form-control" placeholder="(opcional)"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-md-offset-4 col-md-6">
                            <button type="submit" class="btn blue btn-circle">Guardar</button>
                        </div>
                    </div>

                </form>

            </div>
        </div>

    </div>

</div>

<script>

    function $Ready(){

        var $form = $('#form_open_box');
        $form.action = $form.find('.action select');
        $form._amount = $form.find('.amount');
        $form._notes = $form.find('.notes');

        $form.action.change(function(){
            if(this.value == 'close'){
                $form._amount.fadeOut();
                $form._notes.fadeOut();
            } else {
                $form._amount.fadeIn();
                $form._notes.fadeIn();
            }
        });

        $form.submit(function(e){
            e.preventDefault();
            api('ajax/box.php', $form.serializeObject(), function(rsp){
                if(rsp.ok){
                    toastr.success('Guardado correctamente');
                    if(stg.return!=''){
                        location.href = stg.return;
                    }
                } else {
                    bootbox.alert(rsp.msg);
                }
            }, 'Guardando...');
        });

    }

</script>

{include file='_footer.tpl'}