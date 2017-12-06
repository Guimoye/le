// Modal Driver
var MDuesRental = {

    callback: null,

    title_add:  'Generar Cronograma de Alquiler',
    title_edit: 'Editar conductor',

    $modal: null,
    $title: null, // Modal: Titulo
    $form: null, // Modal: Formulario

    $remove: null,

    init: function(callback){
        this.callback = (typeof callback === 'function') ? callback : null;

        this.$modal         = $('#modal_add_dues_rental');
        this.$modal.title   = $('.modal-title', this.$modal);
        this.$modal.remove  = $('.remove', this.$modal);

        this.$form                      = $('form', this.$modal);
        this.$form.id                   = $('input[name="id"]', this.$form);
        this.$form.surname 			    = $('input[name="surname"]', this.$form);

        // Asignar eventos
        this.$modal.remove.click(function(){
            MDuesRental.remove(MDuesRental.$id.val());
        });
        $('.save', this.$modal).click(this.save);

    },

    // Guardar
    save: function(){
        api('dues_rental/add', MDuesRental.$form.serializeObject(), function(rsp){
            if(rsp.ok == true){
                toastr.success('Guardado correctamente');
                MDuesRental.$modal.modal('hide');

                if(MDuesRental.callback == null){
                    location.reload();
                } else {
                    MDuesRental.callback(rsp.id, false);
                }

            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Registrando...');
    },
    
    // Abrir para nuevo NUEVO
    add: function(){
        MDuesRental.$modal.title.text(MDuesRental.title_add);
        MDuesRental.$modal.remove.hide();

        MDuesRental.$form.id.val('');
        MDuesRental.$form.surname.val('');

        MDuesRental.$modal.modal('show');
    },
    
    // Editar
    edit: function(o){
        MDuesRental.$modal.title.text(MDuesRental.title_edit);
        MDuesRental.$modal.remove.show();

        MDuesRental.$form.id.val(o.id);
        MDuesRental.$form.surname.val(o.surname);

        MDuesRental.$modal.modal('show');
    },

    // Eliminar
    remove: function(id){
        bootbox.confirm('¿Realmente desea eliminar?', function(result){
            if(result){
                api('dues_rental/remove', {action:'remove', id:id}, function(rsp){
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

    // Eliminar Cronograma de alquiler de un conductor
    removeAll: function(id_driver){
        bootbox.confirm({
            title: '¿Seguro que quieres eliminar todo el cronograma de alquiler?',
            message: 'Esta acción borrará todas las cuotas de alquiler, incluso aquellas que han sido pagadas. No podrás deshacer más tarde.',
            buttons: {
                confirm: {label:'ELIMINAR',className:'btn-danger'},
                cancel: {label:'Cancelar'}
            },
            callback: function(result){
                if(result){
                    api('dues_rental/remove_all', {action:'remove', id_driver:id_driver}, function(rsp){
                        if(rsp.ok == true){
                            toastr.success('Eliminado correctamente');
                            location.reload();
                        } else {
                            bootbox.alert(rsp.msg);
                        }
                    }, 'Eliminando...');
                }
            }
        });
    },

    // Marcar como pagado
    setDuePaid: function(id, amount_total){
        bootbox.prompt({
            title: 'Pagar cuota pendiente',
            placeholder: 'Ingrese el monto a pagar',
            callback: function(result){
                if(result == null) return;
                api('dues_rental/set_due_paid', {action:'set_due_paid', id:id, amount_total:amount_total, amount:result}, function(rsp){
                    if(rsp.ok == true){
                        toastr.success('Guardado correctamente');
                        location.reload();
                    } else {
                        bootbox.alert(rsp.msg);
                    }
                });
            }
        });
        /*bootbox.confirm('¿Marcar como pagado?', function(result){
            if(result){
                api('dues_rental/set_due_paid', {action:'set_due_paid', id:id, amount:amount}, function(rsp){
                    if(rsp.ok == true){
                        toastr.success('Guardado correctamente');
                        location.reload();
                    } else {
                        bootbox.alert(rsp.msg);
                    }
                });
            }
        });*/
    },

    // Marcar como pagado
    setDueUnpaid: function(id){
        bootbox.confirm('¿Marcar como no pagado?', function(result){
            if(result){
                api('dues_rental/set_due_unpaid', {action:'set_due_unpaid', id:id}, function(rsp){
                    if(rsp.ok == true){
                        toastr.success('Guardado correctamente');
                        location.reload();
                    } else {
                        bootbox.alert(rsp.msg);
                    }
                });
            }
        });
    }

};// Modal Driver

var MDuesRentalPay = {

    $modal: null,
    $form: null, // Modal: Formulario

    $remove: null,

    init: function(){
        if(this.$modal != null) return;

        this.$modal         = $('#modal_pay_dues_rental');
        this.$modal.title   = $('.modal-title', this.$modal);

        this.$form                  = $('form', this.$modal);
        this.$form.id               = $('input[name="id"]', this.$form);
        this.$form.amount_total     = $('input[name="amount_total"]', this.$form);
        this.$form.amount_paid 	    = $('input[name="amount_paid"]', this.$form);
        this.$form.amount_cabify    = $('input[name="amount_cabify"]', this.$form);
        this.$form.amount_penalty   = $('input[name="amount_penalty"]', this.$form);
        this.$form.amount_discount  = $('input[name="amount_discount"]', this.$form);
        this.$form.date_paid        = $('input[name="date_paid"]', this.$form);

        // Asignar eventos
        //$('.save', this.$modal).click(this.save);
        this.$form.submit(function(e){
            e.preventDefault();
            MDuesRentalPay.save();
        });

        this.$modal.on('shown.bs.modal', function() {
            MDuesRentalPay.$form.amount_paid.focus();
        });
    },

    show: function(){
        MDuesRentalPay.init();
        MDuesRentalPay.$modal.modal('show');
    },

    // Guardar
    save: function(){
        api('dues_rental/set_due_paid', MDuesRentalPay.$form.serializeObject(), function(rsp){
            if(rsp.ok == true){
                toastr.success('Guardado correctamente');
                MDuesRentalPay.$modal.modal('hide');

                location.reload();

            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Registrando...');
    },

    // Pagar
    open: function(o){
        MDuesRentalPay.show();
        MDuesRentalPay.$modal.title.text('Pagar cuota');

        MDuesRentalPay.$form.id.val(o.id);
        MDuesRentalPay.$form.amount_total.val(o.amount_total);
        MDuesRentalPay.$form.amount_paid.val(o.amount_paid!='0' ? o.amount_paid : '');
        MDuesRentalPay.$form.amount_paid.attr('placeholder','Monto total: '+stg.coin+o.amount_total);
        //MDuesRentalPay.$form.amount_paid.val(o.amount_due);
        MDuesRentalPay.$form.amount_cabify.val(o.amount_cabify);
        MDuesRentalPay.$form.amount_penalty.val(o.amount_penalty);
        MDuesRentalPay.$form.amount_discount.val(o.amount_discount);
        MDuesRentalPay.$form.date_paid.val(o.date_paid==null?o.date_due:o.date_paid);

        console.log(o.date_paid);
    }

};

// Modal Dias
var MDays = {

    callback: null,

    $modal: null,
    $title: null, // Modal: Titulo
    $form: null, // Modal: Formulario

    $remove: null,

    init: function(callback){
        this.callback = (typeof callback === 'function') ? callback : null;

        this.$modal         = $('#modal_add_days');
        this.$modal.title   = $('.modal-title', this.$modal);
        this.$modal.remove  = $('.remove', this.$modal);
        this.$modal.save    = $('.save', this.$modal);

        this.$form          = $('form', this.$modal);
        this.$form.id       = $('input[name="id"]', this.$form);
        this.$form.days     = $('input[name="days[]"]', this.$form);
        this.$form.notes    = $('input[name="notes"]', this.$form);
        this.$form.day_0    = $('.day_0', this.$form);
        this.$form.day_1    = $('.day_1', this.$form);
        this.$form.day_2    = $('.day_2', this.$form);
        this.$form.day_3    = $('.day_3', this.$form);
        this.$form.day_4    = $('.day_4', this.$form);
        this.$form.day_5    = $('.day_5', this.$form);
        this.$form.day_6    = $('.day_6', this.$form);

        // Asignar eventos
        this.$modal.remove.click(function(){
            MDays.remove(MDays.$id.val());
        });
        this.$form.submit(function(e){
            e.preventDefault();
            MDays.save();
        });
        $('.save', this.$modal).click(this.save);
    },

    // Guardar
    save: function(){
        api('dues_rental/set_free_days', MDays.$form.serializeObject(), function(rsp){
        if(rsp.ok == true){
            toastr.success('Guardado correctamente');
            MDays.$modal.modal('hide');

            if(MDays.callback == null){
                location.reload();
            } else {
                MDays.callback(rsp.id, false);
            }

        } else {
            bootbox.alert(rsp.msg);
        }
    }, 'Registrando...');
    },

    // Abrir para nuevo NUEVO
    open: function(o, read_only){
        var readOnly = (typeof read_only == 'boolean' && read_only);

        MDays.$modal.title.text('Días libres');
        MDays.$modal.modal('show');
        MDays.$form.id.val(o.id);
        MDays.$form.notes.val(o.fd_notes);

        var arr = o.free_days.split(',');


        MDays.$form.days.prop('checked', false);
        MDays.$form.days.attr('disabled', false);

        if(arr.includes('0')){
            MDays.$form.day_0.prop('checked', true);
            MDays.$form.day_0.attr('disabled', true);
        }

        if(arr.includes('1')){
            MDays.$form.day_1.prop('checked', true);
            MDays.$form.day_1.attr('disabled', true);
        }

        if(arr.includes('2')){
            MDays.$form.day_2.prop('checked', true);
            MDays.$form.day_2.attr('disabled', true);
        }

        if(arr.includes('3')){
            MDays.$form.day_3.prop('checked', true);
            MDays.$form.day_3.attr('disabled', true);
        }

        if(arr.includes('4')){
            MDays.$form.day_4.prop('checked', true);
            MDays.$form.day_4.attr('disabled', true);
        }

        if(arr.includes('5')){
            MDays.$form.day_5.prop('checked', true);
            MDays.$form.day_5.attr('disabled', true);
        }

        if(arr.includes('6')){
            MDays.$form.day_6.prop('checked', true);
            MDays.$form.day_6.attr('disabled', true);
        }

        if(readOnly){
            MDays.$form.days.attr('disabled', true);
            MDays.$form.notes.attr('disabled', true);
            MDays.$modal.save.hide();
        } else {
            MDays.$form.notes.attr('disabled', false);
            MDays.$modal.save.show();
        }

        /*MDays.$form.day_0.prop('checked', arr.includes('0'));
        MDays.$form.day_1.prop('checked', arr.includes('1'));
        MDays.$form.day_2.prop('checked', arr.includes('2'));
        MDays.$form.day_3.prop('checked', arr.includes('3'));
        MDays.$form.day_4.prop('checked', arr.includes('4'));
        MDays.$form.day_5.prop('checked', arr.includes('5'));*/

        $.uniform.update();
    }

};