// Unidades de medida
var MArea = {

    tit_add: 'Agregar area de producción',
    tit_edit: 'Editar area de producción',

    $modal: null,
    $form: null,

    init: function(){
        this.$modal = $('#modal_add_area');
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
            MArea.save();
        });
        this.$modal.on('shown.bs.modal', function() {
            MArea.$form.name.focus();
        });
    },

    add: function(){
        MArea.$form.id.val('');
        MArea.$form.name.val('');
        MArea.$modal.title.html(MArea.tit_add);
        MArea.$modal.remove.hide();
        MArea.$modal.modal('show');
    },

    edit: function(o){
        MArea.$form.id.val(o.id);
        MArea.$form.name.val(o.name);
        MArea.$modal.title.html(MArea.tit_edit);
        MArea.$modal.remove.show();
        MArea.$modal.modal('show');
    },

    save: function(){
        api('ajax/areas.php', MArea.$form.serializeObject(), function(rsp){
            if(rsp.ok){
                toastr.success('Guardado correctamente');
                MArea.$modal.modal('hide');
                location.reload();
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Guardando...');
    },

    remove: function(){
        bootbox.confirm('¿Seguro que quieres eliminarlo?', function(result){
            if(!result) return;
            api('ajax/areas.php', {action:'remove', id:MArea.$form.id.val()}, function(rsp){
                if(rsp.ok){
                    toastr.success('Eliminado correctamente...');
                    MArea.$modal.modal('hide');
                    location.reload();
                } else {
                    bootbox.alert(rsp.msg);
                }
            }, 'Eliminando...');
        });
    }

};