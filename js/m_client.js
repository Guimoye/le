// Modal
var MClient = {

    callback: null,

    title_add:  'Registrar cliente',
    title_edit: 'Editar cliente',

    $modal: null,
    $form: null, // Modal: Formulario

    $id: null, // ID del registro, si: accion = EDIT
    $name: null,
    $surname: null,
    $email: null,
    $phone: null,
    $state: null,

    init: function(callback){
        this.callback = (typeof callback === 'function') ? callback : null;

        $body.append(
            '<div id="modal_add_client" class="modal fade modal-scroll" data-backdrop="static" data-keyboard="false">'+
            ' <div class="modal-dialog" style="max-width:450px">'+
            '  <div class="modal-content">'+
            '   <div class="modal-header">'+
            '    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>'+
            '    <h4 class="modal-title">---</h4>'+
            '   </div>'+
            '   <div class="modal-body">'+

            '    <form class="form-horizontal">'+
            '     <input type="hidden" name="action" value="add">'+
            '     <input type="hidden" name="id" value="">'+

            '     <div class="form-group">'+
            '      <label class="col-md-3 control-label">Nombres</label>'+
            '      <div class="col-md-9">'+
            '       <input type="text" class="form-control" name="name" placeholder="Nombre o Razón social">'+
            '      </div>'+
            '     </div>'+

            '     <div class="form-group">'+
            '      <label class="col-md-3 control-label">DNI/RUC</label>'+
            '      <div class="col-md-9">'+
            '       <input type="text" class="form-control" name="dni" placeholder="Número de documento">'+
            '      </div>'+
            '     </div>'+

            '     <div class="form-group">'+
            '      <label class="col-md-3 control-label">Dirección</label>'+
            '      <div class="col-md-9">'+
            '       <input type="text" class="form-control" name="address" placeholder="Dirección">'+
            '      </div>'+
            '     </div>'+

            '     <div class="form-group">'+
            '      <label class="col-md-3 control-label">Teléfono</label>'+
            '      <div class="col-md-9">'+
            '       <input type="text" class="form-control" name="phone" placeholder="Teléfono">'+
            '      </div>'+
            '     </div>'+

            '     <div class="form-group">'+
            '      <label class="col-md-3 control-label">Email</label>'+
            '      <div class="col-md-9">'+
            '       <input type="email" class="form-control" name="email" placeholder="Correo electrónico">'+
            '      </div>'+
            '     </div>'+

            '    </form>'+

            '    <table class="table table-bordered table-striped" style="display:none"></table>'+

            '   </div>'+
            '   <div class="modal-footer">'+
            '    <button type="button" class="btn red pull-left remove">Eliminar</button>'+
            '    <button type="button" data-dismiss="modal" class="btn cancel">Cancelar</button>'+
            '    <button type="button" class="btn blue save">Guardar</button>'+
            '   </div>'+
            '  </div>'+
            ' </div>'+
            '</div>'
        );

        this.$modal         = $('#modal_add_client');
        this.$modal.title   = $('.modal-title', this.$modal);
        this.$modal.remove  = $('.remove', this.$modal);
        this.$form          = $('form', this.$modal);
        this.$form.id       = $('input[name="id"]', this.$modal);
        this.$form.name     = $('input[name="name"]', this.$modal);
        this.$form.dni      = $('input[name="dni"]', this.$modal);
        this.$form.address  = $('input[name="address"]', this.$modal);
        this.$form.phone    = $('input[name="phone"]', this.$modal);
        this.$form.email    = $('input[name="email"]', this.$modal);

        // Asignar eventos
        this.$modal.remove.click(function(){
            MClient.remove(MClient.$form.id.val());
        });
        $('.save', this.$modal).click(this.save);

    },

    // Guardar
    save: function(){
        api('ajax/clients.php', MClient.$form.serializeObject(), function(rsp){
            if(rsp.ok == true){
                toastr.success('Guardado correctamente');
                MClient.$modal.modal('hide');

                if(MClient.callback == null){
                    location.reload();
                } else {
                    MClient.callback(rsp.client, false);
                }

            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Registrando...');
    },
    
    // Abrir para nuevo NUEVO
    add: function(){
        MClient.$modal.title.text(MClient.title_add);
        MClient.$modal.remove.hide();

        MClient.$form.id.val('');
        MClient.$form.name.val('');
        MClient.$form.dni.val('');
        MClient.$form.address.val('');
        MClient.$form.phone.val('');
        MClient.$form.email.val('');

        MClient.$modal.modal('show');
    },
    
    // Editar
    edit: function(c){
        MClient.$modal.title.text(MClient.title_edit);
        MClient.$modal.remove.show();

        MClient.$form.id.val(c.id);
        MClient.$form.name.val(c.name);
        MClient.$form.dni.val(c.dni);
        MClient.$form.address.val(c.address);
        MClient.$form.phone.val(c.phone);
        MClient.$form.email.val(c.email);

        MClient.$modal.modal('show');
    },

    // Eliminar
    remove: function(id){
        bootbox.confirm('¿Realmente desea eliminar?', function(result){
            if(result){
                api('ajax/clients.php', {action:'remove', id:id}, function(rsp){
                    if(rsp.ok){
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