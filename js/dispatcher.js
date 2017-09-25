var map;
var circle;
var infoWindow;
var dService;
var dDisplay;

var mkOrg; // Marker Origen
var mkDst; // Marker Destino

var drivers = {};

// Funciones de mapas
var Maps = {

    init: function(){
        try {
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 13,
                center: {lat: -12.107085497712584, lng: -77.00320004790461}
            });

            dService = new google.maps.DirectionsService;
            dDisplay = new google.maps.DirectionsRenderer({
                map: map,
                suppressMarkers: true,
                //preserveViewport: true
            });

            infoWindow = new google.maps.InfoWindow();

            google.maps.event.addListener(map, 'click', function () {
                infoWindow.close();
            });

            mkOrg = new google.maps.Marker({
                map: map,
                draggable: true,
                animation: google.maps.Animation.DROP,
                label: 'A'
            });
            mkOrg.addListener('dragend', this.movedOrg);

            mkDst = new google.maps.Marker({
                map: map,
                draggable: true,
                animation: google.maps.Animation.DROP,
                label: 'B'
            });
            mkDst.addListener('dragend', this.movedDst);

            // Circle :: Origen
            circle = new google.maps.Circle({
                strokeColor: '#FF0000',
                strokeOpacity: 0.3,
                strokeWeight: 2,
                fillColor: '#FF0000',
                fillOpacity: 0.1,
                map: map,
                center: {lat: 0, lng:  0},
                radius: (stg.radio_request * 1000)
            });
        } catch(e){
            console.error('Error al iniciar Mapas');
        }
    },

    // Origen movido
    movedOrg: function(e){
        Race.org = {lat: e.latLng.lat(), lng: e.latLng.lng()};
        $org.loading(true);
        api('../api/geocode.php', Race.org, function(rsp){
            $org.loading(false);
            $org.val(rsp.adr);
        }, false, true);
        Maps.setPos();
    },

    // Destino movido
    movedDst: function(e){
        Race.dst = {lat: e.latLng.lat(), lng: e.latLng.lng()};
        $dst.loading(true);
        api('../api/geocode.php', Race.dst, function(rsp){
            $dst.loading(false);
            $dst.val(rsp.adr);
        }, false, true);
        Maps.setPos();
    },

    // Se llama cuando los puntos son actualizados
    setPos: function(){

        if(Race.org == null) return;

        circle.setCenter(Race.org);

        if(Race.dst == null) return;

        Maps.calcRoute();
        Race.loadCost();
    },

    // Calcular ruta
    calcRoute: function(){
        dService.route({
                origin: Race.org,
                destination: Race.dst,
                travelMode: google.maps.TravelMode.DRIVING,
                avoidTolls: true
            }, function(response, status) {
                if (status == google.maps.DirectionsStatus.OK){
                    dDisplay.setDirections(response);
                } else {
                    console.warn('Directions request failed due to', status);
                }
            }
        );
    },

    getCenter: function(){
        var ctr = map.getCenter();
        return {lat:ctr.lat(), lng:ctr.lng()};
    },

    show_only: 'all',
    update_bounds: true,

    setDrivers: function(dvrs){
        var bounds = new google.maps.LatLngBounds();

        dvrs.list.forEach(function(d,i){
            var exist   = (typeof drivers[d.id] != 'undefined');
            var dvr 	= exist ? drivers[d.id] : d;
            dvr.latLng = new google.maps.LatLng(d.lat, d.lng);

            var icon = ic_mk_defaulr;

            switch(d.state){
                case 'active':
                    icon = ic_mk_dvr_active;
                    bounds.extend(dvr.latLng); // Extender mapa solo activos
                    break;
                case 'busy':
                    icon = ic_mk_dvr_busy;
                    bounds.extend(dvr.latLng); // ... y ocupados
                    break;
                case 'inactive':
                    icon = ic_mk_dvr_inactive;
                    break;
            }

            if(Race.id_driver == dvr.id){
                icon = ic_mk_dvr_selected;
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
                if(dvr.state != d.state || Race.id_driver == dvr.id){
                    //console.log(dvr.state+'::'+d.state+' => '+dvr.nombre);
                    dvr.marker.setIcon(icon);
                }

                dvr.date_tracked = d.date_tracked; // Actualizar
                dvr.adr = d.adr; // Actualizar
                dvr.state = d.state; // Actualizar nuevo state
            }

            if(Maps.show_only == 'all' || dvr.state == Maps.show_only){
                dvr.marker.setMap(map);
            } else {
                dvr.marker.setMap(null);
            }

            google.maps.event.addListener(dvr.marker, 'click', (Maps.openInfoWindow)(dvr.id));

            drivers[dvr.id] = dvr;
        });

        console.log('drivers',drivers);

        if(Maps.update_bounds){
            Maps.update_bounds = false;

            if(drivers.actives > 1 && !bounds.isEmpty()){
                map.fitBounds(bounds);
            }
        }
    },

    openInfoWindow: function(id){
        return function(){
            var dvr = drivers[id];
            infoWindow.setContent(
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
            infoWindow.open(map, dvr.marker);
        };
    }

};


