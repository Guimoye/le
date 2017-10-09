{include file='_header.tpl'}

<div class="portlet light">

    <div class="portlet-title">
        <div class="caption">
            <span class="caption-subject bold uppercase">{$page_title}</span>
        </div>
        <div class="actions">
            {if $can_users}
                <span class="btn btn-circle green btn-outline" onclick="MUser.add();"> <i class="fa fa-plus"></i> Nuevo </span>
            {/if}
        </div>
    </div>

    <div class="portlet-body">

        <!-- FILTERS -->
        <form class="form-inline" id="filters" action="users/pager">
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
                <input class="form-control" name="word" placeholder="nombre,celular"/>
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
                    {foreach key=key item=val from=$st_user}
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
                <th width="1%"> Usuario </th>
                <th> Teléfono </th>
                <th width="1%"> Nivel </th>
                <th width="155px"> Fecha de registro </th>
                <th width="1%"> Estado </th>
                <th width="1%"></th>
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

{literal}
<script>
    function $Ready(){
        MUser.init();
        //MUser.add();
    }
</script>
{/literal}

{include file='_footer.tpl' js=[
    'js/m_user.js',
    'js/pager.js'
]}