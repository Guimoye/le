// Unidades de medida
var BOX = {

    order: null,
    ordpros: [], // Productos en esta cuenta
    accounts: [],
    account: { //  Cuenta actual a cobrar
        name: 0,
        amount: 0,
        ordpros: {}
    },

    $rooms: null,
    $products: null,
    $order: null,

    $accounts: null,

    $form: null,

    init: function(){

        this.$acts              = $('#acts');
        this.$acts.show_tables  = $('.show_tables', this.$acts);

        this.$rooms             = $('#rooms');
        this.$rooms.reload      = $('.reload', this.$rooms);

        this.$products          = $('#products');
        this.$products.title    = $('.prods_title', this.$products);
        this.$products.query    = $('.query', this.$products);
        this.$products.quantity = $('.quantity', this.$products);
        this.$products.list     = $('.list', this.$products);

        this.$order             = $('#order');
        this.$order.list        = $('.list', this.$order);
        this.$order.total       = $('.order_total_price', this.$order);

        this.$accounts          = $('#accounts');

        this.$form              = $('#form_box');
        this.$form.title        = $('.form_title');
        this.$form.id_order     = $('input[name=id_order]', this.$form);
        this.$form.account      = $('input[name=account]', this.$form);
        this.$form.id_promo     = $('input[name=id_promo]', this.$form);
        this.$form.price        = $('input[name=price]', this.$form);
        this.$form.client       = $('.client_cont', this.$form);
        this.$form.client.id    = $('input[name=id_client]', this.$form.client);
        this.$form.client.name  = $('input[name=client_name]', this.$form.client);
        this.$form.client.btn   = $('button', this.$form.client);
        this.$form.tip          = $('input[name=tip]', this.$form);
        this.$form.promo_amt    = $('input[name=promo_amt]', this.$form);
        this.$form.promo_pct    = $('input[name=promo_pct]', this.$form);
        this.$form.with_card    = $('input[name=with_card]', this.$form);
        this.$form.card         = $('input[name=card]', this.$form);
        this.$form.card.cont    = this.$form.card.parent().parent();
        this.$form.cash         = $('input[name=cash]', this.$form);
        this.$form.total        = $('input[name=total]', this.$form);
        this.$form.total.cont   = $('.total_cont', this.$form);
        this.$form.total.txt    = $('.total', this.$form);

        this.$form.give         = $('.give', this.$form);
        this.$form.give.txt     = $('.text', this.$form.give);
        this.$form.give.amount  = $('.amount', this.$form.give);

        // Custom funcs
        this.$rooms.open = function(){
            BOX.$acts.show_tables.fadeOut();
            BOX.$rooms.slideDown();
            BOX.$order.slideUp();
        };
        this.$rooms.close = function(){
            BOX.$acts.show_tables.fadeIn();
            BOX.$rooms.slideUp();
            BOX.$order.slideDown();
        };

        this.$form.client.set = function(id,name){
            BOX.$form.client.id.val(id);
            BOX.$form.client.name.val(name);
        };

        this.$form.total.set = function(total){
            BOX.$form.total.val(total);
            BOX.$form.total.txt.html(total);
        };

        // EVENTOS
        this.$acts.show_tables.click(function(){
            BOX.$rooms.open();
        });
        this.$rooms.reload.click(this.loadTables);

        this.$form.with_card.change(function(){
            if(this.checked){
                BOX.$form.card.cont.slideDown();
            } else {
                BOX.$form.card.cont.slideUp();
            }
        });

        this.$form.client.btn.click(MClient.add);
        AClient.init();

        this.$form.tip.on('input', this.updateTotal);
        this.$form.promo_amt.on('input', this.promoAmtChanged);
        this.$form.promo_pct.on('input', this.promoPctChanged);
        this.$form.card.on('input', this.updateTotal);
        this.$form.cash.on('input', this.updateTotal);

        $('.kbd_btn').click(function(){
            var key = this.innerHTML;
            BOX.$form.cash.val((key == 'C') ? '' : BOX.$form.cash.val()+''+key);
            BOX.updateTotal();
        });

        $('.promo_code_btn', this.$form).click(this.choosePromoCode);

        $('.total_cont', this.$form).click(function(){
            BOX.$form.cash.val(BOX.getTotalPrice());
            BOX.updateTotal();
        });

        $('.send_btn', this.$form).click(this.send);
        $('.add_subaccount').click(this.addSubaccount);

        this.loadTables(true);

        MClient.init(function(c){
            BOX.$form.client.set(c.id, c.name);
        });

        //console.log('num:',num('',0,2));

        this.updateTotal();
    },

    choosePromoCode: function(){
        bootbox.prompt({
            title: 'Código promocional',
            placeholder: 'Código...',
            size: 'small',
            buttons: {
                cancel: {label:'Cancelar'},
                confirm: {label:'Verificar'}
            },
            callback: function(code){
                if(code==null) return;
                api('ajax/promos.php', {action:'check_code', code: code, amount:BOX.getTotalPrice()}, function(rsp){
                    if(rsp.ok){
                        BOX.$form.id_promo.val(rsp.id);
                        BOX.$form.promo_amt.val(rsp.discount_amount);
                        BOX.promoAmtChanged();
                        console.log(rsp);
                    } else {
                        bootbox.alert(rsp.msg);
                    }
                }, 'Verificando...');
            }
        });
    },

    addSubaccount: function(){
        BOX.accounts[BOX.accounts.length] = {
            paid: false,
            ordpros: {}
        };
        console.log('BOX.accounts',BOX.accounts);
        BOX.updateOrderList();
        BOX.updateListSubaccounts();
    },

    removeAccount: function(ia){
        //delete BOX.accounts[ia];
        //BOX.accounts.splice(ia,1);
        if(ia == 0){
            bootbox.alert('No puedes eliminar la cuenta principal.');
        } else {
            $.each(BOX.accounts[ia].ordpros, function(id,o){
                BOX.accounts[0].ordpros[id] = o;
            });
            BOX.accounts.splice(ia,1);

            BOX.updateOrderList();
            BOX.updateListSubaccounts();
            BOX.addAccountToPay(0);
        }
    },

    addOrdproToAccount: function(id_ordpro,ia,from_ia){

        delete BOX.accounts[from_ia].ordpros[id_ordpro];

        BOX.accounts[ia].ordpros[id_ordpro] = BOX.order.ordpros[id_ordpro];

        //BOX.order.ordpros[id_ordpro].ia = index_account;
        BOX.updateOrderList();
        BOX.updateListSubaccounts();
        BOX.addAccountToPay(BOX.account.name);
    },

    updateListSubaccounts: function(){
        var html = '';

        console.log('BOX.accounts',BOX.accounts);

        var tta = BOX.accounts.length;

        BOX.accounts.forEach(function(a,ia){
            var total_price = 0,
                total_promo = 0;

            html += '<div class="portlet light account_'+ia+'" '+(a.paid?'style="background:#DDD"':'')+'>';
            html += ' <div class="portlet-title">';
            html += '  <div class="caption">';
            html += '   <span class="caption-subject uppercase bold font-yellow-gold">'+BOX.table_name+' &raquo; Cuenta '+padIx(ia)+'</span>';
            html += '  </div>';

            html += '  <div class="actions">';
            if(ia != 0){
                html += '   <button class="btn btn-circle red" onclick="BOX.removeAccount('+ia+')">';
                html += '    <i class="fa fa-close"></i> Eliminar';
                html += '   </button>';
            }
            html += '  </div>';

            html += ' </div>';
            html += ' <div class="portlet-body">';

            html += '  <table class="table table-bordered mdl-td">';
            html += '   <thead>';
            html += '   <tr>';
            html += '    <th>Descripción</th>';
            html += '    <th width="85px" class="ctr">Precio</th>';
            html += '    <th width="1%" class="ctr">Cant.</th>';
            html += '    <th width="85px" class="ctr"> Desc. </th>';
            html += '    <th width="90px" class="ctr"> Total </th>';
            html += '    <th width="1%"></th>';
            html += '   </tr>';
            html += '   </thead>';
            html += '   <tbody class="list">';

            $.each(a.ordpros, function(id,o){

                total_price += num(o.price_total);

                var state = '';
                switch(o.state){
                    case '1': state = '<span class="badge pull-right" style="background:#1991EB"> Solicitado </span>'; break;
                    case '2': state = '<span class="badge pull-right" style="background:#F3C200"> Preparando </span>'; break;
                    case '3': state = '<span class="badge pull-right" style="background:#26C281"> Despachado </span>'; break;
                }

                html += '<tr>';
                html += ' <td> '+o.product+' - '+o.propre+' '+state+'</td>';
                html += ' <td> '+stg.coin +num(o.price_unit,2)+' </td>';
                html += ' <td class="ctr"> '+o.quantity+' </td>';
                html += ' <td> '+stg.coin +num(o.price_discount,2,0)+' </td>';
                html += ' <td> '+stg.coin +num(o.price_total,2)+' </button> </td>';
                html += ' <td class="ctr">';

                if(tta <= 1){
                    html += '<button class="btn btn-circle btn-default" onclick="BOX.removeOrdpro('+id+','+ia+');"> <i class="fa fa-trash"></i> </button>';
                } else {
                    html += '<div class="btn-group btn-block">';
                    html += ' <a class="btn btn-default btn-circle btn-block dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false">';
                    html += '  <i class="fa fa-ellipsis-v"></i>';
                    html += ' </a>';
                    html += ' <ul class="dropdown-menu">';
                    BOX.accounts.forEach(function(o,i){
                        if(i != ia) {
                            html += '  <li> <a onclick="BOX.addOrdproToAccount(' + id + ',' + i + ',' + ia + ');"> Mover a cuenta ' + padIx(i) + ' </a> </li>';
                            //html += '<button class="btn btn-circle btn-default" onclick="BOX.addOrdproToAccount('+id+','+i+');"> C'+(i+1)+' </button>';
                        }
                    });
                    html += '  <li> <a onclick="BOX.removeOrdpro('+id+','+ia+');"> Eliminar </a> </li>';
                    html += ' </ul>';
                    html += '</div>';
                }

                html += ' </td>';
                html += '</tr>';
            });

            html += '   </tbody>';
            html += '   <tfoot>';
            html += '   <tr>';
            html += '    <td class="ctr">';
            html += '     <button class="btn yellow-gold btn-circle hide">';
            html += '      <i class="fa fa-refresh"></i> Agrupar peridos';
            html += '     </button>';
            html += '     <button class="btn blue-madison btn-circle" onclick="BOX.printPreAccount('+ia+');">';
            html += '      <i class="fa fa-print"></i> Imprimir Precuenta';
            html += '     </button>';
            html += '    </td>';
            html += '    <td colspan="3" class="uppercase"> Total </td>';
            html += '    <td class="ctr pdg_h_0"> '+stg.coin+'<b class="order_total_price">'+num(total_price,2)+'</b> </td>';
            html += '    <td>';
            if(tta > 0){
                html += '<button class="btn green-jungle btn-circle btn-block" onclick="BOX.addAccountToPay('+ia+');">';
                html += ' <i class="fa fa-arrow-right"></i>';
                html += '</button>';
            }
            html += '    </td>';
            html += '   </tr>';
            html += '   </tfoot>';
            html += '  </table>';

            html += ' </div>';
            html += '</div>';

        });
        BOX.$accounts.html(html);
    },

    // Eliminar producto de una orden
    removeOrdpro: function(id,ia){
        bootbox.confirm({
            message: '¿Estás seguro de que quieres eliminar este pedido?',
            buttons: {
                cancel: {label: 'Cancelar'},
                confirm: {label: 'Eliminar', className: 'btn-danger'}
            },
            callback: function(result){
                if(!result) return;
                api('ajax/box.php', {action:'remove_ordpro', id:id}, function(rsp){
                    if(rsp.ok){
                        toastr.success('Eliminado correctamente');
                        delete BOX.accounts[ia].ordpros[id];
                        BOX.updateListSubaccounts();
                        BOX.addAccountToPay(BOX.account.name);
                    } else {
                        bootbox.alert(rsp.msg);
                    }
                }, 'Eliminando...');
            }
        });
    },

    // Cargar mesas
    loadTables: function(updateInterval){
        var update_after = (typeof updateInterval === 'boolean' && updateInterval);

        BOX.$rooms.reload.html('<i class="fa fa-refresh fa-pulse fa-1x fa-fw"></i>').attr('disabled',true);
        api('ajax/box.php', {action:'get_tables'}, function(rsp){
            BOX.$rooms.reload.html('<i class="fa fa-refresh"></i>').attr('disabled', false);
            if(rsp.ok){
                if(Array.isArray(rsp.rooms)){
                    $('.room').html('<div class="alert alert-info">No hay mesas ocupadas.</div>');
                } else {
                    $.each(rsp.rooms, function(id_room,tables){
                        //console.log('id_room',id_room);
                        var html = '';
                        tables.forEach(function(o,i){
                            //console.log('bb',i);
                            var state = o.state===2 ? 'busy' : 'ready';
                            html += '<div class="item '+state+'" id="table_'+o.id+'" onclick="BOX.setTable('+o.id+',\''+o.name+'\');">';
                            html += ' <span>'+o.name+'</span>';
                            html += ' <b>'+o.total_items+'</b>';
                            html += '</div>';
                        });
                        $('.tables.room_'+id_room, BOX.$rooms).html(html);
                    });
                }
            }
            if(update_after) setTimeout(function(){BOX.loadTables(true)}, 5000)
        }, false);
    },

    send: function(){
        var data = BOX.$form.serializeObject();
        data.price = BOX.account.total;
        data.ia = BOX.account.name;
        data.ordpros = Object.keys(BOX.account.ordpros);
        api('ajax/box.php', data, function(rsp){
            if(rsp.ok){
                toastr.success('Guardado correctamente');
                BOX.accounts[data.ia].paid = true;
                BOX.addNextAccountToPay();
                BOX.updateListSubaccounts();
                MAnnul.setItems(rsp.transactions);

                // Print
                Print.print(rsp.data_print);
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Guardando...');
    },

    table_name: '',
    setTable: function(id,name){
        BOX.table_name = name;
        api('ajax/box.php', {action:'get_table', id:id}, function(rsp){
            if(rsp.ok){
                BOX.$rooms.close();
                BOX.$order.list.html('');
                BOX.setOrder(rsp.order)
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Obteniendo mesa...');
    },

    setOrder: function(order){
        BOX.order = order;
        BOX.accounts = [];
        BOX.accounts[0] = {
            paid: false,
            ordpros: {}
        };

        // Agregamos todos los productos a la cuenta '0'
        //console.log('order.ordpros',order.ordpros);
        $.each(order.ordpros, function(id,o){
            BOX.accounts[0].ordpros[id] = o;
        });

        BOX.$order.total.html(num(order.price,2));
         /*BOX.$form.id_order.val(order.id);
        BOX.$form.price.val(order.price);
        BOX.$form.tip.val('');
        BOX.$form.promo_amt.val('');
        BOX.$form.promo_pct.val('');
        BOX.$form.card.val('');
        BOX.$form.cash.val('');*/
        BOX.updateOrderList();
        BOX.updateListSubaccounts();
        BOX.updateTotal();

        BOX.addAccountToPay(0);
    },

    updateOrderList: function(){

        /*var html = '';
        $.each(BOX.order.ordpros, function(id,o){

            var state = '';
            switch(o.state){
                case '1': state = '<span class="badge pull-right" style="background:#1991EB"> Solicitado </span>'; break;
                case '2': state = '<span class="badge pull-right" style="background:#F3C200"> Preparando </span>'; break;
                case '3': state = '<span class="badge pull-right" style="background:#26C281"> Despachado </span>'; break;
            }

            html += '<tr id="propre_'+id+'">';
            html += ' <td> '+id+' ::  '+o.product+' - '+o.propre+' '+state+' </td>';
            html += ' <td> '+stg.coin +num(o.price_unit,2)+' </td>';
            html += ' <td class="ctr"> '+o.quantity+' </td>';
            html += ' <td> '+stg.coin +num(o.price_discount,2,0)+' </td>';
            html += ' <td> '+stg.coin +num(o.price_total,2)+' </button> </td>';
            html += ' <td>';

            if(BOX.accounts.length == 0){
                html += '<button class="btn btn-circle btn-default btn-block"> <i class="fa fa-trash"></i> </button>';
            } else {
                html += '<div class="btn-group">';
                html += ' <a class="btn btn-default dropdown-toggle" data-toggle="dropdown" href="javascript:;" aria-expanded="false">';
                html += '  <i class="fa fa-ellipsis-v"></i>';
                html += ' </a>';
                html += ' <ul class="dropdown-menu">';
                BOX.accounts.forEach(function(o,i){
                    html += '  <li> <a onclick="BOX.addOrdproToAccount('+id+','+i+');"> Mover a cuenta '+(i+1)+' </a> </li>';
                    //html += '<button class="btn btn-circle btn-default" onclick="BOX.addOrdproToAccount('+id+','+i+');"> C'+(i+1)+' </button>';
                });
                html += '  <li> <a href="javascript:;"> Eliminar </a> </li>';
                html += ' </ul>';
                html += '</div>';
            }

            html += ' </td>';
            html += '</tr>';
        });
        BOX.$order.list.html(html);*/
    },

    promoAmtChanged: function(){
        var promo_amt = num(BOX.$form.promo_amt.val()),
            total_price = BOX.getTotalPrice();

        var promo_pct = (promo_amt/total_price)*100;

        if(promo_pct==0){
            BOX.$form.promo_pct.val('');
        } else {
            BOX.$form.promo_pct.val(promo_pct);
        }
        BOX.updateTotal();
    },

    promoPctChanged: function(){
        var pcp = num(BOX.$form.promo_pct.val()),
            price = BOX.getTotalPrice();

        var promo_amt = (pcp / 100) * price;

        if(promo_amt==0){
            BOX.$form.promo_amt.val('');
        } else {
            BOX.$form.promo_amt.val(num(promo_amt));
        }
        BOX.updateTotal();
    },

    getTotalPrice: function(){
        var price = BOX.account.total,
            tip = num(BOX.$form.tip.val()),
            promo = num(BOX.$form.promo_amt.val());
        return (price + tip)-promo;
    },

    updateTotal: function(){
        var card = num(BOX.$form.card.val()),
            cash = num(BOX.$form.cash.val());

        var total = BOX.getTotalPrice(),
            give = (cash+card)-total;

        if(give < 0){
            BOX.$form.give.css('background-color','#1991EB');
            BOX.$form.give.txt.html('FALTA');
            BOX.$form.give.amount.html(num(Math.abs(give),2));
        } else if(give == 0){
            BOX.$form.give.css('background-color','#26C281');
            BOX.$form.give.txt.html('EXACTO');
            BOX.$form.give.amount.html(num(total,2));
        } else if(give > 0) {
            BOX.$form.give.css('background-color','#e7505a');
            BOX.$form.give.txt.html('VUELTO');
            BOX.$form.give.amount.html(num(give,2));
        } else {
            BOX.$form.give.css('background-color','#000');
            BOX.$form.give.txt.html('¿..?');
            BOX.$form.give.amount.html(num(give,2));
        }
        /*BOX.$form.give.txt.html('FALTA');
        BOX.$form.give.amount.html(num(give,2));*/

        BOX.$form.total.set(num(total,2));
    },

    addNextAccountToPay: function(){
        var next_ia = -1;
        BOX.accounts.forEach(function(a,ia){
            if(!a.paid){
                next_ia = ia;
                return false;
            }
        });
        if(next_ia >= 0){
            BOX.addAccountToPay(next_ia);
        } else {
            BOX.loadTables();
            BOX.$rooms.open();
        }
    },

    addAccountToPay: function(ia){
        var total = 0;
        var ordpros = BOX.accounts[ia].ordpros;
        $.each(ordpros, function(id,o){
            total += num(o.price_total);
        });
        BOX.account = {
            name: ia,
            total: total,
            ordpros: ordpros
        };

        BOX.$form.title.html('Cuenta '+padIx(ia));
        BOX.$form.account.val(ia);
        BOX.$form.id_order.val(BOX.order.id);
        BOX.$form.price.val(total);
        BOX.$form.tip.val('');
        BOX.$form.promo_amt.val('');
        BOX.$form.promo_pct.val('');
        BOX.$form.card.val('');
        BOX.$form.cash.val('');
        BOX.updateTotal();
        //BOX.$form.total.set(num(total,2));
    },

    printPreAccount: function(ia){
        var total = 0;
        var ordpros = BOX.accounts[ia].ordpros;
        $.each(ordpros, function(id,o){
            total += num(o.price_total);
        });

        console.log('order: ', BOX.order);
        console.log('total: ', total);
        console.log('ordpros: ', ordpros);

        Print.precuenta(BOX.order.id,total,ordpros);
    }
};

// Autocompletado de clientes
var AClient = {

    init: function(){
        BOX.$form.client.name.autocomplete({
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
                    return $("<li>").append('<a>' + item.name + '</a>').appendTo(ul);
                }
            }
        });
        BOX.$form.client.name.keypress(this.clean);
        BOX.$form.client.name.keyup(function(e){ if(e.keyCode == 8) AClient.clean(); });
    },

    assign: function(item){
        BOX.$form.client.set(item.id, item.name);
    },

    clean: function(){
        if(BOX.$form.client.id.val() > 0){
            BOX.$form.client.set('', '');
        }
    }
};

// Modal Anular
var MAnnul = {

    id_regbox: 0,

    list: null,

    $modal: null,

    init: function(callback){

        if(this.$modal != null) return;

        $body.append(
            '<div id="modal_annul" class="modal fade modal-scroll" tabindex="-1">'+
            ' <div class="modal-dialog modal-lg">'+
            '  <div class="modal-content">'+
            '   <div class="modal-header">'+
            '    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>'+
            '    <h4 class="modal-title">Anulación de ventas</h4>'+
            '   </div>'+
            '   <div class="modal-body">'+
            '    <table class="table table-bordered table-striped mdl-td" style="margin-bottom:0">'+
            '     <thead>'+
            '      <tr>'+
            '       <th>Fecha</th>'+
            '       <th>Cliente</th>'+
            '       <th>Documento</th>'+
            '       <th>N° Doc.</th>'+
            '       <th>Total</th>'+
            '       <th width="1%"></th>'+
            '      </tr>'+
            '     </thead>'+
            '     <tbody class="list"></tbody>'+
            '    </table>'+
            '   </div>'+
            '   <div class="modal-footer">'+
            '    <button type="button" data-dismiss="modal" class="btn cancel">Cerrar</button>'+
            '   </div>'+
            '  </div>'+
            ' </div>'+
            '</div>'
        );

        this.$modal         = $('#modal_annul');
        this.$modal.list    = $('.list', this.$modal);
    },

    getBoxTransactions: function(){
        if(MAnnul.items==null){
            MAnnul.$modal.list.html('<tr><td colspan="6"><i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i> Sincronizando...</td></tr>');
        }
        api('ajax/box.php', {action:'get_transactions', id_regbox:MAnnul.id_regbox}, function(rsp){
            if(rsp.ok){
                MAnnul.setItems(rsp.items);
            } else {
                bootbox.alert(rsp.msg);
            }
        }, false);
    },

    setItems: function(items){
        MAnnul.init();

        var html = '';
        console.log(items);
        if(items.length > 0){
            MAnnul.items = items;
            MAnnul.items.forEach(function(o,i){
                html += '<tr>';
                html += '<td> '+ o.date_added +' </td>';
                html += '<td> '+ (o.client==null ? 'Público en General' : o.client) +' </td>';
                html += '<td> '+ o.proof +' </td>';
                html += '<td> '+ pad(o.id,7) +' </td>';
                html += '<td> '+ stg.coin + num(o.total,2) +' </td>';
                html += '<td> <button class="btn red-mint btn-circle btn-block" onclick="MAnnul.annul(MAnnul.items['+i+']);">Anular</button> </td>';
                html += '</tr>';
            });
        } else {
            html = '<tr><td colspan="6"> No se han realizado transacciones. </td></tr>';
        }
        MAnnul.$modal.list.html(html);
    },

    // Anular
    annul: function(o){
        bootbox.confirm({
            message: '¿Está seguro de que desea cancelar esta transacción?',
            buttons: {
                cancel: {label: 'No'},
                confirm: {label: 'Anular', className: 'btn-danger'}
            },
            callback: function(result){
                if(!result) return;
                api('ajax/box.php', {action:'annul_transaction',id:o.id}, function(rsp){
                    if(rsp.ok == true){
                        toastr.success('Transacción anulada');
                        MAnnul.setItems(rsp.items);
                    } else {
                        bootbox.alert(rsp.msg);
                    }
                }, 'Anulando...');
            }
        });
    },

    // Abrir para nuevo NUEVO
    open: function(id_regbox){
        MAnnul.id_regbox = id_regbox;
        MAnnul.init();
        MAnnul.getBoxTransactions();
        MAnnul.$modal.modal('show');
    }

};

function padIx(n){
    n = n+1;
    return n<10 ? '0'+n : n;
}