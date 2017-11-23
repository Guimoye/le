// Modal Expense
var MMaintenance = {

    callback: null,

    $modal: null,
    $title: null, // Modal: Titulo
    $form: null, // Modal: Formulario

    $remove: null,

    init: function(callback){
        this.callback = (typeof callback === 'function') ? callback : null;

        this.$modal         = $('#modal_add_maintenance');
        this.$modal.title   = $('.modal-title', this.$modal);
        this.$modal.remove  = $('.remove', this.$modal);

        this.$form                  = $('form', this.$modal);
        this.$form.id               = $('input[name="id"]', this.$form);
        this.$form.kms        = $('input[name="kms"]', this.$form);
        this.$form.amount           = $('input[name="amount"]', this.$form);
        this.$form.amount_stored    = $('input[name="amount_stored"]', this.$form);
        this.$form.date_item        = $('input[name="date_item"]', this.$form);

        // Asignar eventos
        this.$modal.remove.click(function(){
            MMaintenance.remove(MMaintenance.$form.id.val());
        });
        $('.save', this.$modal).click(this.save);

    },

    // Guardar
    save: function(){
        api('maintenances/add', MMaintenance.$form.serializeObject(), function(rsp){
            if(rsp.ok == true){
                toastr.success('Guardado correctamente');
                MMaintenance.$modal.modal('hide');

                if(MMaintenance.callback == null){
                    location.reload();
                } else {
                    MMaintenance.callback(rsp.id, false);
                }

            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Registrando...');
    },
    
    // Abrir para nuevo NUEVO
    add: function(){
        MMaintenance.$modal.title.text("Agregar Mantenimiento");
        MMaintenance.$modal.remove.hide();

        MMaintenance.$form.id.val('');
        MMaintenance.$form.kms.val('');
        MMaintenance.$form.amount.val('');
        MMaintenance.$form.amount_stored.val('');
        MMaintenance.$form.date_item.val('');

        MMaintenance.$modal.modal('show');
    },
    
    // Editar
    edit: function(o){
        MMaintenance.$modal.title.text("Editar Mantenimiento");
        MMaintenance.$modal.remove.show();

        MMaintenance.$form.id.val(o.id);
        MMaintenance.$form.kms.val(o.kms);
        MMaintenance.$form.amount.val(o.amount);
        MMaintenance.$form.amount_stored.val(o.amount_stored);
        MMaintenance.$form.date_item.val(o.date_item);

        MMaintenance.$modal.modal('show');
    },

    // Eliminar
    remove: function(id){
        bootbox.confirm('¿Realmente desea eliminar?', function(result){
            if(result){
                api('maintenances/remove', {action:'remove', id:id}, function(rsp){
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
    setPaid: function(id){
        bootbox.confirm('¿Marcar como pagado?', function(result){
            if(result){
                api('maintenances/set_paid', {action:'set_paid', id:id}, function(rsp){
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


var MMaintenancePay = {

    $modal: null,
    $form: null, // Modal: Formulario

    $remove: null,

    init: function(){
        if(this.$modal != null) return;

        this.$modal         = $('#modal_pay_maintenance');
        this.$modal.title   = $('.modal-title', this.$modal);

        this.$form                  = $('form', this.$modal);
        this.$form.id               = $('input[name="id"]', this.$form);
        this.$form.ids_dues_rental  = $('input[name="ids_dues_rental"]', this.$form);
        this.$form.amount           = $('input[name="amount"]', this.$form);
        this.$form.amount_stored    = $('input[name="amount_stored"]', this.$form);
        this.$form.date_paid        = $('input[name="date_paid"]', this.$form);

        // Asignar eventos
        $('.save', this.$modal).click(this.save);
    },

    show: function(){
        MMaintenancePay.init();
        MMaintenancePay.$modal.modal('show');
    },

    // Guardar
    save: function(){
        api('maintenances/set_paid', MMaintenancePay.$form.serializeObject(), function(rsp){
            if(rsp.ok == true){
                toastr.success('Guardado correctamente');
                MMaintenancePay.$modal.modal('hide');

                location.reload();

            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Registrando...');
    },

    // Pagar
    open: function(o){
        // Obtener el pozo para este mantenimiento
        api('maintenances/get_pit_to_maintenance', {id:o.id}, function(rsp){
            if(rsp.ok){

                MMaintenancePay.show();
                MMaintenancePay.$modal.title.text('Pagar mantenimiento');

                MMaintenancePay.$form.id.val(o.id);
                MMaintenancePay.$form.ids_dues_rental.val(rsp.ids_dues_rental);
                MMaintenancePay.$form.amount.val(o.amount);
                MMaintenancePay.$form.amount_stored.val(rsp.pit);
                MMaintenancePay.$form.date_paid.val(o.date_item);

            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Obteniendo pozo de mantenimiento');
    }

};