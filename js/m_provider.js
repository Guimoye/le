// Unidades de medida
var MProvider = {

    tit_add: 'Agregar proveedor',
    tit_edit: 'Editar proveedor',

    $modal: null,
    $form: null,

    init: function(){
        this.$modal = $('#modal_add_provider');
        this.$modal.title = $('.modal-title', this.$modal);
        this.$modal.remove = $('.remove', this.$modal);
        this.$form          = $('form', this.$modal);
        this.$form.id       = $('input[name=id]', this.$form);
        this.$form.name     = $('input[name=name]', this.$form);
        this.$form.ruc      = $('input[name=ruc]', this.$form);
        this.$form.email    = $('input[name=email]', this.$form);
        this.$form.phone    = $('input[name=phone]', this.$form);
        this.$form.address  = $('input[name=address]', this.$form);

        // Actions
        this.$modal.remove.click(this.remove);
        $('.save', this.$modal).click(this.save);
        this.$form.submit(function(e){
            e.preventDefault();
            MProvider.save();
        });
        this.$modal.on('shown.bs.modal', function() {
            MProvider.$form.name.focus();
        })
    },

    add: function(){
        MProvider.$form.id.val('');
        MProvider.$form.name.val('');
        MProvider.$form.ruc.val('');
        MProvider.$form.email.val('');
        MProvider.$form.phone.val('');
        MProvider.$form.address.val('');
        MProvider.$modal.title.html(MProvider.tit_add);
        MProvider.$modal.remove.hide();
        MProvider.$modal.modal('show');
    },

    edit: function(o){
        MProvider.$form.id.val(o.id);
        MProvider.$form.name.val(o.name);
        MProvider.$form.ruc.val(o.ruc);
        MProvider.$form.email.val(o.email);
        MProvider.$form.phone.val(o.phone);
        MProvider.$form.address.val(o.address);
        MProvider.$modal.title.html(MProvider.tit_edit);
        MProvider.$modal.remove.show();
        MProvider.$modal.modal('show');
    },

    save: function(){
        api('ajax/providers.php', MProvider.$form.serializeObject(), function(rsp){
            if(rsp.ok){
                toastr.success('Guardado correctamente');
                MProvider.$modal.modal('hide');
                location.reload();
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Guardando...');
    },

    remove: function(){
        bootbox.confirm('¿Seguro que quieres eliminarlo?', function(result){
            if(!result) return;
            api('ajax/providers.php', {action:'remove', id:MProvider.$form.id.val()}, function(rsp){
                if(rsp.ok){
                    toastr.success('Eliminado correctamente...');
                    MProvider.$modal.modal('hide');
                    location.reload();
                } else {
                    bootbox.alert(rsp.msg);
                }
            }, 'Eliminando...');
        });
    }

};