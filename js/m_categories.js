// Categorias
var MCateg = {

    tit_add: 'Agregar categoría',
    tit_edit: 'Editar categoría',

    $modal: null,
    $form: null,

    $list: null,

    init: function(){
        this.$modal = $('#modal_add_category');
        this.$modal.title = $('.modal-title', this.$modal);
        this.$modal.remove = $('.remove', this.$modal);
        this.$form          = $('form', this.$modal);
        this.$form.id       = $('input[name=id]', this.$form);
        this.$form.name     = $('input[name=name]', this.$form);
        this.$form.color    = $('input[name=color]', this.$form);
        this.$form.sort     = $('input[name=sort]', this.$form);
        this.$form.in_deli  = $('input[name=in_deli]', this.$form);
        this.$form.favorite = $('input[name=favorite]', this.$form);

        this.$list = $('#list').nestable();
        this.$form.color.minicolors();

        // Actions
        this.$modal.remove.click(this.remove);
        $('.save', this.$modal).click(this.save);
    },

    add: function(){
        //MCateg.edit(0,'','','');
        MCateg.$modal.title.html(MCateg.tit_add);
        MCateg.$modal.remove.hide();
        MCateg.$form.id.val('');
        MCateg.$form.name.val('');
        MCateg.$form.color.minicolors('value','#67809F');
        MCateg.$form.sort.val('0');
        MCateg.$form.in_deli.prop('checked', true);
        MCateg.$form.favorite.prop('checked', true);
        MCateg.$modal.modal('show');
        $.uniform.update()
    },

    edit: function(id,name,color,sort,in_deli,favorite){
        MCateg.$modal.title.html(MCateg.tit_edit);
        MCateg.$modal.remove.show();
        MCateg.$form.id.val(id);
        MCateg.$form.name.val(name);
        MCateg.$form.color.minicolors('value',color);
        MCateg.$form.sort.val(sort);
        MCateg.$form.in_deli.prop('checked', (in_deli==1));
        MCateg.$form.favorite.prop('checked', (favorite==1));
        MCateg.$modal.modal('show');
        $.uniform.update()
    },

    save: function(){
        api('ajax/categories.php', MCateg.$form.serializeObject(), function(rsp){
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
            api('ajax/categories.php', {action:'remove', id:MCateg.$form.id.val()}, function(rsp){
                if(rsp.ok){
                    toastr.success('Eliminado correctamente...');
                    MCateg.$modal.modal('hide');
                    location.reload();
                } else {
                    bootbox.alert(rsp.msg);
                }
            }, 'Eliminando...');
        });
    },

    reOrder: function(){
        var list = MCateg.$list.nestable('serialize');
        console.log(list);

        api('ajax/categories.php', {action:'re_sort', list:list}, function(rsp){
            if(rsp.ok){
                toastr.success('Guardado correctamente');
                location.reload();
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Guardando menu...');
    }

};