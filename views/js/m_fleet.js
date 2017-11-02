// Modal Expense
var MFleet = {

    callback: null,

    $modal: null,
    $title: null, // Modal: Titulo
    $form: null, // Modal: Formulario

    $remove: null,

    init: function(callback){
        this.callback = (typeof callback === 'function') ? callback : null;

        this.$modal         = $('#modal_add_fleet');
        this.$modal.title   = $('.modal-title', this.$modal);
        this.$modal.remove  = $('.remove', this.$modal);

        this.$form          = $('form', this.$modal);
        this.$form.id       = $('input[name="id"]', this.$form);
        this.$form.name     = $('input[name="name"]', this.$form);

        // Asignar eventos
        this.$modal.remove.click(function(){
            MFleet.remove(MFleet.$form.id.val());
        });
        this.$form.submit(function(e){
            e.preventDefault();
            MFleet.save();
        });
        $('.save', this.$modal).click(this.save);

    },

    // Guardar
    save: function(){
        api('fleets/add', MFleet.$form.serializeObject(), function(rsp){
            if(rsp.ok == true){
                toastr.success('Guardado correctamente');
                MFleet.$modal.modal('hide');

                if(MFleet.callback == null){
                    location.reload();
                } else {
                    MFleet.callback(rsp.id, false);
                }

            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Registrando...');
    },
    
    // Abrir para nuevo NUEVO
    add: function(){
        MFleet.$modal.title.text("Agregar Flota");
        MFleet.$modal.remove.hide();

        MFleet.$form.id.val('');
        MFleet.$form.name.val('');

        MFleet.$modal.modal('show');
    },
    
    // Editar
    edit: function(o){
        MFleet.$modal.title.text("Editar Flota");
        MFleet.$modal.remove.show();

        MFleet.$form.id.val(o.id);
        MFleet.$form.name.val(o.name);

        MFleet.$modal.modal('show');
    },

    // Eliminar
    remove: function(id){
        bootbox.confirm('¿Realmente desea eliminar?', function(result){
            if(result){
                api('fleets/remove', {id:id}, function(rsp){
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