// Vistas
var $name,
    $org,
    $dst,
    $type,
    $driver,
    $cost,
    $prog,
    $promo,
    $notes;

function initViews(){

    $org        = $('#org');
    $org.load   = $org.parent().find('.search');
    $dst        = $('#dst');
    $dst.load   = $dst.parent().find('.search');

    $type       = $('input[name="type"]');
    $driver     = $('#driver');
    $driver.x   = $('.driver_x');
    $driver.mk  = $('.driver_mk');
    $cost       = $('#cost');
    $cost.load  = $('#cost_load');
    $cost.msg   = $('#cost_msg');
    $prog       = $('#prog');
    $prog.box   = $('#prog_box');
    $prog.date  = $('#prog_date');
    $prog.time  = $('#prog_time');
    $promo      = $('#promo');
    $notes      = $('#notes');

    $type.change(Race.loadCost);

    $org.loading = function(show){
        $org.load.html(show ? '<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i>' : '<i class="fa fa-search"></i>');
    };
    $dst.loading = function(show){
        $dst.load.html(show ? '<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i>' : '<i class="fa fa-search"></i>');
    };

    $driver.set = function(id,name,surname){
        Race.id_driver = id;
        $driver.val(name + ' ' + surname);
        $driver.mk.show();
        $driver.x.show();
    };
    $driver.unset = function(){
        Race.id_driver = 0;
        $driver.val('Sin conductor');
        $driver.mk.hide();
        $driver.x.hide();
    };

    /*$cost.TouchSpin({
        verticalbuttons:!0,
        forcestepdivisibility:'none',
        decimals: 2,
        step: 0.5,
        min: 0,
        max: 9999
    });*/

    $prog.change(function(e){
        if(e.target.checked){
            $prog.box.slideDown();
        } else {
            $prog.box.slideUp();
        }
    });
}

