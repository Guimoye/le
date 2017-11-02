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

};

// Modal Voucher
var MVoucher = {

    callback: null,

    $modal: null,
    $title: null, // Modal: Titulo
    $form: null, // Modal: Formulario

    $remove: null,

    init: function(callback){
        this.callback = (typeof callback === 'function') ? callback : null;

        this.$modal         = $('#modal_add_voucher');
        this.$modal.title   = $('.modal-title', this.$modal);
        this.$modal.remove  = $('.remove', this.$modal);

        this.$form          = $('form', this.$modal);
        this.$form.id       = $('input[name="id"]', this.$form);
        this.$form.photo    = $('input[name="photo"]', this.$form);
        this.$form.image    = $('.image', this.$form);

        // Asignar eventos
        this.$form.ajaxForm(this.save);
        this.$form.photo.change(function(){
            MVoucher.$form.submit();
            MVoucher.$form.photo.val('');
        });
        this.$modal.remove.click(function(){
            MVoucher.remove(MVoucher.$id.val());
        });
        $('.save', this.$modal).click(this.save);
    },

    // Guardar
    save: {
        beforeSend: function() {
            Loading.show();
        },
        complete: function(xhr) {
            Loading.hide();
            var rsp = JSON.parse(xhr.responseText);
            if(rsp.ok == true){
                //MVoucher.$modal.modal('hide');
                toastr.success('Guardado correctamente');
                //location.reload();
                MVoucher.open(MVoucher.$form.id.val(), rsp.pic_voucher)
            } else {
                bootbox.alert(rsp.message);
            }
        }
    },

    // Abrir para nuevo NUEVO
    open: function(id,pic_voucher){
        MVoucher.$modal.title.text('Comprobante de pago');
        MVoucher.$modal.modal('show');
        MVoucher.$form.id.val(id);
        MVoucher.$form.image.attr('src','uploads/'+pic_voucher+'.jpg');
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

        this.$form          = $('form', this.$modal);
        this.$form.id       = $('input[name="id"]', this.$form);
        this.$form.days     = $('input[name="days[]"]', this.$form);
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
    open: function(id,free_days){
        MDays.$modal.title.text('Días libres');
        MDays.$modal.modal('show');
        MDays.$form.id.val(id);

        var arr = free_days.split(',');


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

        /*MDays.$form.day_0.prop('checked', arr.includes('0'));
        MDays.$form.day_1.prop('checked', arr.includes('1'));
        MDays.$form.day_2.prop('checked', arr.includes('2'));
        MDays.$form.day_3.prop('checked', arr.includes('3'));
        MDays.$form.day_4.prop('checked', arr.includes('4'));
        MDays.$form.day_5.prop('checked', arr.includes('5'));*/

        $.uniform.update();
    }

};