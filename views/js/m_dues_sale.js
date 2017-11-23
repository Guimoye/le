// Modal Driver
var MDuesSale = {

    callback: null,

    title_add:  'Generar Cronograma de Venta',
    title_edit: 'Editar conductor',

    $modal: null,
    $title: null, // Modal: Titulo
    $form: null, // Modal: Formulario

    $remove: null,

    init: function(callback){
        this.callback = (typeof callback === 'function') ? callback : null;

        this.$modal         = $('#modal_add_dues_sale');
        this.$modal.title   = $('.modal-title', this.$modal);
        this.$modal.remove  = $('.remove', this.$modal);

        this.$form                      = $('form', this.$modal);
        this.$form.id                   = $('input[name="id"]', this.$form);
        this.$form.surname 			    = $('input[name="surname"]', this.$form);

        // Asignar eventos
        this.$modal.remove.click(function(){
            MDuesSale.remove(MDuesSale.$id.val());
        });
        $('.save', this.$modal).click(this.save);

    },

    // Guardar
    save: function(){
        api('dues_sale/add', MDuesSale.$form.serializeObject(), function(rsp){
            if(rsp.ok == true){
                toastr.success('Guardado correctamente');
                MDuesSale.$modal.modal('hide');

                if(MDuesSale.callback == null){
                    location.reload();
                } else {
                    MDuesSale.callback(rsp.id, false);
                }

            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Registrando...');
    },
    
    // Abrir para nuevo NUEVO
    add: function(){
        MDuesSale.$modal.title.text(MDuesSale.title_add);
        MDuesSale.$modal.remove.hide();

        MDuesSale.$form.id.val('');
        MDuesSale.$form.surname.val('');

        MDuesSale.$modal.modal('show');
    },
    
    // Editar
    edit: function(o){
        MDuesSale.$modal.title.text(MDuesSale.title_edit);
        MDuesSale.$modal.remove.show();

        MDuesSale.$form.id.val(o.id);
        MDuesSale.$form.surname.val(o.surname);

        MDuesSale.$modal.modal('show');
    },

    // Eliminar
    remove: function(id){
        bootbox.confirm('¿Realmente desea eliminar?', function(result){
            if(result){
                api('dues_sale/remove', {action:'remove', id:id}, function(rsp){
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

    // Eliminar Cronograma de venta de un conductor
    removeAll: function(id_driver){
        bootbox.confirm({
            title: '¿Seguro que quieres eliminar todo el cronograma de venta?',
            message: 'Esta acción borrará todas las cuotas de venta, incluso aquellas que han sido pagadas. No podrás deshacer más tarde.',
            buttons: {
                confirm: {label:'ELIMINAR',className:'btn-danger'},
                cancel: {label:'Cancelar'}
            },
            callback: function(result){
                if(result){
                    api('dues_sale/remove_all', {action:'remove', id_driver:id_driver}, function(rsp){
                        if(rsp.ok == true){
                            toastr.success('Eliminado correctamente');
                            location.reload();
                        } else {
                            bootbox.alert(rsp.msg);
                        }
                    }, 'Eliminando...');
                }
            }
        });
    },

    // Marcar como pagado
    setDuePaid: function(id, amount_total){
        bootbox.prompt({
            title: 'Pagar cuota pendiente',
            placeholder: 'Ingrese el monto a pagar',
            callback: function(result){
                if(result == null) return;
                api('dues_sale/set_due_paid', {action:'set_due_paid', id:id, amount_total:amount_total, amount:result}, function(rsp){
                    if(rsp.ok == true){
                        toastr.success('Guardado correctamente');
                        location.reload();
                    } else {
                        bootbox.alert(rsp.msg);
                    }
                });
            }
        });
        /*bootbox.confirm('¿Marcar como pagado?', function(result){
            if(result){
                api('dues_sale/set_due_paid', {action:'set_due_paid', id:id, amount:amount}, function(rsp){
                    if(rsp.ok == true){
                        toastr.success('Guardado correctamente');
                        location.reload();
                    } else {
                        bootbox.alert(rsp.msg);
                    }
                });
            }
        });*/
    },

    // Marcar como pagado
    setDueUnpaid: function(id){
        bootbox.confirm('¿Marcar como no pagado?', function(result){
            if(result){
                api('dues_sale/set_due_unpaid', {action:'set_due_unpaid', id:id}, function(rsp){
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

// Modal Voucher
var MVoucher = {

    callback: null,

    $modal: null,
    $title: null, // Modal: Titulo
    $form: null, // Modal: Formulario

    $remove: null,

    init: function(callback){
        this.callback = (typeof callback === 'function') ? callback : null;

        this.$modal         = $('#modal_add_voucher');
        this.$modal.title   = $('.modal-title', this.$modal);
        this.$modal.remove  = $('.remove', this.$modal);

        this.$form          = $('form', this.$modal);
        this.$form.id       = $('input[name="id"]', this.$form);
        this.$form.photo    = $('input[name="photo"]', this.$form);
        this.$form.image    = $('.image', this.$form);
        this.$form.link     = $('.link', this.$form);

        // Asignar eventos
        this.$form.ajaxForm(this.save);
        this.$form.photo.change(function(){
            MVoucher.$form.submit();
            MVoucher.$form.photo.val('');
        });
        this.$modal.remove.click(function(){
            MVoucher.remove(MVoucher.$id.val());
        });
        $('.save', this.$modal).click(this.save);
    },

    // Guardar
    save: {
        beforeSend: function() {
            Loading.show();
        },
        complete: function(xhr) {
            Loading.hide();
            var rsp = JSON.parse(xhr.responseText);
            if(rsp.ok == true){
                //MVoucher.$modal.modal('hide');
                toastr.success('Guardado correctamente');
                //location.reload();
                MVoucher.open(MVoucher.$form.id.val(), rsp.pic_voucher)
            } else {
                bootbox.alert(rsp.message);
            }
        }
    },

    // Abrir para nuevo NUEVO
    open: function(id,pic_voucher){
        MVoucher.$modal.title.text('Comprobante de pago');
        MVoucher.$modal.modal('show');
        MVoucher.$form.id.val(id);

        var url = 'uploads/'+pic_voucher;

        // Verificar extension
        MVoucher.$form.image.hide();
        MVoucher.$form.link.hide();

        if((/\.(gif|jpg|jpeg|tiff|png)$/i).test(url)){
            MVoucher.$form.image.attr('src',url).show();

        } else if(pic_voucher != ''){
            MVoucher.$form.link.attr('href',url).show();
        }
    }

};

// Modal Dias
var MEditDuesSale = {

    callback: null,

    $modal: null,
    $title: null, // Modal: Titulo
    $form: null, // Modal: Formulario

    $remove: null,

    init: function(callback){
        this.callback = (typeof callback === 'function') ? callback : null;

        this.$modal         = $('#modal_edit_dues_sale');
        this.$modal.title   = $('.modal-title', this.$modal);
        this.$modal.remove  = $('.remove', this.$modal);

        this.$form                  = $('form', this.$modal);
        this.$form.id               = $('input[name="id"]', this.$form);
        this.$form.amount_penalty   = $('input[name="amount_penalty"]', this.$form);

        // Asignar eventos
        this.$form.submit(function(e){
            e.preventDefault();
            MEditDuesSale.save();
        });
        this.$modal.remove.click(function(){
            MEditDuesSale.remove(MEditDuesSale.$id.val());
        });
        $('.save', this.$modal).click(this.save);
    },

    // Guardar
    save: function(){
        api('dues_sale/edit', MEditDuesSale.$form.serializeObject(), function(rsp){
        if(rsp.ok == true){
            toastr.success('Guardado correctamente');
            MEditDuesSale.$modal.modal('hide');

            if(MEditDuesSale.callback == null){
                location.reload();
            } else {
                MEditDuesSale.callback(rsp.id, false);
            }

        } else {
            bootbox.alert(rsp.msg);
        }
    }, 'Registrando...');
    },

    // Abrir para nuevo NUEVO
    open: function(id,amount_menalty){
        MEditDuesSale.$modal.title.text('Editar');
        MEditDuesSale.$modal.modal('show');
        MEditDuesSale.$form.id.val(id);
        MEditDuesSale.$form.amount_penalty.val(amount_menalty);

    }

};