var Race = {

    id: 0,
    id_driver: 0,
    id_client: 0,
    org: null,
    dst: null,
    id_price: 0,
    price_base: 0,

    init: function(){
    },

    add: function(){

        var isProg = ($('#prog:checked').length == 1);

        var data = {
            action:     'request',
            id:         Race.id,
            id_client:  Race.id_client,
            type:       $('input[name=type]:checked').val(),
            org:        Race.org,
            dst:        Race.dst,
            id_price:   $cost.data('id'),
            price_base: $cost.val(),
            prog_date:  $prog.date.val(),
            prog_time:  $prog.time.val(),
            promo:      $promo.val(),
            notes:      $notes.val()
        };

        if(Race.id_client <= 0){
            bootbox.alert('Elige el cliente');

        } else if(Race.org == null){
            bootbox.alert('Ingresa el origen');

        } else if(Race.dst == null){
            bootbox.alert('Ingresa el destino');

        } else if(isProg && (!data.prog_date || data.prog_date == '')){
            bootbox.alert('Ingresa la fecha');

        } else if(isProg && (!data.prog_time || data.prog_time == '')){
            bootbox.alert('Ingresa la hora');

        } else {
            data.org.adr = $org.val();
            data.dst.adr = $dst.val();
            api('ajax/dispatcher.php', data, function(rsp){
                console.log('dispatch:', rsp);
                if(rsp.ok){
                    Race.id = rsp.id;
                    $driver.set(1, 'Álvaro', 'Chachapoyas');
                    toastr.success('Guardado correctamente');
                } else {
                    bootbox.alert(rsp.msg);
                }
            }, 'Generando servicio...');
        }

        console.log('data', data);
    },
    
    // Cargar costo de la carrera
    loadCost: function(){
        $cost.data('id', 0);
        //$cost.val('0.00');
        if(Race.org != null && Race.dst != null){
            var data = {
                action: 'get_cost',
                org: Race.org,
                dst: Race.dst,
                type: $('input[name=type]:checked').val()
            };
            $cost.load.fadeIn();
            $cost.msg.hide();
            api('ajax/dispatcher.php', data, function(rsp){
                if(rsp.ok){
                    $cost.load.fadeOut(100);
                    $cost.data('id', rsp.price.id);
                    $cost.val(rsp.price.cost);
                } else {
                    $cost.load.fadeOut(function(){
                        $cost.msg.fadeIn().html('Fuera de cobertura');
                    });
                    $cost.val('0.00');
                }
            }, false);
        }
    },
    
    setOrg: function(o){
        //var o = {lat: -12.08510, lng: -77.01187, adr: 'Av. Canto Rey, Lima, Perú'};
        if(o){
            Race.org = {lat:parseFloat(o.lat), lng:parseFloat(o.lng)};
            $org.val(o.adr);
            mkOrg.setPosition(Race.org);
            map.setCenter(Race.org);
            Maps.setPos();
        }
    },

    setDst: function(o){
        //var o = {lat: -12.10025, lng: -77.03487, adr: 'Av. Canto Rey, Lima, Perú'};
        if(o){
            Race.dst = {lat:parseFloat(o.lat), lng:parseFloat(o.lng)};
            $dst.val(o.adr);
            mkDst.setPosition(Race.dst);
            map.setCenter(Race.dst);
            Maps.setPos();
        }
    },

    savePoint: function(isOrg){
        var data  = isOrg ? Race.org : Race.dst;
        var $ipt = isOrg ? $org     : $dst;
        if(Race.id_client > 0){
            if(data != null){
                data.action = 'save_point';
                data.id_client = Race.id_client;
                data.adr = $ipt.val();
                api('ajax/clients.php', data, function(rsp){
                    if(rsp.ok){
                        toastr.success('Guardado correctamente');
                    } else {
                        bootbox.alert(rsp.msg);
                    }
                }, 'Guardando dirección favorita...');
            } else bootbox.alert('Dirección incorrecta');
        } else  bootbox.alert('Elige un cliente para guardar direcciones favoritas');
    },

    driverOnMap: function(){
        toastr.info('Mostrar en mapa');
    },

    removeDriver: function(){
        bootbox.confirm('¿Eliminar conductor asignado a este servicio?', function(response){
            if(!response) return;
            $driver.unset();
        });
    },
    
    // Solicitar conductor
    requestDriver: function(id){
        if(Race.id <= 0){
            toastr.warning('Guarda el servicio para solicitar conductor');
        } else {
            alert('A solicitar :D');
        }
    }

};

