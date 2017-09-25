// Unidades de medida
var RQST = {

    $rooms: null,
    $products: null,
    $order: null,

    init: function(){

        this.$rooms             = $('#rooms');
        this.$rooms.reload      = $('.reload', this.$rooms);

        this.$products          = $('#products');
        this.$products.title    = $('.prods_title', this.$products);
        this.$products.query    = $('.query', this.$products);
        this.$products.quantity = $('.quantity', this.$products);
        this.$products.list     = $('.list', this.$products);
        this.$order         = $('#order');
        this.$order.title   = $('.tbl_ord_title', this.$order);
        this.$order.list    = $('tbody', this.$order);
        this.$order.total   = $('.total', this.$order);

        // Custom funcs
        this.$order.open = function(){
            //RQST.$rooms.removeClass('col-xs-12').addClass('col-xs-5');
            RQST.$rooms.css('width','');
            RQST.$order.show();
        };
        this.$order.close = function(){
            //RQST.$rooms.removeClass('col-xs-5').addClass('col-xs-12');
            RQST.$rooms.css('width','100%');
            RQST.$order.hide();
        };

        $('.add_note_tbl', this.$order).click(function(){
            bootbox.prompt({
                title: 'Agregar nota',
                placeholder: 'Escribir...',
                //inputType: 'textarea',
                //size: 'small',
                value: RQST.order.notes,
                buttons: {
                    confirm: { label: 'Agregar' },
                    cancel: { label: 'Cancelar' }
                },
                callback: function(note){
                    if(note===null) return;
                    RQST.order.notes = note;
                }
            });
        });

        /*this.$products.query.on('input', function() {
            console.log(this.value);
            RQST.$products.title.html(this.value);
        });*/
        // Autocompletado producto
        this.$products.query.autocomplete({
            source: function(data){
                RQST.searchProducts(0,data.term);
            },
            minLength: 1
            //delay: 300
        });

        this.$order.list.on('change', 'input.quantity', function(){
            console.log('cantidad cambio',this.value);
            RQST.updateTotal();
        });

        this.$order.find('.send').click(this.send);
        this.$order.find('.enable_table').click(this.enableTable);

        this.updateTouchSpin();

        this.loadTables(true);

        this.$order.close();

        //console.log('num:',num('',0,2));
    },

    updateTouchSpin: function(){
        $('input.quantity', RQST.$order).TouchSpin({
            //verticalbuttons:!0,
            forcestepdivisibility:'none',
            min:1,
            max:999
        });
    },
    
    chooseCategory: function(){
        CCateg.choose(function(c){
            RQST.searchProducts(c.id,c.name);
        });
    },

    // Cargar mesas
    loadTables: function(updateInterval){
        var update_after = (typeof updateInterval === 'boolean' && updateInterval);

        RQST.$rooms.reload.html('<i class="fa fa-refresh fa-pulse fa-1x fa-fw"></i>').attr('disabled',true);
        api('ajax/request.php', {action:'get_tables'}, function(rsp){
            RQST.$rooms.reload.html('<i class="fa fa-refresh"></i>').attr('disabled', false);
            if(rsp.ok){
                $.each(rsp.rooms, function(id_room,tables){
                    //console.log('id_room',id_room);
                    var html = '';
                    tables.forEach(function(o,i){
                        //console.log('bb',i);
                        var state = o.state===2 ? 'busy' : 'ready';
                        html += '<div class="item '+state+'" id="table_'+o.id+'" onclick="RQST.setTable('+o.id+',\''+o.name+'\');">';
                        html += ' <span>'+o.name+'</span>';
                        html += ' <b>'+o.total_items+'</b>';
                        html += '</div>';
                    });
                    $('.tables.room_'+id_room, RQST.$rooms).html(html);
                });
            }
            if(update_after) setTimeout(function(){RQST.loadTables(true)}, 5000)
        }, false);
    },

    send: function(){
        var data = RQST.order;
        data.action = 'send';
        api('ajax/request.php', data, function(rsp){
            if(rsp.ok){
                RQST.setOrder(rsp.order);
                toastr.success('Guardado correctamente');
                RQST.loadTables();
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Enviando...');
    },

    searchProducts: function(id_category,query){
        RQST.$products.title.html(query == '' ? 'Productos' : query);
        RQST.$products.list.html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i> Obteniendo productos...');

        var items = [];

        if(id_category > 0){
            products.forEach(function(o,i){
                if(o.id_category == id_category){
                    items[i] = o;
                }
            });
        }
        if(id_category==0){
            items = $.grep(products, function(o, i){
                var title = (o.ca_name.toLowerCase() + ' ' +o.name.toLowerCase());
                console.log(title);
                return title.indexOf(query) > -1;
            });
        }
        RQST.showProducts({products:items});
    },

    showProducts: function(rsp){
        var html = '';
        if(rsp.products.length > 0){
            rsp.products.forEach(function(o,i){
                var propres = o.propres;



                if(propres.length === 1){
                    var pp = propres[0];
                    pp.product = o.name;
                    html += '<a class="btn btn-circle grey-gallery" onclick=\'RQST.addPropre('+JSON.stringify(pp)+')\'> '+o.name+' </a> ';
                } else if(propres.length > 1){
                    html += '<div class="btn-group">';
                    html += ' <a class="btn btn-circle grey-gallery" data-toggle="dropdown">';
                    html += '  '+o.name+' <i class="fa fa-angle-down"></i>';
                    html += ' </a>';
                    html += ' <ul class="dropdown-menu">';
                    propres.forEach(function(pp,i){
                        pp.product = o.name;
                        html += '  <li> <a onclick=\'RQST.addPropre('+JSON.stringify(pp)+')\'> '+pp.name+' </a> </li>';
                    });
                    html += ' </ul>';
                    html += '</div> ';
                } else { // No tiene propres
                    html += '<a class="btn btn-circle grey-gallery disabled"> '+o.name+' </a> ';
                }

            });
        } else {
            html = '<div class="alert alert-warning"> No se encontraron productos. </div>';
        }
        RQST.$products.list.html(html);
    },

    // Agregar producto a lista de la orden
    order: null,
    addPropre: function(o){
        if(RQST.order === null) return;

        // Obtener propre que no ha sido guardado aun
        var result =  $.grep(RQST.order.ordpros, function(e){ return e.id_propre == o.id && e.id == 0; });
        if(result.length > 0){
            toastr.warning('Hay el mismo producto "no guardado" en la lista');
        } else {
            RQST.order.ordpros.unshift({
                id: 0,
                id_propre: o.id,
                product: o.product,
                propre: o.name,
                quantity: num(RQST.$products.quantity.val(),0,1),
                price_unit: o.price,
                price_total: 0,
                notes: ''
            });
            RQST.setOrder(RQST.order); // Actualizamos orden actual
        }
    },

    // Actualizar todal a pagar
    updateTotal: function(){
        RQST.order.price = 0;

        $.each(RQST.order.ordpros, function(i, o){
            var $tr = $('#propre_'+i);

            o.quantity      = num($tr.find('.quantity').val(), 0, 1);
            o.price_unit    = num(o.price_unit);
            o.price_total   = o.price_unit*o.quantity;

            $tr.find('.item_total').html(num(o.price_total,2));

            RQST.order.price += o.price_total;

            //console.log('total:',o.price_total,o.quantity );
        });

        RQST.$order.total.html(num(RQST.order.price,2));
    },

    // Habilitar meta
    enableTable: function(){
        if(RQST.order === null || RQST.order.id <= 0){
            toastr.warning('No hay mesa para liberar');
            return;
        }
        bootbox.confirm({
            message: '¿Estás seguro de que deseas liberar esta mesa?',
            buttons: {
                cancel: {label:'Cancelar'},
                confirm: {label:'Si, liberar'}
            },
            callback: function(result){
                if(!result) return;
                api('ajax/request.php', {action:'enable_table', id_order:RQST.order.id}, function(rsp){
                    if(rsp.ok){
                        toastr.success('Mesa liberada');
                        RQST.loadTables();
                        RQST.order = null;
                        RQST.$order.close();
                    } else {
                        bootbox.alert(rsp.msg);
                    }
                }, 'Liberando mesa...');
            }
        });
    },

    addNotePP: function(i){
        bootbox.prompt({
            title: 'Agregar nota',
            placeholder: 'Escribir...',
            //inputType: 'textarea',
            //size: 'small',
            value: RQST.order.ordpros[i].notes,
            buttons: {
                confirm: { label: 'Agregar' },
                cancel: { label: 'Cancelar' }
            },
            callback: function(note){
                if(note===null) return;
                RQST.order.ordpros[i].notes = note;
            }
        });
    },

    removePP: function(i){
        var ordpro = RQST.order.ordpros[i];
        console.log('ordpro',ordpro);
        if(isNumber(ordpro.id) && ordpro.id > 0){
            bootbox.confirm({
                title: '¿Seguro que desea eliminar este producto?',
                message: "<form id='_crpp' onsubmit='return false;'>"+
                         " <input type='text' name='reason' class='form-control' placeholder='Indica el motivo'/><br/>"+
                         " <label> <input type='checkbox' name='back_stock'/> ¿Desea devolver el stock descontado?</label>"+
                         "</form>",
                buttons: {
                    cancel: {label:'Cancelar'},
                    confirm: {label:'Eliminar', className: 'btn-danger'}
                },
                callback: function(result){
                    if(!result) return;
                    var data = $('#_crpp').serializeObject();
                    data.action = 'remove_ordpro';
                    data.id = ordpro.id;
                    api('ajax/request.php', data, function(rsp){
                        if(rsp.ok){
                            toastr.success('Eliminado correctamente');
                            RQST.order.ordpros.splice(i, 1);
                            RQST.updateOrderList();
                        } else {
                            bootbox.alert(rsp.msg);
                        }
                    }, 'Eliminando...');
                }
            });
        } else {
            RQST.order.ordpros.splice(i, 1);
            RQST.updateOrderList();
        }
    },

    table_name: '',
    setTable: function(id,name){
        RQST.table_name = name;
        api('ajax/request.php', {action:'get_table', id:id}, function(rsp){
            if(rsp.ok){
                RQST.$order.open();
                RQST.$order.list.html('');
                if(rsp.order === null){
                    RQST.$order.title.html(name);
                    RQST.order = {
                        id: 0,
                        id_table: id,
                        notes: '',
                        ordpros: [],
                        price: 0
                    };
                    RQST.updateTotal();
                } else {
                    RQST.setOrder(rsp.order)
                }
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Obteniendo mesa...');
    },

    setOrder: function(order){
        RQST.order = order;
        RQST.$order.title.html(RQST.table_name+' - Trabajador: '+RQST.order.user);
        RQST.updateOrderList();
    },

    updateOrderList: function(){
        var html = '';
        $.each(RQST.order.ordpros, function(i,o){
            var saved = (o.id>0);

            html += '<tr id="propre_'+i+'" '+(saved ? 'style="background:rgba(0,255,0,0.01)"' : '')+'>';
            html += ' <td> '+o.product+' - '+o.propre+' </td>';
            html += ' <td> '+stg.coin+num(o.price_unit,2)+' </td>';
            html += ' <td> <input type="text" class="quantity ctr" value="'+o.quantity+'" '+(saved?'disabled':'')+'> </td>';
            html += ' <td> '+stg.coin+' <span class="item_total">--</span> </td>';
            html += ' <td> <button class="btn btn-circle green-jungle btn-block" onclick="RQST.addNotePP('+i+');"> <i class="fa fa-sticky-note"></i> </button> </td>';
            html += ' <td> <button class="btn btn-circle btn-default btn-block" onclick="RQST.removePP('+i+');"> <i class="fa fa-trash"></i> </button> </td>';
            html += '</tr>';
        });
        RQST.$order.list.html(html);
        RQST.updateTouchSpin();
        RQST.updateTotal();
    }

};


// Elegir
var CCateg = {

    items: [],

    callback: null,

    $modal: null,
    
    initViews: function(){
        if(this.$modal != null) return;
        
        $body.append(
            '<div id="modal_choose_category" class="modal fade modal-scroll">'+
            ' <div class="modal-dialog">'+
            '  <div class="modal-content">'+

            '   <div class="modal-header">'+
            '    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>'+
            '    <h4 class="modal-title">Elegir categoría</h4>'+
            '   </div>'+

            '   <div class="modal-body">'+
            '    <table class="table table-bordered table-hover mdl-td">'+
            '     <tbody></tbody>'+
            '    </table>'+
            '   </div>'+

            '  </div>'+
            ' </div>'+
            '</div>'
        );
        
        this.$modal = $('#modal_choose_category');
        this.$order = $('tbody', this.$modal);
    },
    
    choose: function(callback){
        CCateg.callback = (typeof callback === 'function') ? callback : null;

        CCateg.initViews();

        if(typeof categories !== 'undefined'){
            CCateg.showList(categories);
        } else {
            api('get_categories', function(rsp){
                if(rsp.ok){
                    CCateg.showList(rsp.categories);
                } else {
                    toastr.warning("Error al recuperar categorías");
                }
                console.log(rsp);
            }, 'Obteniendo categorías...', true);
        }
    },

    showList: function(categories){
        CCateg.items = categories;

        var html = CCateg.mkMenuTr(CCateg.items, 0);
        CCateg.$order.html(html);

        CCateg.$modal.modal('show');
    },

    call: function(c){
        CCateg.callback(c);
        CCateg.$modal.modal('hide');
        console.log('elegido:',c);
    },

    mkMenuTr: function(data,level){
        var html = '',
            spaces = '&nbsp;&nbsp;'.repeat(level);
        data.forEach(function(o,i){
            html += '<tr>';
            html += ' <script>var cat_'+o.id+' = '+JSON.stringify(o)+';</script>';
            html += ' <td> '+ spaces +' <span class="badge badge-empty" style="background:'+o.color+'"></span> '+o.name+' </td>';
            html += ' <td width="1%" style="padding:4px">';
            html += '  <button class="btn btn-default btn-block" onclick="CCateg.call(cat_'+o.id+');">Elegir</button>';
            html += ' </td>';
            html += '</tr>';

            if(o.sub.length > 0){
                html += CCateg.mkMenuTr(o.sub, level+1);
            }
        });
        return html;
    }
    
};