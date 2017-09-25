
// Menu
var MMenu = {

    tit_add: 'Agregar item',
    tit_edit: 'Editar item',

    $modal: null,
    $form: null,

    $list: null,

    init: function(){
        this.$modal = $('#modal_add_menu');
        this.$modal.title = $('.modal-title', this.$modal);
        this.$modal.remove = $('.remove', this.$modal);
        this.$form      = $('form', this.$modal);
        this.$form.id   = $('input[name=id]', this.$form);
        this.$form.name = $('input[name=name]', this.$form);
        this.$form.url  = $('input[name=url]', this.$form);
        this.$form.icon = $('input[name=icon]', this.$form);

        this.$list = $('#list').nestable();

        // Actions
        this.$modal.remove.click(this.remove);
        $('.save', this.$modal).click(this.save);
    },

    add: function(){
        //MMenu.edit(0,'','','');
        MMenu.$modal.title.html(MMenu.tit_add);
        MMenu.$modal.remove.hide();
        MMenu.$form.id.val('');
        MMenu.$form.name.val('');
        MMenu.$form.url.val('');
        MMenu.$form.url.prop('disabled', false);
        MMenu.$form.icon.val('');
        MMenu.$modal.modal('show');
    },

    edit: function(id,name,url,icon,root){
        MMenu.$modal.title.html(MMenu.tit_edit);
        if(root==1){
            MMenu.$modal.remove.hide();
            MMenu.$form.url.prop('disabled', true);
        } else {
            MMenu.$modal.remove.show();
            MMenu.$form.url.prop('disabled', false);
        }
        MMenu.$form.id.val(id);
        MMenu.$form.name.val(name);
        MMenu.$form.url.val(url);
        MMenu.$form.icon.val(icon);
        MMenu.$modal.modal('show');
    },

    save: function(){
        api('ajax/menu.php', MMenu.$form.serializeObject(), function(rsp){
            if(rsp.ok){
                toastr.success('Guardado correctamente');
                location.reload();
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Guardando...');
    },

    remove: function(){
        bootbox.confirm('¿Eliminar item?', function(result){
            if(!result) return;
            api('ajax/menu.php', {action:'remove_menu', id:MMenu.$form.id.val()}, function(rsp){
                if(rsp.ok){
                    toastr.success('Eliminado correctamente...');
                    MMenu.$modal.modal('hide');
                    location.reload();
                } else {
                    bootbox.alert(rsp.msg);
                }
            }, 'Eliminando...');
        });
    },

    reOrder: function(){
        var list = MMenu.$list.nestable('serialize');
        console.log(list);

        api('ajax/menu.php', {action:'re_sort', list:list}, function(rsp){
            if(rsp.ok){
                toastr.success('Guardado correctamente');
                location.reload();
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Guardando menu...');
    }

};