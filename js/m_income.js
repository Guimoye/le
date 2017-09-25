// Unidades de medida
var MIncome = {

    tit_add: 'Registrar ingreso',
    tit_edit: 'Editar ingreso',

    $modal: null,
    $form: null,

    init: function(){
        this.$modal = $('#modal_add_provider');
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
            MIncome.save();
        });
        this.$modal.on('shown.bs.modal', function() {
            MIncome.$form.name.focus();
        })
    },

    add: function(){
        MIncome.$form.id.val('');
        MIncome.$form.notes.val('');
        MIncome.$form.total.val('');
        MIncome.$form.coin.val(0);
        MIncome.$modal.title.html(MIncome.tit_add);
        MIncome.$modal.remove.hide();
        MIncome.$modal.modal('show');
    },

    edit: function(o){
        MIncome.$form.id.val(o.id);
        MIncome.$form.notes.val(o.notes);
        MIncome.$form.total.val(o.total);
        MIncome.$form.coin.val(o.coin);
        MIncome.$modal.title.html(MIncome.tit_edit);
        MIncome.$modal.remove.show();
        MIncome.$modal.modal('show');
    },

    save: function(){
        api('ajax/box.php', MIncome.$form.serializeObject(), function(rsp){
            if(rsp.ok){
                toastr.success('Guardado correctamente');
                MIncome.$modal.modal('hide');
                location.reload();
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Guardando...');
    },

    remove: function(){
        bootbox.confirm('¿Seguro que quieres eliminarlo?', function(result){
            if(!result) return;
            api('ajax/box.php', {action:'remove_transaction', id:MIncome.$form.id.val()}, function(rsp){
                if(rsp.ok){
                    toastr.success('Eliminado correctamente...');
                    MIncome.$modal.modal('hide');
                    location.reload();
                } else {
                    bootbox.alert(rsp.msg);
                }
            }, 'Eliminando...');
        });
    }

};