// Cliente
var AClient = {
    
    points: [],

    $name: null,

    init: function(){

        this.$name = $('#name');
        this.$name.autocomplete({
            //source: "ajax/autocomplete-clients.php",
            source: function(data, response){
                data.action = 'autocomplete';
                api('ajax/clients.php', data, response, false, true);
            },
            minLength: 1,
            select: function(event, ui) {
                AClient.assign(ui.item);
                return false;
            },

            create: function () {
                $(this).data('ui-autocomplete')._renderItem = function(ul, item){
                    return $("<li>").append('<a>' + item.name + ' ' + item.surname + '</a>').appendTo(ul);
                }
            }
        });
        this.$name.keypress(this.clean);
        this.$name.keyup(function(e){ if(e.keyCode == 8) AClient.clean(); });
    },

    assign: function(item){
        Race.id_client = item.id;
        AClient.$name.val(item.name + ' ' + item.surname);
        AClient.getPoints(item.id);
    },

    clean: function(){
        if(Race.id_client > 0){
            Race.id_client = 0;
            AClient.$name.val('');
            AClient.rutas = [];
            $('.destinations ul').html('<div class="msg">Elige un cliente</div>');
            $('.services ul').html('<div class="msg">Elige un cliente</div>');
        }
    },

    // Obtener destinos favoritos del pasajero
    getPoints: function(id){
        var $list = $('.points tbody');

        $list.html('<div class="msg"><i class="fa fa-circle-o-notch fa-spin fa-1x fa-fw"></i> Obteniendo direcciones...</div>');

        var html  = '';
        api('ajax/clients.php', {action:'points', id:id}, function(rsp){
            if(!rsp.ok) return;
            rsp.points.forEach(function(o,i){
                AClient.points[o.id] = o;

                html += '<tr>';
                html += ' <td width="1%"> <i class="icon-heart font-grey-silver"></i> </td>';
                html += ' <td> <div class="_adr">' + o.adr + '</div> </td>';
                html += ' <td width="1%">';
                html += '  <div class="btn-group">';
                html += '   <a class="btn grey-cascade btn-xs btn-outline" href="#" data-toggle="dropdown">';
                html += '    <i class="fa fa-angle-down"></i>';
                html += '   </a>';
                html += '   <ul class="dropdown-menu">';
                html += '    <li>';
                html += '     <a onclick="Race.setOrg(AClient.points['+o.id+']);"> <i class="fa fa-map-marker font-green-jungle"></i> Agregar a Origen </a>';
                html += '    </li>';
                html += '    <li>';
                html += '     <a onclick="Race.setDst(AClient.points['+o.id+']);"> <i class="fa fa-map-marker font-red-flamingo"></i> Agregar a Destino </a>';
                html += '    </li>';
                html += '   </ul>';
                html += '  </div>';
                html += ' </td>';
                html += '</tr>';

            });

            if(html == ''){
                html = '<div class="msg">No tiene direcciones</div>';
            }

            $list.fadeOut(function(){
                $list.html(html);
                $list.fadeIn();
            });
        }, false, true);

    }

};

// Auto completar direccion
var AAdr = {
    init: function(){
        $('#org,#dst').autocomplete({
            source: function(data, response){
                data.query = data.term;
                data.lat = -12.08421;
                data.lng = -77.00354;

                //$search.html('<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i>');

                api('../api/ext/autocomplete-address.php',data, response, false, true);
            },
            minLength: 1,
            select: function(event, ui){
                AAdr.getAddressDetails(ui.item.content, ui.item.reference, $(this));
                return false;
            },
            create: function(){
                $(this).data('ui-autocomplete')._renderItem = function(ul, item){
                    return $("<li>").append('<a>' + item.content + '</a>').appendTo(ul);
                }
            }
        });

        /*$search.click(function(){
            var adr = $ipt.val();
            var data = (adr != '') ? {address:adr} : FMap.getCenter();
            Mude.geocode(data, key);
        });*/
    },

    getAddressDetails: function(adr, ref, $ipt){
        //var $search = $field.parent().find('.search');
        //$search.html('<i class="fa fa-spinner fa-spin fa-1x fa-fw"></i>');
        api('../api/ext/address-details.php', {ref:ref}, function(pos){
            //$search.html('<i class="fa fa-search"></i>');
            $ipt.val(adr);
            map.setCenter(pos);
            if($ipt.attr('id') == 'org'){
                mkOrg.setPosition(pos);
                Race.org = pos;
            } else {
                mkDst.setPosition(pos);
                Race.dst = pos;
            }
            Maps.setPos();
        }, false, true);
    },

    geocode: function(isOrg){
        var $ipt = isOrg ? $org : $dst;
        var adr = $ipt.val();
        var data = (adr != '') ? {adr:adr} : Maps.getCenter();

        $ipt.loading(true);
        api('../api/geocode.php', data, function(rsp){
            var pos = {lat:rsp.lat, lng:rsp.lng};
            if(isOrg){
                mkOrg.setPosition(pos);
                Race.org = pos;
            } else {
                mkDst.setPosition(pos);
                Race.dst = pos;
            }
            $ipt.loading(false);
            $ipt.val(rsp.adr);
            Maps.setPos();
        }, false, true);
    }

};

$(document).ready(function(){

    initViews();

    Race.init();
    Maps.init();
    AClient.init();
    AAdr.init();

    MClient.init(AClient.assign);

});