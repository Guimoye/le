{include file='_header.tpl' css=[
    'assets/pages/css/profile.min.css'
]}

<style>
    .left_infos{ padding:10px 15px }
    .left_infos ._t{ font-size:14px; color:#8A9AAE; margin-top:10px }
</style>

<div class="row">
    <div class="col-md-12">
        <!-- BEGIN PROFILE SIDEBAR -->
        <div class="profile-sidebar">
            <!-- PORTLET MAIN -->
            <div class="portlet light profile-sidebar-portlet ">
                <!-- SIDEBAR USERPIC -->
                <div class="profile-userpic">
                    <img src="img/ph_person.png" class="img-responsive" alt=""> </div>
                <!-- END SIDEBAR USERPIC -->
                <!-- SIDEBAR USER TITLE -->
                <div class="profile-usertitle">
                    <div class="profile-usertitle-name"> {$driver->name} {$driver->surname} </div>
                    <!--<div class="profile-usertitle-job"> Developer </div>-->
                </div>
                <!-- END SIDEBAR USER TITLE -->

                <div class="left_infos">

                    <div class="_t">Fecha de inicio</div>
                    <div class="_c">
                        {if $rental_started}
                            {$driver->rental_date_formated} (hace {$driver->rental_age_weeks} semanas)
                        {else}
                            No iniciado
                        {/if}
                    </div>

                    <div class="_t">Placa</div>
                    <div class="_c">{$driver->vh_plate}</div>

                    <div class="_t">Plazo Alquiler</div>
                    <div class="_c">
                        {if $rental_started}
                            {$driver->rental_dues} semanas (días {$driver->rental_date_day})
                        {else}
                            No iniciado
                        {/if}
                    </div>

                    <div class="_t">IMEI del GPS</div>
                    <div class="_c">{$driver->vh_gps_number}</div>

                    <div class="_t">Número de cuenta "{$driver->bank_name}"</div>
                    <div class="_c">{$driver->bank_account}</div>

                </div>

            </div>
            <!-- END PORTLET MAIN -->
            <!-- PORTLET MAIN -->
            <div class="portlet light ">

                <!-- SIDEBAR BUTTONS -->
                <div class="profile-userbuttons">
                    <button type="button" class="btn btn-circle red btn-sm">Finalizar conductor</button>
                </div>
                <!-- END SIDEBAR BUTTONS -->

            </div>
            <!-- END PORTLET MAIN -->
        </div>
        <!-- END BEGIN PROFILE SIDEBAR -->
        <!-- BEGIN PROFILE CONTENT -->
        <div class="profile-content">

            <div class="portlet light ">
                <div class="portlet-body">
                    <div class="table-scrollable table-scrollable-borderless">
                        <table class="table table-hover">
                            <thead>
                            <tr>
                                <th> Modulos </th>
                                <th> </th>
                                <th width="1%"> Última actualización </th>
                                <th width="1%"> </th>
                            </tr>
                            </thead>
                            <tbody>

                            <tr>
                                <td>
                                    Cronograma de Alquiler
                                    <div class="progress" style="height:5px;border-radius:2.5px!important;overflow:hidden;margin-top:8px;margin-bottom:0">
                                        <div class="progress-bar progress-bar-info" style="width: {$rental->percent}%"></div>
                                    </div>
                                </td>
                                <td>
                                    <b>{$stg->coin}{$rental->total_paid|string_format:"%.2f"}</b> / {$stg->coin}{$rental->total_due|string_format:"%.2f"}
                                    <br>
                                    {if $rental->weeks_late > 0}
                                        <span class="badge badge-danger">Retraso de {$rental->weeks_late} semanas</span>
                                    {else}
                                        <span class="font-sm font-green-jungle">Pagos al día</span>
                                    {/if}
                                </td>
                                <td> {$rental->last_date_paid} </td>
                                <td>
                                    <a href="dues_rental.php?id={$driver->id}" class="btn btn-circle green-jungle">
                                        <i class="fa fa-paper-plane"></i> Ver detalle
                                    </a>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    Cronograma de Venta
                                    <div class="progress" style="height:5px;border-radius:2.5px!important;overflow:hidden;margin-top:8px;margin-bottom:0">
                                        <div class="progress-bar progress-bar-info" style="width: {$sale->percent}%"></div>
                                    </div>
                                </td>
                                <td>
                                    <b>{$stg->coin}{$sale->total_paid|string_format:"%.2f"}</b> / {$stg->coin}{$sale->total_due|string_format:"%.2f"}
                                    <br>
                                    {if $sale->weeks_late > 0}
                                        <span class="badge badge-danger">Retraso de {$sale->weeks_late} semanas</span>
                                    {else}
                                        <span class="font-sm font-green-jungle">Pagos al día</span>
                                    {/if}
                                </td>
                                <td> {$sale->last_date_paid} </td>
                                <td>
                                    <a href="dues_sale.php?id={$driver->id}" class="btn btn-circle green-jungle">
                                        <i class="fa fa-paper-plane"></i> Ver detalle
                                    </a>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    Gastos
                                    <div class="font-grey-salsa font-sm">
                                        Actualmente {$expenses->total_items} registros
                                    </div>
                                </td>
                                <td>
                                    <b>{$stg->coin}{$expenses->last_amount|string_format:"%.2f"}</b>
                                    <br>
                                    {if $expenses->weeks_late > 0}
                                        <span class="badge badge-danger">Retraso de {$expenses->weeks_late} semanas</span>
                                    {else}
                                        <span class="font-sm font-green-jungle">Pagos al día</span>
                                    {/if}
                                </td>
                                <td> {$expenses->last_date_pay} </td>
                                <td>
                                    <a href="expenses.php?id={$driver->id}" class="btn btn-circle green-jungle">
                                        <i class="fa fa-paper-plane"></i> Ver detalle
                                    </a>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
        <!-- END PROFILE CONTENT -->
    </div>
</div>


{literal}
<script>
    function $Ready(){

    }
</script>
{/literal}

{include file='_footer.tpl' js=[
    'assets/pages/scripts/profile.min.js'
]}