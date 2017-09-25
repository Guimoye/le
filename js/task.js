// Icons
var ic_mk_home		    = 'img/mk_home.png?v=2',
    ic_mk_default		= 'img/mk_default.png',
    ic_mk_dvr_active 	= 'img/mk_dvr_active.png',
    ic_mk_dvr_busy 		= 'img/mk_dvr_busy.png',
    ic_mk_dvr_inactive	= 'img/mk_dvr_inactive.png',
    ic_mk_dvr_selected 	= 'img/mk_dvr_selected.png';


// Task dispatcher
function tMapsCallback(){Task.maps.mapLoaded()}
var Task = {

    title_add:  'Nuevo despacho',
    title_edit: 'Editar conductor',

    $modal: null,
    $title: null, // Modal: Titulo
    $form: null, // Modal: Formulario

    $driver: null,
    $name: null,
    $org: null,
    $dst: null,
    $prog: null,
    $notes: null,

    $remove: null,

    // DATA
    id: 0,
    id_client: 0,
    id_driver: 0,
    org: null,
    dst: null,

    init: function(){
        this.$modal = $('.dispatch');
        this.$title = $('.modal-title', this.$modal);
        this.$form  = $('form', this.$modal);

        this.$name          = $('#name', this.$modal);

        this.$driver        = $('.driver', this.$modal);
        this.$driver.name   = $('#driver', this.$driver);
        this.$driver.locate = $('.driver_mk', this.$driver);

        this.$org           = $('.org', this.$modal);
        this.$org.adr       = $('#org', this.$org);

        this.$dst           = $('.dst', this.$modal);
        this.$dst.adr       = $('#dst', this.$dst);
        this.$dst.search    = $('.dst_search', this.$dst);
        this.$dst.add       = $('.dst_add', this.$dst);

        this.$prog          = $('.prog', this.$modal);
        this.$prog.date = $('.prog_date', this.$prog);
        this.$prog.time = $('.prog_time', this.$prog);

        this.$notes          = $('#notes', this.$modal);

        this.$remove        = $('.remove', this.$modal);

        // Asignar eventos
        this.$remove.click(function(){
            Task.remove(Task.$id.val());
        });

        this.$dst.search.click(this.aAdr.geocode);
        this.$dst.add.click(this.saveDstClient);
        this.$dst.loading = function(show){
            Task.$dst.search.html(show ? '<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i>' : '<i class="fa fa-search"></i>');
        };

        $('.cancel', this.$modal).click(this.cancel);
        $('.save', this.$modal).click(this.save);

        this.$modal.on('shown.bs.modal', this.maps.init); // iniciar mapas cuando el modal este habierto, para mostrar

        this.aClient.init();
        this.aDriver.init();
        this.aAdr.init();
        MClient.init(this.aClient.assign);

        // Direccion de empresa como Org
        if(stg.lat != '0' && stg.lat != '0' && stg.adr != ''){
            this.org = {lat: parseFloat(stg.lat), lng: parseFloat(stg.lng), adr: stg.adr};
        }

    },

    cancel: function(){
        Task.$modal.modal('hide');
    },

    /**
     * Guardar cambios o Modificarlos
     */
    save: function(){

        var data = {
            action:     'request',
            id:         Task.id,
            id_client:  Task.id_client,
            id_driver:  Task.id_driver,
            name:       Task.$name.val(),
            org:        Task.org,
            dst:        Task.dst,
            notes:      Task.$notes.val(),
            prog_date:  Task.$prog.date.val(),
            prog_time:  Task.$prog.time.val()
        };
        
        if(Task.org != null){
            Task.org.adr = Task.$org.adr.val();
        }
        
        if(Task.dst != null){
            Task.dst.adr = Task.$dst.adr.val();
        }

        console.log('data:', data);

        api('ajax/dispatch.php', data, function(rsp){
            if(rsp.ok == true){
                toastr.success('Guardado correctamente');

                Task.id = rsp.id;

            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Registrando...');

    },

    // Guardar destino en casa del cliente
    saveDstClient: function(){
        var data = Task.dst;
        if(data != null){
            data.action = 'save_point';
            data.id = Task.id_client;
            data.adr = Task.$dst.adr.val();
            api('ajax/clients.php', data, function(rsp){
                if(rsp.ok){
                    toastr.success('Guardado correctamente');
                } else {
                    bootbox.alert(rsp.msg);
                }
            }, 'Guardando dirección');
        } else {
            bootbox.alert('Dirección incorrecta');
        }
    },

    geocode: function(){
        alert('Geocode dst');
    },
    
    // Abrir para nuevo NUEVO
    add: function(){
        Task.clear();
        Task.$modal.modal('show');
    },
    
    // Editar
    edit: function(id){

        Task.clear();

        api('ajax/dispatch.php', {action:'get_task', id:id}, function(rsp){
            if(rsp.ok){
                var t = rsp.task;

                Task.id = t.id;
                Task.$name.val(t.name);

                if(t.client){
                    Task.aClient.assign(t.client);
                }

                if(t.driver){
                    Task.aDriver.assign(t.driver);
                }

                if(t.dst_lat != '0' && t.dst_lng != '0'){
                    Task.dst = {lat: parseFloat(t.dst_lat), lng: parseFloat(t.dst_lng)};
                    Task.$dst.adr.val(t.dst_adr);
                    //Task.maps.setPos();
                }

                Task.$prog.date.val(t.prog_date);
                Task.$prog.time.val(t.prog_time);

                Task.$notes.val(t.notes);

                Task.$modal.modal('show');
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Obteniendo información...');
    },

    // Limpiar campos
    clear: function(){
        Task.$title.text(Task.title_add);
        Task.id = 0;
        Task.aClient.clean();
        Task.aDriver.clean();
        Task.$name.val('');
        Task.dst = null;
        Task.$dst.adr.val('');
        Task.$prog.date.val('');
        Task.$prog.time.val('');
        Task.$notes.val('');
        Task.$remove.hide();
    },

    // Eliminar
    remove: function(id){
        bootbox.confirm('¿Realmente desea eliminar?', function(result){
            if(result){
                api('ajax/drivers.php', {action:'remove', id:id}, function(rsp){
                    if(rsp.ok == true){
                        toastr.success('Eliminado correctamente');
                        location.reload();
                    } else {
                        bootbox.alert(rsp.msg);
                    }
                }, 'Eliminando...');
            }
        });
    },

    /**
     * Autocompletado de cliente
     */
    aClient: {

        client: {},

        $client: null,

        init: function(){

            this.$client        = $('.client');
            this.$client.name   = $('.cli_name', this.$client);
            this.$client.info   = $('._cl_info', this.$client);
            this.$client.phone  = $('.phone', this.$client);
            this.$client.email  = $('.email', this.$client);
            this.$client.edit   = $('.cl_edit', this.$client);
            this.$client.edit.click(function(){
                MClient.edit(Task.aClient.client);
            });

            this.$client.name.autocomplete({
                //source: "ajax/autocomplete-clients.php",
                source: function(data, response){
                    data.action = 'autocomplete';
                    api('ajax/clients.php', data, response, false, true);
                },
                minLength: 1,
                select: function(event, ui) {
                    Task.aClient.assign(ui.item, true);
                    return false;
                },
                create: function () {
                    $(this).data('ui-autocomplete')._renderItem = function(ul, item){
                        console.log('create: ', item);
                        return $("<li>").append('<a>' + item.name + ' ' + item.surname + '</a>').appendTo(ul);
                    }
                }
            });


            this.$client.name.keypress(this.clean);
            this.$client.name.keyup(function(e){ if(e.keyCode == 8) Task.aClient.clean(); });
        },

        assign: function(item, changePos){
            Task.id_client = item.id;

            Task.aClient.client = item;

            //Task.aClient.$name.val(item.name + ' ' + item.surname);

            Task.aClient.$client.name.val(item.name + ' ' + item.surname);
            Task.aClient.$client.phone.html(item.phone);
            Task.aClient.$client.email.html(item.email);
            Task.aClient.$client.info.fadeIn();

            if(changePos && item.lat != '0' && item.lng != '0' && item.adr != ''){
                Task.dst = {lat:parseFloat(item.lat), lng:parseFloat(item.lng), adr:item.adr};
                Task.$dst.adr.val(item.adr);
                Task.maps.mkDst.setPosition(Task.dst);
                Task.maps.map.setCenter(Task.dst);
                Task.maps.setPos();
            }
        },

        clean: function(){
            if(Task.id_client > 0){
                Task.id_client = 0;
                Task.aClient.$client.name.val('');
                Task.aClient.$client.info.fadeOut();
            }
        }
    },

    /**
     * Autocompletado de conductores
     */
    aDriver: {

        init: function(){

            Task.$driver.name.autocomplete({
                //source: "ajax/autocomplete-clients.php",
                source: function(data, response){
                    data.action = 'autocomplete';
                    api('ajax/drivers.php', data, response, false, true);
                },
                minLength: 1,
                select: function(event, ui) {
                    Task.aDriver.assign(ui.item);
                    return false;
                },
                create: function () {
                    $(this).data('ui-autocomplete')._renderItem = function(ul, item){
                        console.log('create: ', item);
                        return $("<li>").append('<a>' + item.name + ' ' + item.surname + '</a>').appendTo(ul);
                    }
                }
            });

            Task.$driver.locate.click(function(){
                alert('ubicar driver');
            });

            Task.$driver.name.keypress(this.clean);
            Task.$driver.name.keyup(function(e){ if(e.keyCode == 8) Task.aDriver.clean(); });
        },

        assign: function(item){
            Task.id_driver = item.id;
            Task.$driver.name.val(item.name + ' ' + item.surname);
        },

        clean: function(){
            if(Task.id_driver > 0){
                Task.id_driver = 0;
                Task.$driver.name.val('');
            }
        }
    },

    /**
     * Autocompletar direccion
     */
    aAdr: {
        init: function(){
            Task.$dst.adr.autocomplete({
                source: function(data, response){
                    data.query = data.term;
                    data.lat = -12.08421;
                    data.lng = -77.00354;

                    //$search.html('<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i>');

                    api('api/ext/autocomplete-address.php', data, response, false, true);
                },
                minLength: 1,
                select: function(event, ui){
                    Task.aAdr.getAddressDetails(ui.item.content, ui.item.reference, $(this));
                    return false;
                },
                create: function(){
                    $(this).data('ui-autocomplete')._renderItem = function(ul, item){
                        return $("<li>").append('<a>' + item.content + '</a>').appendTo(ul);
                    }
                }
            });
        },

        getAddressDetails: function(adr, ref, $ipt){
            var isOrg = ($ipt.attr('id') == 'org');
            //var $search = $field.parent().find('.search');
            //$search.html('<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i>');
            if(isOrg){
                Task.$org.loading(true);
            } else {
                Task.$dst.loading(true);
            }
            api('api/ext/address-details.php', {ref:ref}, function(pos){
                //$search.html('<i class="fa fa-search"></i>');
                $ipt.val(adr);
                Task.maps.map.setCenter(pos);
                if(isOrg){
                    Task.$org.loading(false);
                    Task.maps.mkOrg.setPosition(pos);
                    Task.org = pos;
                } else {
                    Task.$dst.loading(false);
                    Task.maps.mkDst.setPosition(pos);
                    Task.dst = pos;
                }
                Task.maps.setPos();
            }, false, true);
        },

        geocode: function(isOrg){
            var $ipt = !isOrg ? Task.$org : Task.$dst;
            var adr = $ipt.adr.val();
            var data = (adr != '') ? {adr:adr} : Task.maps.getCenter();

            $ipt.loading(true);
            api('api/ext/geocode.php', data, function(rsp){
                var pos = {lat:rsp.lat, lng:rsp.lng};
                if(!isOrg){
                    Task.maps.mkOrg.setPosition(pos);
                    Task.org = pos;
                } else {
                    Task.maps.mkDst.setPosition(pos);
                    Task.dst = pos;
                }
                $ipt.loading(false);
                $ipt.adr.val(rsp.adr);
                Task.maps.setPos();
            }, false, true);
        }
    },

    /**
     * Funciones de mapas
     */
    maps: {

        map: null,
        infoWindow: null,
        dService: null,
        dDisplay: null,
        mkOrg: null, // Marker Origen
        mkDst: null, // Marker Destino

        init: function(){
            console.log('Task.maps.init():::');

            if(Task.maps.map != null){ // Ya tenemos el map
                Task.maps.mapReady();
            } else if(typeof google === 'object' && typeof google.maps === 'object'){ // El mapa fue cargado previamente
                Task.maps.mapLoaded();
            } else { // Debemos cargar el mapa de cerp via URL
                Task.maps.loadMap();
            }
        },

        loadMap: function(){
            console.log('maps.loadMap');
            js('https://maps.googleapis.com/maps/api/js?key='+stg.key_maps+'&callback=tMapsCallback');
        },

        mapLoaded: function(){
            console.log('maps.mapLoaded');

            Task.maps.map = new google.maps.Map(document.getElementById('dsp_map'), {
                zoom: 13,
                center: {lat: -12.107085497712584, lng: -77.00320004790461}
            });

            Task.maps.dService = new google.maps.DirectionsService;
            Task.maps.dDisplay = new google.maps.DirectionsRenderer({
                map: Task.maps.map,
                suppressMarkers: true
            });

            Task.maps.infoWindow = new google.maps.InfoWindow();

            google.maps.event.addListener(Task.maps.map, 'click', function () {
                Task.maps.infoWindow.close();
            });

            Task.maps.mkOrg = new google.maps.Marker({
                map: Task.maps.map,
                draggable: false,
                animation: google.maps.Animation.DROP,
                icon: ic_mk_home
                //label: 'A'
            });
            if(Task.org!=null) Task.maps.mkOrg.setPosition(Task.org);
            //Task.maps.mkOrg.addListener('dragend', Task.maps.movedOrg);

            Task.maps.mkDst = new google.maps.Marker({
                map: Task.maps.map,
                draggable: true,
                animation: google.maps.Animation.DROP,
                label: 'B'
            });
            Task.maps.mkDst.addListener('dragend', Task.maps.movedDst);

            Task.maps.mapReady();
        },

        mapReady: function(){
            console.log('maps.mapReady');

            google.maps.event.trigger(Task.maps.map, 'resize');

            Task.maps.updateDrivers();
            if(Task.dst!=null){
                Task.maps.mkDst.setPosition(Task.dst);
                Task.maps.setPos();
            }
        },

        // Origen movido
        movedOrg: function(e){
            Task.org = {lat: e.latLng.lat(), lng: e.latLng.lng()};
            //Task.$org.loading(true);
            api('api/ext/geocode.php', Task.org, function(rsp){
                //Task.$org.loading(false);
                Task.$org.adr.val(rsp.adr);
            }, false, true);
            Task.maps.setPos();
        },

        // Destino movido
        movedDst: function(e){
            Task.dst = {lat: e.latLng.lat(), lng: e.latLng.lng()};
            Task.$dst.loading(true);
            api('api/ext/geocode.php', Task.dst, function(rsp){
                Task.$dst.loading(false);
                Task.$dst.adr.val(rsp.adr);
            }, false, true);
            Task.maps.setPos();
        },

        // Se llama cuando los puntos son actualizados
        setPos: function(){
            if(Task.dst == null) return;
            if(Task.org == null) return;

            Task.maps.calcRoute();
        },

        // Calcular ruta
        calcRoute: function(){
            Task.maps.dService.route({
                    origin: Task.org,
                    destination: Task.dst,
                    travelMode: google.maps.TravelMode.DRIVING,
                    avoidTolls: true
                }, function(response, status) {
                    if (status == google.maps.DirectionsStatus.OK){
                        Task.maps.dDisplay.setDirections(response);
                    } else {
                        console.warn('Directions request failed due to', status);
                    }
                }
            );
        },

        getCenter: function(){
            var ctr = Task.maps.map.getCenter();
            return {lat:ctr.lat(), lng:ctr.lng()};
        },

        show_only: 'all',
        update_bounds: true,
        markers: {},
        drivers: {},


        setDrivers: function(dvrs){
            Task.maps.markers = dvrs;
            Task.maps.updateDrivers();
        },

        updateDrivers: function(){
            if(Task.maps.map == null) return;

            var bounds = new google.maps.LatLngBounds();

            Task.maps.markers.list.forEach(function(d,i){
                var exist   = (typeof Task.maps.drivers[d.id] != 'undefined');
                var dvr 	= exist ? Task.maps.drivers[d.id] : d;
                dvr.latLng = new google.maps.LatLng(d.lat, d.lng);

                var icon = ic_mk_default;

                if(Task.id_driver == dvr.id) d.state = 'selected';

                switch(d.state){
                    case 'active':
                        icon = ic_mk_dvr_active;
                        bounds.extend(dvr.latLng); // Extender mapa solo activos
                        break;
                    case 'busy':
                        icon = ic_mk_dvr_busy;
                        bounds.extend(dvr.latLng); // ... y ocupados
                        break;
                    case 'selected':
                        icon = ic_mk_dvr_selected;
                        break;
                    case 'inactive':
                        icon = ic_mk_dvr_inactive;
                        break;
                }

                if(!exist){
                    dvr.marker = new google.maps.Marker({
                        position: dvr.latLng,
                        icon: icon,
                        duration: 500,
                        zIndex: dvr.state == 'active' ? 1 : 0
                    });
                } else {

                    if(dvr.marker.getPosition().lat() != dvr.latLng.lat() && dvr.marker.getPosition().lng() != dvr.latLng.lng()){
                        //console.log('Ubicacion diferente');
                        dvr.marker.setPosition(dvr.latLng);
                    }

                    // Si el state anterior es diferente al nuevo, actualizar icono
                    if(dvr.state != d.state || Task.id_driver == dvr.id){
                        //console.log(dvr.state+'::'+d.state+' => '+dvr.nombre);
                        dvr.marker.setIcon(icon);
                    }

                    dvr.date_tracked = d.date_tracked; // Actualizar
                    dvr.adr = d.adr; // Actualizar
                    dvr.state = d.state; // Actualizar nuevo state
                }

                if(Task.maps.show_only == 'all' || dvr.state == Task.maps.show_only){
                    dvr.marker.setMap(Task.maps.map);
                } else {
                    dvr.marker.setMap(null);
                }

                google.maps.event.addListener(dvr.marker, 'click', (Task.maps.openInfoWindow)(dvr.id));

                Task.maps.drivers[dvr.id] = dvr;
            });

            console.log('drivers', Task.maps.drivers);

            if(Task.maps.update_bounds){
                Task.maps.update_bounds = false;

                if(Task.maps.drivers.actives > 1 && !bounds.isEmpty()){
                    Task.maps.map.fitBounds(bounds);
                }
            }
        },

        openInfoWindow: function(id){
            return function(){
                var dvr = Task.maps.drivers[id];
                Task.maps.infoWindow.setContent(
                    '<div class="map_mk_iw_dvr clearfix">'+
                    ' <div class="_lft">'+
                    '  <img src="' + dvr.pic + '"/>'+
                    ' </div>'+
                    ' <div class="_rgt">'+
                    '  <h5><a href="driver-info.php?id='+id+'" target="_blank">'+dvr.name+' '+dvr.surname+'</a></h5>'+
                    '  <h6>Edad: '+dvr.age+' años</h6>'+
                    '  <h6 title="Fecha de rastreo">Con.: '+dvr.date_tracked+'</h6>'+
                    '  <h6>Dir.: '+dvr.adr+'</h6>'+
                    '  <span class="btn btn-sm btn-default" onclick="Race.requestDriver('+id+');">'+
                    '   <i class="fa fa-paper-plane"></i> Enviar solicitud'+
                    '  </span>'+
                    ' </div>'+
                    '</div>'
                );
                Task.maps.infoWindow.open(Task.maps.map, dvr.marker);
            };
        }

    }

};