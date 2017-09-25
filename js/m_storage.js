// Unidades de medida
var MStorage = {

    tit_add: 'Agregar almacén',
    tit_edit: 'Editar almacén',

    $modal: null,
    $form: null,

    init: function(){
        this.$modal = $('#modal_add_branch');
        this.$modal.title = $('.modal-title', this.$modal);
        this.$modal.remove = $('.remove', this.$modal);
        this.$form          = $('form', this.$modal);
        this.$form.id       = $('input[name=id]', this.$form);
        this.$form.name     = $('input[name=name]', this.$form);
        this.$form.id_area  = $('select[name=id_area]', this.$form);

        // Actions
        this.$modal.remove.click(this.remove);
        $('.save', this.$modal).click(this.save);
        this.$form.submit(function(e){
            e.preventDefault();
            MStorage.save();
        });
        this.$modal.on('shown.bs.modal', function() {
            MStorage.$form.name.focus();
        })
    },

    add: function(){
        MStorage.$form.id.val('');
        MStorage.$form.name.val('');
        MStorage.$form.id_area.val('');
        MStorage.$modal.title.html(MStorage.tit_add);
        MStorage.$modal.remove.hide();
        MStorage.$modal.modal('show');
    },

    edit: function(o){
        MStorage.$form.id.val(o.id);
        MStorage.$form.name.val(o.name);
        MStorage.$form.id_area.val(o.id_area);
        MStorage.$modal.title.html(MStorage.tit_edit);
        MStorage.$modal.remove.show();
        MStorage.$modal.modal('show');
    },

    save: function(){
        api('ajax/storages.php', MStorage.$form.serializeObject(), function(rsp){
            if(rsp.ok){
                toastr.success('Guardado correctamente');
                MStorage.$modal.modal('hide');
                location.reload();
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Guardando...');
    },

    remove: function(){
        bootbox.confirm('¿Eliminar unidad de medida?', function(result){
            if(!result) return;
            api('ajax/storages.php', {action:'remove', id:MStorage.$form.id.val()}, function(rsp){
                if(rsp.ok){
                    toastr.success('Eliminado correctamente...');
                    MStorage.$modal.modal('hide');
                    location.reload();
                } else {
                    bootbox.alert(rsp.msg);
                }
            }, 'Eliminando...');
        });
    }

};