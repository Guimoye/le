// Unidades de medida
var MUnimed = {

    tit_add: 'Agregar unidad de medida',
    tit_edit: 'Editar unidad de medida',

    $modal: null,
    $form: null,

    init: function(){
        this.$modal = $('#modal_add_unimed');
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
            MUnimed.save();
        });
        this.$modal.on('shown.bs.modal', function() {
            MUnimed.$form.name.focus();
        })
    },

    add: function(){
        MUnimed.$form.id.val('');
        MUnimed.$form.name.val('');
        MUnimed.$modal.title.html(MUnimed.tit_add);
        MUnimed.$modal.remove.hide();
        MUnimed.$modal.modal('show');
    },

    edit: function(o){
        MUnimed.$form.id.val(o.id);
        MUnimed.$form.name.val(o.name);
        MUnimed.$modal.title.html(MUnimed.tit_edit);
        MUnimed.$modal.remove.show();
        MUnimed.$modal.modal('show');
    },

    save: function(){
        api('ajax/unimeds.php', MUnimed.$form.serializeObject(), function(rsp){
            if(rsp.ok){
                toastr.success('Guardado correctamente');
                MUnimed.$modal.modal('hide');
                if(MUnimed.$form.id.val() == ''){
                    bootbox.confirm({
                        message: 'Guardado correctamente.',
                        buttons: {
                            cancel: { label: 'Listo' },
                            confirm: { label: 'Agregar otro' }
                        },
                        callback: function(result){
                            if(result){
                                MUnimed.add();
                            } else {
                                location.reload();
                            }
                        }
                    });
                } else {
                    location.reload();
                }
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Guardando...');
    },

    remove: function(){
        bootbox.confirm('¿Eliminar unidad de medida?', function(result){
            if(!result) return;
            api('ajax/unimeds.php', {action:'remove', id:MUnimed.$form.id.val()}, function(rsp){
                if(rsp.ok){
                    toastr.success('Eliminado correctamente...');
                    MUnimed.$modal.modal('hide');
                    location.reload();
                } else {
                    bootbox.alert(rsp.msg);
                }
            }, 'Eliminando...');
        });
    }

};

// Relacion de unidades de medida
var MUnimedRel = {

    tit_add: 'Relacionar unidad de medida',
    tit_edit: 'Editar relación de unidad de medida',

    $modal: null,
    $form: null,

    init: function(){
        this.$modal = $('#modal_add_unimed_rel');
        this.$modal.title = $('.modal-title', this.$modal);
        this.$modal.remove = $('.remove', this.$modal);
        this.$form                  = $('form', this.$modal);
        this.$form.id               = $('input[name=id]', this.$form);
        this.$form.quantity         = $('input[name=quantity]', this.$form);
        this.$form.id_unimed_org    = $('select[name=id_unimed_org]', this.$form);
        this.$form.id_unimed_dst    = $('select[name=id_unimed_dst]', this.$form);

        // Actions
        this.$modal.remove.click(this.remove);
        $('.save', this.$modal).click(this.save);
        this.$form.submit(function(e){
            e.preventDefault();
            MUnimedRel.save();
        });
        this.$modal.on('shown.bs.modal', function() {
            MUnimedRel.$form.quantity.focus();
        })
    },

    add: function(){
        MUnimedRel.$form.id.val('');
        MUnimedRel.$form.quantity.val('');
        MUnimedRel.$modal.title.html(MUnimedRel.tit_add);
        MUnimedRel.$modal.remove.hide();
        MUnimedRel.$modal.modal('show');
    },

    edit: function(o){
        MUnimedRel.$form.id.val(o.id);
        MUnimedRel.$form.quantity.val(o.quantity);
        MUnimedRel.$form.id_unimed_org.val(o.id_unimed_org);
        MUnimedRel.$form.id_unimed_dst.val(o.id_unimed_dst);
        MUnimedRel.$modal.title.html(MUnimedRel.tit_edit);
        MUnimedRel.$modal.remove.show();
        MUnimedRel.$modal.modal('show');
    },

    save: function(){
        api('ajax/unimeds.php', MUnimedRel.$form.serializeObject(), function(rsp){
            if(rsp.ok){
                toastr.success('Guardado correctamente');
                MUnimedRel.$modal.modal('hide');
                if(MUnimedRel.$form.id.val() == ''){
                    bootbox.confirm({
                        message: 'Guardado correctamente.',
                        buttons: {
                            cancel: { label: 'Listo' },
                            confirm: { label: 'Agregar otro' }
                        },
                        callback: function(result){
                            if(result){
                                MUnimedRel.add();
                            } else {
                                location.reload();
                            }
                        }
                    });
                } else {
                    location.reload();
                }
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Guardando...');
    },

    remove: function(){
        bootbox.confirm('¿Eliminar unidad de medida?', function(result){
            if(!result) return;
            api('ajax/unimeds.php', {action:'remove_rel', id:MUnimedRel.$form.id.val()}, function(rsp){
                if(rsp.ok){
                    toastr.success('Eliminado correctamente...');
                    MUnimedRel.$modal.modal('hide');
                    location.reload();
                } else {
                    bootbox.alert(rsp.msg);
                }
            }, 'Eliminando...');
        });
    }

};