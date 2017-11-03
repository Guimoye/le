// Modal
var MLoan = {

    callback: null,

    $modal: null,
    $form: null, // Modal: Formulario

    $remove: null,

    init: function(callback){
        this.callback = (typeof callback === 'function') ? callback : null;

        this.$modal         = $('#modal_add_loan');
        this.$modal.title   = $('.modal-title', this.$modal);
        this.$modal.remove  = $('.remove', this.$modal);

        this.$form              = $('form', this.$modal);
        this.$form.id           = $('input[name="id"]', this.$form);
        this.$form.description  = $('input[name="description"]', this.$form);
        this.$form.num_dues     = $('input[name="num_dues"]', this.$form);
        this.$form.tea          = $('input[name="tea"]', this.$form);
        this.$form.amount       = $('input[name="amount"]', this.$form);
        this.$form.date_pay     = $('select[name="date_pay"]', this.$form);

        // Asignar eventos
        this.$modal.remove.click(function(){
            MLoan.remove(MLoan.$form.id.val());
        });
        $('.save', this.$modal).click(this.save);

    },

    // Guardar
    save: function(){
        api('loans/add', MLoan.$form.serializeObject(), function(rsp){
            if(rsp.ok == true){
                toastr.success('Guardado correctamente');
                MLoan.$modal.modal('hide');

                if(MLoan.callback == null){
                    location.reload();
                } else {
                    MLoan.callback(rsp.id, false);
                }

            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Registrando...');
    },
    
    // Abrir para nuevo NUEVO
    add: function(){
        MLoan.$modal.title.text("Agregar préstamo");
        MLoan.$modal.remove.hide();

        MLoan.$form.id.val('');
        MLoan.$form.description.val('');
        MLoan.$form.num_dues.val('');
        MLoan.$form.tea.val('');
        MLoan.$form.amount.val('');
        MLoan.$form.date_pay.val('');

        MLoan.$modal.modal('show');
    },
    
    // Editar
    edit: function(o){
        MLoan.$modal.title.text("Editar préstamo");
        MLoan.$modal.remove.show();

        MLoan.$form.id.val(o.id);
        MLoan.$form.description.val(o.description);
        MLoan.$form.num_dues.val(o.num_dues);
        MLoan.$form.tea.val(o.tea);
        MLoan.$form.amount.val(o.amount);
        MLoan.$form.date_pay.val(o.date_pay);

        MLoan.$modal.modal('show');
    },

    // Eliminar
    remove: function(id){
        bootbox.confirm('¿Realmente desea eliminar?', function(result){
            if(result){
                api('loans/remove', {action:'remove', id:id}, function(rsp){
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
                api('loans/set_paid', {action:'set_paid', id:id}, function(rsp){
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

// Modal prestamos
var MLoanDues = {

    $modal: null,

    init: function(){
        if(this.$modal != null) return;

        this.$modal = $('#modal_loan_dues');
        this.$modal.list = $('tbody', this.$modal);
    },

    show: function(){
        MLoanDues.init();
        MLoanDues.$modal.modal('show');
    },

    open: function(id_loan){
        api('loans/get_dues', {id_loan:id_loan}, function(rsp){
            if(rsp.ok){
                MLoanDues.show();
                var html = '';

                $.each(rsp.dues, function(i,o){
                    html += '<tr>';
                    html += ' <td>'+(i+1)+'</td>';
                    html += ' <td>'+o.date_due+'</td>';
                    html += ' <td class="nowrap">'+stg.coin+num(o.amount_due,2)+'</td>';
                    html += '</tr>';
                });

                MLoanDues.$modal.list.html(html);
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Obteniendo cuotas...');
    }

};