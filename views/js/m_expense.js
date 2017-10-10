// Modal Expense
var MExpense = {

    callback: null,

    $modal: null,
    $title: null, // Modal: Titulo
    $form: null, // Modal: Formulario

    $remove: null,

    init: function(callback){
        this.callback = (typeof callback === 'function') ? callback : null;

        this.$modal         = $('#modal_add_expense');
        this.$modal.title   = $('.modal-title', this.$modal);
        this.$modal.remove  = $('.remove', this.$modal);

        this.$form              = $('form', this.$modal);
        this.$form.id           = $('input[name="id"]', this.$form);
        this.$form.description  = $('input[name="description"]', this.$form);
        this.$form.amount       = $('input[name="amount"]', this.$form);
        this.$form.date_pay     = $('input[name="date_pay"]', this.$form);

        // Asignar eventos
        this.$modal.remove.click(function(){
            MExpense.remove(MExpense.$form.id.val());
        });
        $('.save', this.$modal).click(this.save);

    },

    // Guardar
    save: function(){
        api('expenses/add', MExpense.$form.serializeObject(), function(rsp){
            if(rsp.ok == true){
                toastr.success('Guardado correctamente');
                MExpense.$modal.modal('hide');

                if(MExpense.callback == null){
                    location.reload();
                } else {
                    MExpense.callback(rsp.id, false);
                }

            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Registrando...');
    },
    
    // Abrir para nuevo NUEVO
    add: function(){
        MExpense.$modal.title.text("Agregar gasto");
        MExpense.$modal.remove.hide();

        MExpense.$form.id.val('');
        MExpense.$form.description.val('');
        MExpense.$form.amount.val('');
        MExpense.$form.date_pay.val('');

        MExpense.$modal.modal('show');
    },
    
    // Editar
    edit: function(o){
        MExpense.$modal.title.text("Editar gasto");
        MExpense.$modal.remove.show();

        MExpense.$form.id.val(o.id);
        MExpense.$form.description.val(o.description);
        MExpense.$form.amount.val(o.amount);
        MExpense.$form.date_pay.val(o.date_pay);

        MExpense.$modal.modal('show');
    },

    // Eliminar
    remove: function(id){
        bootbox.confirm('¿Realmente desea eliminar?', function(result){
            if(result){
                api('expenses/remove', {action:'remove', id:id}, function(rsp){
                    if(rsp.ok == true){
                        toastr.success('Eliminado correctamente');
                        location.reload();
                    } else {
                        bootbox.alert(rsp.msg);
                    }
                }, 'Eliminando...');
            }
        });
    },

    // Marcar como pagado
    setPaid: function(id){
        bootbox.confirm('¿Marcar como pagado?', function(result){
            if(result){
                api('expenses/set_paid', {action:'set_paid', id:id}, function(rsp){
                    if(rsp.ok == true){
                        toastr.success('Guardado correctamente');
                        location.reload();
                    } else {
                        bootbox.alert(rsp.msg);
                    }
                });
            }
        });
    }

};