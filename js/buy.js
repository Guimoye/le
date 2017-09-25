var Buy = {

    $modal: null,
    $formSupply: null,
    $cart: null,

    $mdi: null,

    supply: null,

    supplies: {},

    init: function(){

        this.$modal = $('#modal_add_buy');

        this.$formSupply = $('.form_supply');
        this.$formSupply.id          = $('input[name=id]', this.$formSupply);
        this.$formSupply.name        = $('input[name=name]', this.$formSupply);
        this.$formSupply.unimed      = $('.unimed', this.$formSupply);
        this.$formSupply.quantity    = $('input[name=quantity]', this.$formSupply);
        this.$formSupply.price       = $('input[name=price]', this.$formSupply);
        this.$formSupply.stock       = $('input[name=stock]', this.$formSupply);

        this.$cart          = $('.form_cart');
        this.$cart.list     = $('.cart_list');
        this.$cart.total    = $('input[name=total]', this.$cart);


        // Eventos
        $('.add', this.$formSupply).click(this.addSupplyToCart);

        CSupply.init(this.setSupplyEdit);
        //CSupply.choose();
    },

    add: function(){
        Buy.$modal.modal('show');
    },

    setSupplyEdit: function(s){
        Buy.supply = s;
        Buy.$formSupply.id.val(s.id);
        Buy.$formSupply.name.val(s.name);
        Buy.$formSupply.unimed.html(s.un_name);
        Buy.$formSupply.price.val(s.cost);
        //Buy.$formSupply.cost.attr('placeholder',s.cost);
        Buy.$formSupply.stock.val(s.stock);
    },

    addSupplyToCart: function(){
        if(Buy.supply==null){
            return;
        }
        var supply = Buy.supply;
        supply.quantity = num(Buy.$formSupply.quantity.val());
        supply.price = num(Buy.$formSupply.price.val());
        if(supply.quantity <= 0){
            bootbox.alert('Ingrese la cantidad');
        } else if(supply.price < 0){
            bootbox.alert('Ingrese el precio');
        } else {
            Buy.supplies[supply.id] = supply;
            Buy.updateCart();
        }
    },

    removeSupply: function(id){
        delete Buy.supplies[id];
        Buy.updateCart();
    },

    cleanCart: function(){
        bootbox.confirm({
            message: '¿Seguro que quieres vaciar tu carrito?',
            buttons: {
                cancel: {label:'Cancelar'},
                confirm: {label:'Si, vaciar'}
            },
            callback: function(result){
                if(result){
                    Buy.supplies = {};
                    Buy.updateCart();
                }
            }
        });
    },

    updateCart: function(){
        var html = '';
        var total = 0;
        $.each(Buy.supplies, function(id,o){
            var amount = num(o.price) * num(o.quantity);

            total += amount;

            html += '<tr>';
            html += ' <td> '+o.name+' </td>';
            html += ' <td> '+o.un_name+' </td>';
            html += ' <td> '+o.quantity+' </td>';
            html += ' <td> '+stg.coin+num(o.price,2)+' </td>';
            html += ' <td> '+stg.coin+num(amount,2)+' </td>';
            html += ' <td style="padding:5px">';
            html += '  <button class="btn btn-primary btn-sm btn-block btn-circle" onclick="Buy.setSupplyEdit(Buy.supplies['+id+']);">';
            html += '   <i class="fa fa-pencil"></i>';
            html += '  </button>';
            html += ' </td>';
            html += ' <td style="padding:5px">';
            html += '  <button class="btn btn-danger btn-sm btn-block btn-circle" onclick="Buy.removeSupply('+id+');">';
            html += '   <i class="fa fa-trash"></i>';
            html += '  </button>';
            html += ' </td>';
            html += '</tr>';
        });
        Buy.$cart.list.html(html);
        Buy.$cart.total.val(num(total,2));
    },

    save: function(){
        var supplies = [];
        $.each(Buy.supplies, function(id,o){
            supplies.push({
                id: o.id,
                price: o.price,
                quantity: o.quantity
            });
        });

        var data = Buy.$cart.serializeObject();
        data.action = 'add';
        data.supplies = supplies;
        api('ajax/purchases.php', data, function(rsp){
            if(rsp.ok){
                toastr.success('Guardado correctamente');
                Buy.$modal.modal('hide');
                location.reload();
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Guardando...');
    },

    // Extraer y mostrar lista de productos de una compra
    show: function(id_purchase){
        if(Buy.$mdi == null){
            Buy.$mdi = $('#modal_items_sups');
            Buy.$mdi.list = $('.list', Buy.$mdi);
        }
        api('ajax/purchases.php', {action:'get_supplies',id_purchase:id_purchase}, function(rsp){
            if(rsp.ok){
                var html = '';
                rsp.items.forEach(function(o,i){
                    html += '<tr>';
                    html += ' <td> '+o.su_name+' </td>';
                    html += ' <td> '+o.un_name+' </td>';
                    html += ' <td> '+o.quantity+' </td>';
                    html += ' <td> '+stg.coin+num(o.price,2)+' </td>';
                    html += ' <td> '+stg.coin+num(o.total,2)+' </td>';
                    html += '</tr>';
                });
                Buy.$mdi.list.html(html);
                Buy.$mdi.modal('show');
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Obteniendo productos...', true);
    },

    goExport: function(){
        var data = $('#filters').serialize();
        location.href = 'purchases_export.php?'+data;
    }

};

function padIx(n){
    n = n+1;
    return n<10 ? '0'+n : n;
}