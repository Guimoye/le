var MDuesLoansPay = {

    $modal: null,
    $form: null, // Modal: Formulario

    $remove: null,

    init: function(){
        if(this.$modal != null) return;

        this.$modal         = $('#modal_pay_dues_loans');
        this.$modal.title   = $('.modal-title', this.$modal);

        this.$form                  = $('form', this.$modal);
        this.$form.id               = $('input[name="id"]', this.$form);
        this.$form.amount 	        = $('input[name="amount"]', this.$form);
        this.$form.date_paid        = $('input[name="date_paid"]', this.$form);

        // Asignar eventos
        $('.save', this.$modal).click(this.save);
    },

    show: function(){
        MDuesLoansPay.init();
        MDuesLoansPay.$modal.modal('show');
    },

    // Guardar
    save: function(){
        api('dues_loans/set_due_paid', MDuesLoansPay.$form.serializeObject(), function(rsp){
            if(rsp.ok == true){
                toastr.success('Guardado correctamente');
                MDuesLoansPay.$modal.modal('hide');

                location.reload();

            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Registrando...');
    },

    // Pagar
    open: function(o){
        MDuesLoansPay.show();
        MDuesLoansPay.$modal.title.text('Pagar cuota');

        MDuesLoansPay.$form.id.val(o.id);
        MDuesLoansPay.$form.amount.val('');
        MDuesLoansPay.$form.date_paid.val(o.date_paid);
    }

};