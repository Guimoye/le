var KTC = {

    ordpros: {},

    $acts: null,
    $pendings: null,
    $preparing: null,

    init: function(){
        this.$acts                  = $('#acts');
        this.$acts.id_area =         $('.id_area', this.$acts);
        this.$acts.reload           = $('.reload', this.$acts);
        this.$acts.show_dispatched  = $('.show_dispatched', this.$acts);
        this.$pendings = $('#pendings');
        this.$pendings.opts = $('.pdg_opt');
        this.$preparing = $('#preparing');
        this.$dispatched = $('#dispatched');
        this.$dispatched.list = $('tbody', this.$dispatched);

        // Eventos
        this.$acts.id_area.change(function(){
            KTC.loadOrders();
            Cookies.set('id_area',KTC.$acts.id_area.val());
        });
        this.$acts.reload.click(this.loadOrders);
        this.$acts.show_dispatched.click(function(){
            if(KTC.$dispatched.is(':visible')){
                KTC.$acts.show_dispatched.html('Ver despachados');
                KTC.$dispatched.slideUp();
            } else {
                KTC.$acts.show_dispatched.html('Ocultar despachados');
                KTC.$dispatched.slideDown();
            }
        });

        this.$pendings.on('click','tr', function(){
            MOPdg.open(KTC.ordpros[$(this).data('id')]);
        });

        this.$preparing.on('click','tr', function(){
            MOPrp.open(KTC.ordpros[$(this).data('id')]);
        });

        MOPdg.init();
        MOPrp.init();

        this.loadOrders(true);
    },

    loadOrders: function(updateInterval){
        var update_after = (typeof updateInterval === 'boolean' && updateInterval);

        KTC.$acts.reload.attr('disabled',true);

        api('ajax/orders.php', {action:'get_orders', id_area:KTC.$acts.id_area.val()}, function(rsp){
            KTC.$acts.reload.attr('disabled',false);
            if(rsp.ok){
                // Alertar nuevos
                if(!$.isEmptyObject(KTC.ordpros)){
                    var hasNew = false;
                    $.each(rsp.ordpros, function(id,o){
                        if(typeof KTC.ordpros[id] === 'undefined'){
                            hasNew = true;
                        }
                    });
                    if(hasNew){
                        toastr.warning('Hay nuevos pedidos');
                    }
                }

                KTC.ordpros = rsp.ordpros;
                KTC.updateLists();
            }
            if(update_after) setTimeout(function(){KTC.loadOrders(true)}, 5000)
        }, false);
    },

    updateLists: function(){
        var html_pendings = '',
            html_preparing = '',
            h_dsp = '';

        $.each(KTC.ordpros, function(id,o){

            var pct = (o.time_trans / o.time) * 100,
                color = '';

            if(pct > 95){
                color = '#E43A45';
            } else if(pct > 75){
                color = '#EA7129';
            } else if(pct > 50){
                color = '#F2BE02';
            } else if(pct > 25){
                color = '#ACC22D';
            } else {
                color = '#26C281';
            }

            var hh = '';
            hh += '<tr data-id="'+id+'">';
            hh += ' <td> <b>'+o.id+'</b> </td>';
            hh += ' <td> '+o.ta_name+' </td>';
            hh += ' <td> '+o.us_name+' </td>';
            hh += ' <td> <b>'+o.quantity+'</b> </td>';
            hh += ' <td>';
            hh += '  <b>'+o.pr_name+' - '+o.pp_name+'</b>';
            if(o.notes!='') hh += '  <div class="ord_notes">'+o.notes+'</div>';
            hh += ' </td>';
            hh += ' <td> <span class="label" style="background:'+color+'"> '+num(o.time_trans,0)+'\' </span> </td>';
            hh += '</tr>';

            if(o.state == 1){
                html_pendings += hh;
            } else if(o.state == 2){
                html_preparing += hh;
            } else if(o.state == 3){
                h_dsp += '<tr>';
                h_dsp += ' <td> '+ o.id +' </td>';
                h_dsp += ' <td> '+ o.date_added +' </td>';
                h_dsp += ' <td> '+ o.date_dispatched +' </td>';
                h_dsp += ' <td> '+ o.us_name +' </td>';
                h_dsp += ' <td> '+ o.ta_name +' </td>';
                h_dsp += ' <td> '+ o.pr_name +' - '+ o.pp_name +' </td>';
                h_dsp += ' <td> '+ o.quantity +' </td>';
                h_dsp += ' <td> '+ o.notes +' </td>';
                h_dsp += ' <td> <button class="btn blue btn-circle" onclick="KTC.prepare('+o.id+',0)">Mover a preparación</button> </td>';
                h_dsp += '<tr>';
            }
        });

        KTC.$pendings.html(html_pendings);
        KTC.$preparing.html(html_preparing);
        KTC.$dispatched.list.html(h_dsp);
    },


    prepare: function(id,time){
        api('ajax/orders.php', {action:'prepare', id:id, time:time}, function(rsp){
            if(rsp.ok){
                //KTC.loadOrders();
                toastr.success('Enviado correctamente');
                KTC.ordpros[id].state = 2;
                KTC.updateLists();
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Enviando a preparación...');
    },
    dispatch: function(id){
        api('ajax/orders.php', {action:'dispatch', id:id}, function(rsp){
            if(rsp.ok){
                //KTC.loadOrders();
                toastr.success('Despachado correctamente');
                KTC.ordpros[id].state = 3;
                KTC.updateLists();
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Despachando...');
    },
    remove: function(id){
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
                data.id = id;
                api('ajax/request.php', data, function(rsp){
                    if(rsp.ok){
                        toastr.success('Eliminado correctamente');
                        KTC.ordpros[id].state = 0;
                        KTC.updateLists();
                    } else {
                        bootbox.alert(rsp.msg);
                    }
                }, 'Eliminando...');
            }
        });
    }

};

var MOPdg = {
    $modal: null,

    ordpro: null,

    init: function(){
        this.$modal = $('#modal_opts_pdg');
        this.$modal.title = $('.modal-title', this.$modal);

        $('.time', this.$modal).click(function(){
            KTC.prepare(MOPdg.ordpro.id, $(this).data('time'));
            MOPdg.$modal.modal('hide');
        });

        $('.dispatch', this.$modal).click(function(){
            KTC.dispatch(MOPdg.ordpro.id);
            MOPdg.$modal.modal('hide');
        });

        $('.remove', this.$modal).click(function(){
            KTC.remove(MOPdg.ordpro.id);
            MOPdg.$modal.modal('hide');
        });

        //this.open();
    },

    open: function(op){
        MOPdg.ordpro = op;

        MOPdg.$modal.title.html('<b>'+op.ta_name+'</b> : '+op.pr_name+' - '+op.pp_name+'');
        MOPdg.$modal.modal('show');
    }

};

var MOPrp = {
    $modal: null,

    ordpro: null,

    init: function(){
        this.$modal = $('#modal_opts_prp');
        this.$modal.title = $('.modal-title', this.$modal);

        $('.dispatch', this.$modal).click(function(){
            KTC.dispatch(MOPrp.ordpro.id);
            MOPrp.$modal.modal('hide');
        });

        $('.remove', this.$modal).click(function(){
            KTC.remove(MOPrp.ordpro.id);
            MOPrp.$modal.modal('hide');
        });

        //this.open();
    },

    open: function(op){
        MOPrp.ordpro = op;

        MOPrp.$modal.title.html('<b>'+op.ta_name+'</b> : '+op.pr_name+' - '+op.pp_name+'');
        MOPrp.$modal.modal('show');
    }

};