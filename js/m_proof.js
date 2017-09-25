// Unidades de medida
var MProof = {

    tit_add: 'Agregar comprobante',
    tit_edit: 'Editar comprobante',

    $modal: null,
    $form: null,

    init: function(){
        this.$modal = $('#modal_add_proof');
        this.$modal.title = $('.modal-title', this.$modal);
        this.$modal.remove = $('.remove', this.$modal);
        this.$form          = $('form', this.$modal);
        this.$form.id       = $('input[name=id]', this.$form);
        this.$form.code     = $('input[name=code]', this.$form);
        this.$form.name     = $('input[name=name]', this.$form);

        // Actions
        this.$modal.remove.click(this.remove);
        $('.save', this.$modal).click(this.save);
        this.$form.submit(function(e){
            e.preventDefault();
            MProof.save();
        });
        this.$modal.on('shown.bs.modal', function() {
            MProof.$form.name.focus();
        })
    },

    add: function(){
        MProof.$form.id.val('');
        MProof.$form.code.val('');
        MProof.$form.name.val('');
        MProof.$modal.title.html(MProof.tit_add);
        MProof.$modal.remove.hide();
        MProof.$modal.modal('show');
    },

    edit: function(o){
        MProof.$form.id.val(o.id);
        MProof.$form.code.val(o.code);
        MProof.$form.name.val(o.name);
        MProof.$modal.title.html(MProof.tit_edit);
        MProof.$modal.remove.show();
        MProof.$modal.modal('show');
    },

    save: function(){
        api('ajax/proofs.php', MProof.$form.serializeObject(), function(rsp){
            if(rsp.ok){
                toastr.success('Guardado correctamente');
                MProof.$modal.modal('hide');
                location.reload();
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Guardando...');
    },

    remove: function(){
        bootbox.confirm('¿Eliminar unidad de medida?', function(result){
            if(!result) return;
            api('ajax/proofs.php', {action:'remove', id:MProof.$form.id.val()}, function(rsp){
                if(rsp.ok){
                    toastr.success('Eliminado correctamente...');
                    MProof.$modal.modal('hide');
                    location.reload();
                } else {
                    bootbox.alert(rsp.msg);
                }
            }, 'Eliminando...');
        });
    }

};