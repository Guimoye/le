var MPromo = {

    tit_add: 'Agregar oferta',
    tit_edit: 'Editar oferta',

    $modal: null,
    $form: null,

    init: function(){
        this.$modal = $('#modal_add_promo');
        this.$modal.title = $('.modal-title', this.$modal);
        this.$modal.remove = $('.remove', this.$modal);
        this.$form              = $('form', this.$modal);
        this.$form.id           = $('input[name=id]', this.$form);
        this.$form.code         = $('input[name=code]', this.$form);
        this.$form.percent      = $('input[name=percent]', this.$form);
        this.$form.max_value    = $('input[name=max_value]', this.$form);
        this.$form.name         = $('input[name=name]', this.$form);
        this.$form.date         = $('input[name=date]', this.$form);
        this.$form.time         = $('input[name=time]', this.$form);

        // Actions
        this.$modal.remove.click(this.remove);
        $('.save', this.$modal).click(this.save);
        this.$form.submit(function(e){
            e.preventDefault();
            MPromo.save();
        });
        this.$modal.on('shown.bs.modal', function() {
            MPromo.$form.code.focus();
        })
    },

    add: function(){
        MPromo.$form.id.val('');
        MPromo.$form.code.val('');
        MPromo.$form.percent.val('');
        MPromo.$form.max_value.val('');
        MPromo.$form.name.val('');
        MPromo.$modal.title.html(MPromo.tit_add);
        MPromo.$modal.remove.hide();
        MPromo.$modal.modal('show');
    },

    edit: function(o){
        MPromo.$form.id.val(o.id);
        MPromo.$form.code.val(o.code);
        MPromo.$form.percent.val(o.percent);
        MPromo.$form.max_value.val(o.max_value);
        MPromo.$form.name.val(o.name);
        if(o.date_end != null){
            var arr = o.date_end.split(' ');
            MPromo.$form.date.val(arr[0]);
            MPromo.$form.time.val(arr[1]);
        } else {
            MPromo.$form.date.val('');
            MPromo.$form.time.val('');
        }
        MPromo.$modal.title.html(MPromo.tit_edit);
        MPromo.$modal.remove.show();
        MPromo.$modal.modal('show');
    },

    save: function(){
        api('ajax/promos.php', MPromo.$form.serializeObject(), function(rsp){
            if(rsp.ok){
                toastr.success('Guardado correctamente');
                MPromo.$modal.modal('hide');
                location.reload();
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Guardando...');
    },

    remove: function(){
        bootbox.confirm('¿Eliminar unidad de medida?', function(result){
            if(!result) return;
            api('ajax/promos.php', {action:'remove', id:MPromo.$form.id.val()}, function(rsp){
                if(rsp.ok){
                    toastr.success('Eliminado correctamente...');
                    MPromo.$modal.modal('hide');
                    location.reload();
                } else {
                    bootbox.alert(rsp.msg);
                }
            }, 'Eliminando...');
        });
    }

};