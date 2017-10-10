{include page_title='Error' file='_header.tpl' css=['assets/pages/css/error.min.css']}

<div class="row">
    <div class="col-md-12 page-404">
        <div class="number font-green"> :( </div>
        <div class="details">
            <h3>¡Vaya! Estas perdido.</h3>
            <p>
                Se ha producido un error al solicitar la página.
                <br/>
                <a href="{$stg->url_cms}"> Regresar al inicio</a>.
                <br>
                <em style="color:#CCC">{$text}</em>
            </p>
        </div>
    </div>
</div>

{include file='_footer.tpl'}