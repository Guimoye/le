{include file='_header.tpl' css=[
    'assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.css'
]}

<div class="portlet light">

    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject font-dark bold uppercase">{$page_title}</span>
            <br><a class="caption-helper" href="drivers/{$driver->id}">{$driver->name} {$driver->surname}</a>
        </div>
        <div class="actions">
            <!--span class="btn btn-circle blue" onclick="MDuesLoans.add();"> <i class="fa fa-plus"></i> Registrar </span>-->
        </div>
    </div>

    <div class="portlet-body">

        <!--<div class="date-picker" data-date-format="mm/dd/yyyy"></div>-->

        {if empty($items)}
            <div class="alert alert-warning">
                No hay datos disponibles.
            </div>
        {else}
            <table class="table table-striped table-bordered table-hover dt-responsive mdl-td" style="margin-top:10px">
                <thead>
                <tr>
                    <th width="1%"> # </th>
                    <th width="1%"> Fecha </th>
                    <th> Monto </th>
                    <th> Anterior </th>
                    <th> Monto Total </th>
                    <th> Monto Pagado </th>
                    <th width="1%" class="nowrap"> Fecha de pago </th>
                    <th width="1%"></th>
                </tr>
                </thead>
                <tbody id="pager_content">
                {foreach key=i item=o from=$items}
                    <tr>
                        <td> {$i+1} </td>
                        <td class="nowrap"> {$o.date_due|date_format:"%d-%m-%Y"} </td>
                        <td> {$stg->coin}{$o.amount_due|string_format:"%.2f"} </td>
                        <td> {$stg->coin}{$o.amount_previous|string_format:"%.2f"} </td>
                        <td> {$stg->coin}{$o.amount_total|string_format:"%.2f"} </td>
                        <td> {$stg->coin}{$o.amount_paid|string_format:"%.2f"} </td>
                        <td class="nowrap">
                            {$o.date_paid}
                            {if $o.pay_state == 'paid'}

                                {if $o.all_paid}
                                    <span {if $can_edit}onclick="MDuesLoansPay.open(items[{$i}]);"{/if}
                                            class="btn btn-xs green-jungle">Pagado</span>
                                {else}
                                    <span {if $can_edit}onclick="MDuesLoansPay.open(items[{$i}]);"{/if}
                                            class="btn btn-xs green">Pago parcial</span>
                                {/if}

                            {elseif $o.pay_state == 'pending'}
                                <span {if $can_edit}onclick="MDuesLoansPay.open(items[{$i}]);"{/if}
                                      class="btn btn-xs yellow-crusta">Pendiente</span>

                            {elseif $o.pay_state == 'expired'}
                                <span {if $can_edit}onclick="MDuesLoansPay.open(items[{$i}]);"{/if}
                                      class="btn btn-xs red-mint">Vencido</span>

                            {/if}
                        </td>
                        <td class="nowrap">

                            {if $can_edit}
                            {/if}

                        </td>
                    </tr>
                {/foreach}


                <tr style="background:#e7ecf1">
                    <td colspan="2"></td>
                    <th>{$stg->coin}{$total_amount_due}</th>
                    <td colspan="100%"></td>
                </tr>

                </tbody>
            </table>
        {/if}

    </div>

</div>

<!-- MODAL -->
<div id="modal_pay_dues_loans" class="modal fade modal-scroll" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"></button>
                <h4 class="modal-title">---</h4>
            </div>
            <div class="modal-body">

                <form class="form-horizontal">
                    <input type="hidden" name="id" value="">

                    <div class="form-group">
                        <label class="col-md-4 control-label">Monto a pagar</label>
                        <div class="col-md-6">
                            <div class="input-group">
                                <span class="input-group-addon">{$stg->coin}</span>
                                <input class="form-control" name="amount" placeholder="0.00">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-md-4 control-label">Fecha de pago</label>
                        <div class="col-md-6">
                            <input class="form-control" name="date_paid" type="date">
                        </div>
                    </div>

                </form>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn red pull-left remove hide">Eliminar</button>
                <button type="button" data-dismiss="modal" class="btn btn-outline btn-default cancel">Cancelar</button>
                <button type="button" class="btn blue save">Generar</button>
            </div>
        </div>
    </div>
</div>
<!-- END MODAL -->

<script>
var items = {$items|@json_encode};
{literal}
    function $Ready(){
        //MDuesLoansPay.open(items[0]);
    }
{/literal}
</script>

{include file='_footer.tpl' js=[
    'assets/global/plugins/bootstrap-fileinput/bootstrap-fileinput.js',
    'assets/global/plugins/jquery.form.min.js',
    'views/js/m_dues_loans.js'
]}