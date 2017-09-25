// Modal
var MUser = {

    callback: null,

    title_add:  'Registrar usuario',
    title_edit: 'Editar usuario',

    $modal: null,
    $title: null, // Modal: Titulo
    $form: null, // Modal: Formulario

    $remove: null,

    init: function(callback){
        this.callback = (typeof callback === 'function') ? callback : null;

        $body.append(
            '<div id="modal_add_client" class="modal fade modal-scroll" data-backdrop="static" data-keyboard="false">'+
            ' <div class="modal-dialog" style="max-width:400px">'+
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
            '      <label class="col-md-4 control-label">Nombre</label>'+
            '      <div class="col-md-8">'+
            '       <input type="text" class="form-control" name="name" placeholder="Escribir...">'+
            '      </div>'+
            '     </div>'+

            '     <div class="form-group">'+
            '      <label class="col-md-4 control-label">Apellido</label>'+
            '      <div class="col-md-8">'+
            '       <input type="text" class="form-control" name="surname" placeholder="Escribir...">'+
            '      </div>'+
            '     </div>'+

            '     <div class="form-group">'+
            '      <label class="col-md-4 control-label">Usuario</label>'+
            '      <div class="col-md-8">'+
            '       <input type="text" class="form-control" name="username" placeholder="Escribir...">'+
            '      </div>'+
            '     </div>'+

            '     <div class="form-group">'+
            '      <label class="col-md-4 control-label">Contraseña</label>'+
            '      <div class="col-md-8">'+
            '       <input type="password" class="form-control" name="password" placeholder="Escribir...">'+
            '      </div>'+
            '     </div>'+

            '     <div class="form-group">'+
            '      <label class="col-md-4 control-label">Teléfono</label>'+
            '      <div class="col-md-8">'+
            '       <input type="text" class="form-control" name="phone" placeholder="Escribir...">'+
            '      </div>'+
            '     </div>'+

            '     <div class="form-group">'+
            '      <label class="col-md-4 control-label">Perfil</label>'+
            '      <div class="col-md-8">'+
            '       <select class="form-control" name="id_level"></select>'+
            '      </div>'+
            '     </div>'+

            '     <div class="form-group">'+
            '      <label class="col-md-4 control-label">Estado</label>'+
            '      <div class="col-md-6">'+
            '       <select class="form-control" name="state">'+
            '       <option value="1">Habilitado</option>'+
            '       <option value="2">Bloqueado</option>'+
            '       </select>'+
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

        this.$modal = $('#modal_add_client');
        this.$title = $('.modal-title', this.$modal);

        this.$form           = $('form', this.$modal);
        this.$form.id        = $('input[name="id"]', this.$modal);
        this.$form.name      = $('input[name="name"]', this.$form);
        this.$form.surname   = $('input[name="surname"]', this.$form);
        this.$form.username  = $('input[name="username"]', this.$form);
        this.$form.password  = $('input[name="password"]', this.$form);
        this.$form.phone     = $('input[name="phone"]', this.$form);
        this.$form.level     = $('select[name="id_level"]', this.$form);
        this.$form.state     = $('select[name="state"]', this.$form);

        this.$remove    = $('.remove', this.$modal);

        // Asignar eventos
        this.$remove.click(function(){
            MUser.remove(MUser.$form.id.val());
        });
        $('.save', this.$modal).click(this.save);

    },

    getLevels: function(id_level){
        MUser.$form.level.html('<option>Cargando...</option>');
        api('get_levels', function(rsp){
            var html = '<option value="">Elegir...</option>';
            rsp.levels.forEach(function(o){
                html += '<option value="'+o.id+'" '+(o.id==id_level?'selected':'')+'>'+o.name+'</option>';
            });
            MUser.$form.level.html(html);
        }, false, true);
    },

    // Guardar
    save: function(){
        api('ajax/users.php', MUser.$form.serializeObject(), function(rsp){
            if(rsp.ok == true){
                toastr.success('Guardado correctamente');
                MUser.$modal.modal('hide');

                if(MUser.callback == null){
                    location.reload();
                } else {
                    MUser.callback(rsp.id, false);
                }

            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Registrando...');
    },
    
    // Abrir para nuevo NUEVO
    add: function(){
        MUser.clear();
        MUser.getLevels(0);
        MUser.$modal.modal('show');
    },
    
    // Editar
    edit: function(c){
        MUser.$title.text(MUser.title_edit);

        MUser.$form.id.val(c.id);
        MUser.$form.name.val(c.name);
        MUser.$form.surname.val(c.surname);
        MUser.$form.username.val(c.username);
        MUser.$form.phone.val(c.phone);
        MUser.$form.state.val(c.state);
        MUser.$remove.show();
        MUser.getLevels(c.id_level);

        MUser.$modal.modal('show');
    },

    // Limpiar campos
    clear: function(){
        MUser.$title.text(MUser.title_add);
        MUser.$form.id.val('');
        MUser.$form.name.val('');
        MUser.$form.surname.val('');
        MUser.$form.username.val('');
        MUser.$form.phone.val('');
        MUser.$remove.hide();
    },

    // Eliminar
    remove: function(id){
        bootbox.confirm('¿Realmente desea eliminar?', function(result){
            if(result){
                api('ajax/users.php', {action:'remove', id:id}, function(rsp){
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