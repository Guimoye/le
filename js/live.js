
var Live = {

    time_update: 5000,

    // Badge drivers
    $bdg_all: 		null,
    $bdg_active: 	null,
    $bdg_busy: 		null,
    $bdg_inactive: 	null,

    // Panics
    $panic_number: 	null,

    init: function(){
        this.$bdg_all 		= $('#bdg_all');
        this.$bdg_active 	= $('#bdg_active');
        this.$bdg_busy 		= $('#bdg_busy');
        this.$bdg_inactive	= $('#bdg_inactive');
        this.$panic_number	= $('.page-sidebar-menu .panic .badge');
        //this.loadData();//TODO: Habilitar

        // Eventos
        $('.head-drivers input:radio[name=show_only]').change(function(){
            if(current_page == 'taxi-request'){
                Race.showOnly(this.value);
            }
        });
        $('.head-drivers .btn-group').click(function(){
            if(current_page != 'taxi-request'){
                MapBox.open();
            }
        });
    },

    loadData: function(){
        api('ajax/live.php', function(rsp){
            setTimeout(Live.loadData, Live.time_update); // Ejecutar nuevamente
            //console.log('rsp: ', rsp);

            Live.dataDrivers(rsp.drivers);

        }, false);
    },

    dataDrivers: function(data){
        Live.$bdg_all.text(data.total);
        Live.$bdg_active.text(data.total_active);
        Live.$bdg_busy.text(data.total_busy);
        Live.$bdg_inactive.text(data.total_inactive);
        if(typeof Task === 'object'){
            Task.maps.setDrivers(data);
        } else {
            //MapBox.setTaxis(data);
        }
    }

};