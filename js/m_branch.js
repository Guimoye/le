// Unidades de medida
var MBranch = {

    tit_add: 'Agregar sucursal',
    tit_edit: 'Editar sucursal',

    $modal: null,
    $form: null,

    init: function(){
        this.$modal = $('#modal_add_branch');
        this.$modal.title = $('.modal-title', this.$modal);
        this.$modal.remove = $('.remove', this.$modal);
        this.$form          = $('form', this.$modal);
        this.$form.id       = $('input[name=id]', this.$form);
        this.$form.name     = $('input[name=name]', this.$form);
        this.$form.email    = $('input[name=email]', this.$form);
        this.$form.phone    = $('input[name=phone]', this.$form);
        this.$form.address  = $('input[name=address]', this.$form);

        // Actions
        this.$modal.remove.click(this.remove);
        $('.save', this.$modal).click(this.save);
        this.$form.submit(function(e){
            e.preventDefault();
            MBranch.save();
        });
        this.$modal.on('shown.bs.modal', function() {
            MBranch.$form.name.focus();
        })
    },

    add: function(){
        MBranch.$form.id.val('');
        MBranch.$form.name.val('');
        MBranch.$form.email.val('');
        MBranch.$form.phone.val('');
        MBranch.$form.address.val('');
        MBranch.$modal.title.html(MBranch.tit_add);
        MBranch.$modal.remove.hide();
        MBranch.$modal.modal('show');
    },

    edit: function(o){
        MBranch.$form.id.val(o.id);
        MBranch.$form.name.val(o.name);
        MBranch.$form.email.val(o.email);
        MBranch.$form.phone.val(o.phone);
        MBranch.$form.address.val(o.address);
        MBranch.$modal.title.html(MBranch.tit_edit);
        MBranch.$modal.remove.show();
        MBranch.$modal.modal('show');
    },

    save: function(){
        api('ajax/branches.php', MBranch.$form.serializeObject(), function(rsp){
            if(rsp.ok){
                toastr.success('Guardado correctamente');
                MBranch.$modal.modal('hide');
                location.reload();
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Guardando...');
    },

    remove: function(){
        bootbox.confirm('¿Seguro que quieres eliminarlo?', function(result){
            if(!result) return;
            api('ajax/branches.php', {action:'remove', id:MBranch.$form.id.val()}, function(rsp){
                if(rsp.ok){
                    toastr.success('Eliminado correctamente...');
                    MBranch.$modal.modal('hide');
                    location.reload();
                } else {
                    bootbox.alert(rsp.msg);
                }
            }, 'Eliminando...');
        });
    }

};