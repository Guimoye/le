// Modal Driver
var MDriver = {

    callback: null,

    title_add:  'Registrar conductor',
    title_edit: 'Editar conductor',

    $modal: null,
    $title: null, // Modal: Titulo
    $form: null, // Modal: Formulario

    $id: null, // IS del registro, si: accion = EDIT
    $name: null,
    $surname: null,
    $email: null,
    $password: null,
    $phone: null,
    $date_birth: null,
    $date_licence: null,
    $state: null,

    $remove: null,

    init: function(callback){
        this.callback = (typeof callback === 'function') ? callback : null;

        this.$modal = $('#modal_add_driver');
        this.$title = $('.modal-title', this.$modal);
        this.$form  = $('form', this.$modal);

        this.$id            = $('input[name="id"]', this.$modal);
        this.$name          = $('input[name="name"]', this.$modal);
        this.$surname       = $('input[name="surname"]', this.$modal);
        this.$email         = $('input[name="email"]', this.$modal);
        this.$password      = $('input[name="password"]', this.$modal);
        this.$phone         = $('input[name="phone"]', this.$modal);
        this.$date_birth    = $('input[name="date_birth"]', this.$modal);
        this.$date_licence  = $('input[name="date_licence"]', this.$modal);
        this.$state         = $('select[name="state"]', this.$modal);

        this.$remove        = $('.remove', this.$modal);

        // Asignar eventos
        this.$remove.click(function(){
            MDriver.remove(MDriver.$id.val());
        });
        $('.save', this.$modal).click(this.save);

    },

    // Guardar
    save: function(){
        api('ajax/drivers.php', MDriver.$form.serializeObject(), function(rsp){
            if(rsp.ok == true){
                toastr.success('Guardado correctamente');
                MDriver.$modal.modal('hide');

                if(MDriver.callback == null){
                    location.reload();
                } else {
                    MDriver.callback(rsp.id, false);
                }

            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Registrando...');
    },
    
    // Abrir para nuevo NUEVO
    add: function(){
        MDriver.clear();
        MDriver.$modal.modal('show');
    },
    
    // Editar
    edit: function(c){
        MDriver.$title.text(MDriver.title_edit);

        MDriver.$id.val(c.id);
        MDriver.$name.val(c.name);
        MDriver.$surname.val(c.surname);
        MDriver.$email.val(c.email);
        MDriver.$phone.val(c.phone);
        MDriver.$date_birth.val(c.date_birth);
        MDriver.$date_licence.val(c.date_licence);
        MDriver.$state.val(c.state);
        MDriver.$remove.show();

        MDriver.$modal.modal('show');
    },

    // Limpiar campos
    clear: function(){
        MDriver.$title.text(MDriver.title_add);
        MDriver.$id.val('');
        MDriver.$name.val('');
        MDriver.$surname.val('');
        MDriver.$password.val('');
        MDriver.$email.val('');
        MDriver.$phone.val('');
        MDriver.$date_birth.val('');
        MDriver.$date_licence.val('');
        MDriver.$remove.hide();
    },

    // Eliminar
    remove: function(id){
        bootbox.confirm('¿Realmente desea eliminar?', function(result){
            if(result){
                api('ajax/drivers.php', {action:'remove', id:id}, function(rsp){
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