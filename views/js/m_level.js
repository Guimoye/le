// Levels
var MLevel = {

    tit_add: 'Agregar perfil',
    tit_edit: 'Editar perfil',

    $modal: null,
    $form: null,
    $levels: null,

    init: function(){
        
        this.$modal = $('#modal_add_level');
        this.$modal.title = $('.modal-title', this.$modal);
        this.$form = $('form', this.$modal);
        this.$form.id = $('input[name=id]', this.$form);
        this.$form.name = $('input[name=name]', this.$form);
        this.$form.home = $('input[name=home]', this.$form);
        this.$levels = $('.levels', this.$modal);

        $('.save', this.$modal).click(this.save);
        
        $('thead input', this.$modal).change(function(){
            console.log('input changed thead all');
            if($(this).hasClass('see')){
                $('.see', MLevel.$levels).prop('checked', this.checked);
            } else {
                $('.edit', MLevel.$levels).prop('checked', this.checked);
            }
            $.uniform.update();
        });

        $('.menu input', this.$levels).change(function(state){
            console.log('input changed menu');
            var $this = $(this),
                id = $this.data('id');
            if($this.hasClass('see')){
                $('.submenu.id_parent_'+id+' .see', MLevel.$levels).prop('checked', this.checked);
            } else {
                $('.submenu.id_parent_'+id+' .edit', MLevel.$levels).prop('checked', this.checked);
            }
            $.uniform.update();
        });
    },

    add: function(){
        MLevel.$modal.title.html(MLevel.tit_add);
        MLevel.$form.id.val('');
        MLevel.$form.name.val('');
        $('input', MLevel.$levels).prop('checked', false);
        $.uniform.update();
        MLevel.$modal.modal('show');
    },
    
    edit: function(o){
        $('input', MLevel.$levels).prop('checked', false);

        MLevel.$modal.title.html(MLevel.tit_edit);
        MLevel.$form.id.val(o.id);
        MLevel.$form.name.val(o.name);
        $('input.home.id_'+o.id_menu, MLevel.$levels).prop('checked', true);
        o.perms.forEach(function(i){
            if(i.see == 1){
                $('input.see.id_'+i.id_menu, MLevel.$levels).prop('checked', true);
            }
            if(i.edit == 1){
                $('input.edit.id_'+i.id_menu, MLevel.$levels).prop('checked', true);
            }
            if(i.shortcut == 1){
                $('input.shortcut.id_'+i.id_menu, MLevel.$levels).prop('checked', true);
            }
        });
        $.uniform.update();
        MLevel.$modal.modal('show');
    },
    
    save: function(){
        var data = MLevel.$form.serializeObject();
        console.log(data);
        api('menu/add_level', data, function(rsp){
            if(rsp.ok){
                toastr.success('Guardado correctamente');
                MLevel.$modal.modal('hide');
                location.reload();
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Guardando...');
    }

};