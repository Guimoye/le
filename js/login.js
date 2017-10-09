/**
 * Paginador
 */
var Pager = {

    page: 1,
    hasNext: true,
    cache: true,

    items: [], // Array de items

    $filters: null,
    $content: null,

    $prev: null,
    $next: null,
    $curr: null,

    init: function(){
        this.$filters   = $('#filters');
        this.$content   = $('#pager_content');
        this.$prev      = $('.prev');
        this.$next      = $('.next');
        this.$curr      = $('.curr');

        if(this.$filters.data('cache') == false){
            this.cache = false;
        }

        this.$filters.submit(function(e){
            e.preventDefault();
            Pager.apply();
        });

        this.$filters.find('.apply').click(function(){
            Pager.apply();
        });

        this.$prev.click(function(){
            if(Pager.page > 1){
                --Pager.page;
                Pager.load();
            }
        });

        this.$next.click(function(){
            if(Pager.hasNext){
                ++Pager.page;
                Pager.load();
            }
        });

        this.load();
    },

    // Aplicar filtros
    apply: function(){
        Pager.page = 1;
        Pager.load();
    },

    load: function(){
        if(Pager.page <= 1){
            Pager.$prev.hide();
        } else {
            Pager.$prev.show();
        }

        Pager.$next.show();

        var filters = Pager.$filters.serializeObject();
        filters.page = Pager.page;
        filters.action = 'pager';

        Pager.$curr.html(Pager.page);

        api(Pager.$filters.attr('action'), filters, function(rsp){
            if(rsp.data != ''){
                Pager.hasNext = true;
                Pager.$content.html(rsp.data);

                // Guardar items
                if(typeof rsp.items !== 'undefined'){
                    Pager.items = rsp.items;
                } else {
                    console.error('pager api: no hay items', rsp);
                }
            } else {
                Pager.hasNext = false;
                Pager.$content.html('<tr><td colspan="7">No hay datos</td></tr>');
                Pager.$next.hide();
            }
        }, 'Obteniendo resultados...', Pager.cache);

    }

};


$(document).ready(function(){
    Pager.init();

    $(document.documentElement).keyup(function(e){
        if(e.keyCode == 37) Pager.$prev.click();
        if(e.keyCode == 39) Pager.$next.click();
    });
});