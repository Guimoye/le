// Modal Expense
var MObligation = {

    callback: null,

    $modal: null,
    $title: null, // Modal: Titulo
    $form: null, // Modal: Formulario

    $remove: null,

    init: function(callback){
        this.callback = (typeof callback === 'function') ? callback : null;

        this.$modal         = $('#modal_add_obligation');
        this.$modal.title   = $('.modal-title', this.$modal);
        this.$modal.remove  = $('.remove', this.$modal);

        this.$form              = $('form', this.$modal);
        this.$form.id           = $('input[name="id"]', this.$form);
        this.$form.description  = $('input[name="description"]', this.$form);
        this.$form.amount       = $('input[name="amount"]', this.$form);
        this.$form.date_end     = $('input[name="date_end"]', this.$form);

        // Asignar eventos
        this.$modal.remove.click(function(){
            MObligation.remove(MObligation.$form.id.val());
        });
        $('.save', this.$modal).click(this.save);

    },

    // Guardar
    save: function(){
        api('obligations/add', MObligation.$form.serializeObject(), function(rsp){
            if(rsp.ok == true){
                toastr.success('Guardado correctamente');
                MObligation.$modal.modal('hide');

                if(MObligation.callback == null){
                    location.reload();
                } else {
                    MObligation.callback(rsp.id, false);
                }

            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Registrando...');
    },
    
    // Abrir para nuevo NUEVO
    add: function(){
        MObligation.$modal.title.text("Agregar Oblicación");
        MObligation.$modal.remove.hide();

        MObligation.$form.id.val('');
        MObligation.$form.description.val('');
        MObligation.$form.amount.val('');
        MObligation.$form.date_end.val('');

        MObligation.$modal.modal('show');
    },
    
    // Editar
    edit: function(o){
        MObligation.$modal.title.text("Editar Oblicación");
        MObligation.$modal.remove.show();

        MObligation.$form.id.val(o.id);
        MObligation.$form.description.val(o.description);
        MObligation.$form.amount.val(o.amount);
        MObligation.$form.date_end.val(o.date_end);

        MObligation.$modal.modal('show');
    },

    // Eliminar
    remove: function(id){
        bootbox.confirm('¿Realmente desea eliminar?', function(result){
            if(result){
                api('obligations/remove', {action:'remove', id:id}, function(rsp){
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
                api('obligations/set_paid', {action:'set_paid', id:id}, function(rsp){
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