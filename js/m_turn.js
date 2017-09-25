// Unidades de medida
var MTurn = {

    tit_add: 'Agregar turno',
    tit_edit: 'Editar turno',

    $modal: null,
    $form: null,

    init: function(){
        this.$modal = $('#modal_add_turn');
        this.$modal.title = $('.modal-title', this.$modal);
        this.$modal.remove = $('.remove', this.$modal);
        this.$form          = $('form', this.$modal);
        this.$form.id       = $('input[name=id]', this.$form);
        this.$form.name     = $('input[name=name]', this.$form);

        // Actions
        this.$modal.remove.click(this.remove);
        $('.save', this.$modal).click(this.save);
        this.$form.submit(function(e){
            e.preventDefault();
            MTurn.save();
        });
        this.$modal.on('shown.bs.modal', function() {
            MTurn.$form.name.focus();
        })
    },

    add: function(){
        MTurn.$form.id.val('');
        MTurn.$form.name.val('');
        MTurn.$modal.title.html(MTurn.tit_add);
        MTurn.$modal.remove.hide();
        MTurn.$modal.modal('show');
    },

    edit: function(o){
        MTurn.$form.id.val(o.id);
        MTurn.$form.name.val(o.name);
        MTurn.$modal.title.html(MTurn.tit_edit);
        MTurn.$modal.remove.show();
        MTurn.$modal.modal('show');
    },

    save: function(){
        api('ajax/turns.php', MTurn.$form.serializeObject(), function(rsp){
            if(rsp.ok){
                toastr.success('Guardado correctamente');
                MTurn.$modal.modal('hide');
                location.reload();
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Guardando...');
    },

    remove: function(){
        bootbox.confirm('¿Eliminar unidad de medida?', function(result){
            if(!result) return;
            api('ajax/turns.php', {action:'remove', id:MTurn.$form.id.val()}, function(rsp){
                if(rsp.ok){
                    toastr.success('Eliminado correctamente...');
                    MTurn.$modal.modal('hide');
                    location.reload();
                } else {
                    bootbox.alert(rsp.msg);
                }
            }, 'Eliminando...');
        });
    }

};