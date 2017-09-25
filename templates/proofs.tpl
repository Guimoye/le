{include file='_header.tpl'}

<div class="row">

    <div class="col-md-7">

        <div class="portlet light">

            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject bold uppercase">{$page_title}</span>
                </div>
                <div class="actions">
                    {if $can_proofs}
                        <span class="btn btn-circle green btn-outline" onclick="MProof.add();"> <i class="fa fa-plus"></i> Nuevo </span>
                    {/if}
                </div>
            </div>

            <div class="portlet-body">

                {if empty($proofs)}
                    <div class="alert alert-warning">
                        No se han agregado comprobantes.
                    </div>
                {else}
                    <table class="table table-striped table-bordered table-hover dt-responsive">
                        <thead>
                        <tr>
                            <th width="1%"> # </th>
                            <th width="1%"> Código </th>
                            <th> Nombre </th>
                            <th width="155px"> Fecha de creación </th>
                            <th width="1%"></th>
                        </tr>
                        </thead>
                        <tbody id="pager_content">
                        {foreach key=i item=o from=$proofs}
                            <tr>
                                <td>{$o.id}</td>
                                <td>{$o.code}</td>
                                <td>{$o.name}</td>
                                <td>{$o.date_added|date_format:"%d-%m-%Y %I:%M %p"}</td>
                                <td>
                                    {if $can_proofs}
                                        <span class="btn btn-outline btn-circle dark btn-sm" onclick="MProof.edit(proofs[{$i}]);">
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

    </div>

</div>


<!-- MODAL UNIMED -->
<div class="modal fade" id="modal_add_proof" tabindex="-1" aria-hidden="true">
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
                        <label class="col-md-3 control-label">Código</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="code" placeholder="Código para el sistema contable">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-md-3 control-label">Nombre</label>
                        <div class="col-md-9">
                            <input type="text" class="form-control" name="name" placeholder="Nombre de comprobante">
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
<!-- MODAL UNIMED -->

<script>
    var proofs = {$proofs|@json_encode};

    function $Ready(){
        MProof.init();
        //MProof.add();
    }
</script>

{include file='_footer.tpl' js=[
    'js/m_proof.js?v=0.0.1'
]}