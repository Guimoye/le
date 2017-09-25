var Fares = {

    init: function(){

        $('.icheck').iCheck({
            radioClass: 'iradio_square-red'
        });

        $('.fares input.icheck').on('ifChecked', function(e){
            Fares.enable(this.value);
        });
    },

    add: function(){
        bootbox.prompt({
            size: 'small',
            title: 'Crear tarifario',
            placeholder: 'Nombre...',
            buttons: {
                confirm:{label: 'Guardar'},
                cancel: {label: 'Cancelar'}
            },
            callback: function(name){
                if(name==null) return;
                api('ajax/fares.php', {action:'add', name:name}, function(rsp){
                    if(rsp.ok){
                        toastr.success('Guardado correctamente');
                        location.reload();
                    } else {
                        bootbox.alert(rsp.msg);
                    }
                }, 'Creando tarifario...');
            }
        });
    },

    edit: function(id, name){
        bootbox.prompt({
            size: 'small',
            title: 'Crear tarifario',
            placeholder: 'Nombre...',
            value: name,
            buttons: {
                confirm:{label: 'Guardar'},
                cancel: {label: 'Cancelar'}
            },
            callback: function(name){
                if(name==null) return;
                api('ajax/fares.php', {action:'edit', id:id, name:name}, function(rsp){
                    if(rsp.ok){
                        toastr.success('Editado correctamente');
                        location.reload();
                    } else {
                        bootbox.alert(rsp.msg);
                    }
                }, 'Editando tarifario...');
            }
        });
    },
    
    clone: function(id, name){
        bootbox.prompt({
            size: 'small',
            title: 'Clonar tarifario',
            placeholder: 'Nombre...',
            value: name + ' (copy)',
            buttons: {
                confirm:{label: 'Clonar'},
                cancel: {label: 'Cancelar'}
            },
            callback: function(name){
                if(name==null) return;
                api('ajax/fares.php', {action:'clone', id:id, name:name}, function(rsp){
                    if(rsp.ok){
                        toastr.success('Guardado correctamente');
                        location.reload();
                    } else {
                        bootbox.alert(rsp.msg);
                    }
                }, 'Clonando tarifario...');
            }
        });
    },

    remove: function(id){
        bootbox.confirm({
            message: '¿Seguro que quieres eliminar este tarifario?',
            buttons: {
                confirm:{label:'Eliminar', className:'btn-danger'},
                cancel: {label:'Cancelar'}
            },
            callback: function(response){
                if(!response) return;
                api('ajax/fares.php', {action:'remove', id:id}, function(rsp){
                    if(rsp.ok){
                        toastr.success('Eliminado correctamente');
                        location.reload();
                    } else {
                        bootbox.alert(rsp.msg);
                    }
                }, 'Eliminando tarifario...');
            }
        });
    },

    // Habilitar tarifario
    enable: function(id){
        api('ajax/settings.php', {action:'id_fare', id_fare:id}, function(rsp){
            if(rsp.ok){
                $('#fare_'+id).addClass('active').siblings().removeClass('active');
                Fares.loadPrices(id);
                toastr.success('Habilitado correctamente');
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Habilitando tarifario...');
    },
    
    // Cargar precios por id_fare :: Pager
    loadPrices: function(id_fare){
        $('select[name="id_fare"]', Pager.$filters).val(id_fare);
        Pager.apply();
    }

};

var map;
var polyOrg;
var polyDst;
var bounds;

var Prices = {

    init: function(){

        // Asignar eventos
        $('#prices').on('keyup', 'input', function(e){
            if(e.keyCode == 13){
                Prices.save($(this).data('id'));
            } else if(e.keyCode == 27){
                $('.cost').removeClass('editing').attr('readonly', true);
            }
        });

        this.initMap();
    },

    initMap: function(){
        try {
            map = new google.maps.Map(document.getElementById('map'), {
                zoom: 13,
                center: {lat: -12.107085497712584, lng: -77.00320004790461}
            });

            polyOrg = new google.maps.Polygon({
                map: map,
                strokeColor: "#000",
                strokeOpacity: 0.8,
                strokeWeight: 1,
                fillColor: "#000",
                fillOpacity: 0.3
            });

            polyDst = new google.maps.Polygon({
                map: map,
                strokeColor: "#F00",
                strokeOpacity: 0.8,
                strokeWeight: 1,
                fillColor: "#F00",
                fillOpacity: 0.3
            });

            bounds = new google.maps.LatLngBounds();
        } catch(e){
            console.error(e);
        }
    },

    // Editar
    edit: function(id){
        var $ipt = $('#price_' + id + ' input');
        if($ipt.hasClass('editing')){
            Prices.save(id);
        } else {
            $('.cost').removeClass('editing');
            $ipt.addClass('editing').attr('readonly', false).focus().select();
            //Prices.showMap(id);
        }
    },

    // Guardar
    save: function(id){
        var $ipt = $('#price_' + id + ' input');
        $ipt.removeClass('editing').attr('readonly', true);

        api('ajax/prices.php', {action:'save', id:id, cost:$ipt.val()}, function(rsp){
            if(rsp.ok){
                toastr.success('Guardado correctamente');
            } else {
                bootbox.alert(rsp.msg)
            }
        }, false);

    },
    
    showMap: function(id){
        var price = Pager.items[id];
        console.log('showMap: ', price);

        bounds = new google.maps.LatLngBounds();

        var org_points = [];
        var c = JSON.parse(price.org_points);
        for(var i in c){
            var pos = new google.maps.LatLng(c[i][0], c[i][1]);
            org_points.push(pos);
            bounds.extend(pos);
        }

        var dst_points = [];
        var d = JSON.parse(price.dst_points);
        for(var i in d){
            var pos = new google.maps.LatLng(d[i][0], d[i][1]);
            dst_points.push(pos);
            bounds.extend(pos);
        }

        polyOrg.setPaths(org_points);
        polyDst.setPaths(dst_points);

        map.fitBounds(bounds);

    }

};
$(document).ready(function(){

    Fares.init();
    Prices.init();

});