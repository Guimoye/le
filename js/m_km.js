// Modal Expense
var MKm = {

    callback: null,

    $modal: null,
    $title: null, // Modal: Titulo
    $form: null, // Modal: Formulario

    $remove: null,

    init: function(callback){
        this.callback = (typeof callback === 'function') ? callback : null;

        this.$modal         = $('#modal_add_km');
        this.$modal.title   = $('.modal-title', this.$modal);
        this.$modal.remove  = $('.remove', this.$modal);

        this.$form          = $('form', this.$modal);
        this.$form.id       = $('input[name="id"]', this.$form);
        this.$form.km       = $('input[name="km"]', this.$form);

        // Asignar eventos
        this.$modal.remove.click(function(){
            MKm.remove(MKm.$form.id.val());
        });
        this.$form.submit(function(e){
            e.preventDefault();
            MKm.save();
        });
        $('.save', this.$modal).click(this.save);

    },

    // Guardar
    save: function(){
        api('kms/add', MKm.$form.serializeObject(), function(rsp){
            if(rsp.ok == true){
                toastr.success('Guardado correctamente');
                MKm.$modal.modal('hide');

                if(MKm.callback == null){
                    location.reload();
                } else {
                    MKm.callback(rsp.id, false);
                }

            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Registrando...');
    },
    
    // Abrir para nuevo NUEVO
    add: function(){
        MKm.$modal.title.text("Agregar Km");
        MKm.$modal.remove.hide();

        MKm.$form.id.val('');
        MKm.$form.km.val('');

        MKm.$modal.modal('show');
    },
    
    // Editar
    edit: function(o){
        MKm.$modal.title.text("Editar Km");
        MKm.$modal.remove.show();

        MKm.$form.id.val(o.id);
        MKm.$form.km.val(o.km);

        MKm.$modal.modal('show');
    },

    // Eliminar
    remove: function(id){
        bootbox.confirm('¿Realmente desea eliminar?', function(result){
            if(result){
                api('kms/remove', {id:id}, function(rsp){
                    if(rsp.ok == true){
                        toastr.success('Eliminado correctamente');
                        location.reload();
                    } else {
                        bootbox.alert(rsp.msg);
                    }
                }, 'Eliminando...');
            }
        });
    }

};