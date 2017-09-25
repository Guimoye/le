// Unidades de medida
var MExpense = {

    tit_add: 'Registrar egreso',
    tit_edit: 'Editar egreso',

    $modal: null,
    $form: null,

    init: function(){
        this.$modal = $('#modal_add_expense');
        this.$modal.title = $('.modal-title', this.$modal);
        this.$modal.remove = $('.remove', this.$modal);
        this.$form          = $('form', this.$modal);
        this.$form.id       = $('input[name=id]', this.$form);
        this.$form.notes    = $('input[name=notes]', this.$form);
        this.$form.total    = $('input[name=total]', this.$form);
        this.$form.coin     = $('input[name=coin]', this.$form);

        // Actions
        this.$modal.remove.click(this.remove);
        $('.save', this.$modal).click(this.save);
        this.$form.submit(function(e){
            e.preventDefault();
            MExpense.save();
        });
        this.$modal.on('shown.bs.modal', function() {
            MExpense.$form.name.focus();
        })
    },

    add: function(){
        MExpense.$form.id.val('');
        MExpense.$form.notes.val('');
        MExpense.$form.total.val('');
        MExpense.$form.coin.val(0);
        MExpense.$modal.title.html(MExpense.tit_add);
        MExpense.$modal.remove.hide();
        MExpense.$modal.modal('show');
    },

    edit: function(o){
        MExpense.$form.id.val(o.id);
        MExpense.$form.notes.val(o.notes);
        MExpense.$form.total.val(o.total);
        MExpense.$form.coin.val(o.coin);
        MExpense.$modal.title.html(MExpense.tit_edit);
        MExpense.$modal.remove.show();
        MExpense.$modal.modal('show');
    },

    save: function(){
        api('ajax/box.php', MExpense.$form.serializeObject(), function(rsp){
            if(rsp.ok){
                toastr.success('Guardado correctamente');
                MExpense.$modal.modal('hide');
                location.reload();
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Guardando...');
    },

    remove: function(){
        bootbox.confirm('¿Seguro que quieres eliminarlo?', function(result){
            if(!result) return;
            api('ajax/box.php', {action:'remove_transaction', id:MExpense.$form.id.val()}, function(rsp){
                if(rsp.ok){
                    toastr.success('Eliminado correctamente...');
                    MExpense.$modal.modal('hide');
                    location.reload();
                } else {
                    bootbox.alert(rsp.msg);
                }
            }, 'Eliminando...');
        });
    }

};