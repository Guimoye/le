
var itemss=null;
var MDuesRentalPay = {

    $modal: null,
    $form: null, // Modal: Formulario

    $remove: null,

    init: function () {
        if (this.$modal != null) return;

        this.$modal = $('#modal_dues_rental_pay');
        this.$modal.title = $('.modal-title', this.$modal);
        this.$modal.save = $('.save', this.$modal);

        this.$form = $('form', this.$modal);
        this.$form.id = $('input[name="id"]', this.$form);
        this.$form.amount_total = $('input[name="amount_total"]', this.$form);
        this.$form.amount_paid = $('input[name="amount_paid"]', this.$form);
        this.$form.amount_cabify = $('input[name="amount_cabify"]', this.$form);
        this.$form.amount_penalty = $('input[name="amount_penalty"]', this.$form);
        this.$form.amount_discount = $('input[name="amount_discount"]', this.$form);
        this.$form.amount_additionals = $('input[name="amount_additionals"]', this.$form);
        this.$form.comment_additionals = $('input[name="comment_additionals"]', this.$form);
        this.$form.date_paid = $('input[name="date_paid"]', this.$form);
        this.$form.voucher_code = $('input[name="voucher_code"]', this.$form);
        this.$form.photo_date = $('input[name=photo_date]', this.$form);
        this.$form.photo = $('input[name=photo]', this.$form);
        this.$form.pics = $('.pics', this.$form);

        // Asignar eventos
        //$('.save', this.$modal).click(this.save);
        this.$form.submit(function (e) {
            e.preventDefault();
            if (stg.can_edit) {
                MDuesRentalPay.save();
            } else {
                toastr.warning('Acción no permitida');
            }
        });

        this.$modal.on('shown.bs.modal', function () {
            MDuesRentalPay.$form.amount_paid.focus();
        });

        if (stg.can_edit) {
            this.$form.photo.change(this.changedPic);

        } else {
            MDuesRentalPay.$form.photo_date.parent().parent().hide();
            MDuesRentalPay.$form.photo.parent().parent().hide();
            MDuesRentalPay.$modal.save.hide();
        }

    },

    show: function () {
        MDuesRentalPay.init();
        MDuesRentalPay.$modal.modal('show');
    },

    changedPic: function () {

        var data = new FormData(MDuesRentalPay.$form.get(0));
        data.append('type', 1);
        data.append('id_ref', MDuesRentalPay.$form.id.val());

        Loading.show();
        $.ajax({
            type: 'POST',
            url: 'pics/upload',
            data: data,
            cache: false,
            contentType: false,
            processData: false,
            success: function (rsp) {
                Loading.hide();
                if (rsp.ok) {
                    toastr.success('Guardado correctamente');
                    MDuesRentalPay.loadPics();
                } else {
                    bootbox.alert(rsp.msg);
                }
            }
        });
    },

    removePic: function (id) {
        bootbox.confirm('¿Eliminar comprobante?', function (result) {
            if (result) {
                api('pics/remove', {id: id}, function (rsp) {
                    if (rsp.ok) {
                        toastr.success('Eliminado correctamente');
                        MDuesRentalPay.loadPics();

                    } else {
                        bootbox.alert(rsp.msg);
                    }
                });
            }
        });
    },

    // Guardar
    save: function () {

        var data = MDuesRentalPay.$form.serializeObject();
        data.items = [];

        $('.addFileItem').each(function(){
            var $this = $(this);
            data.items.push({
                descripcion: $this.find('input[name=descripcion]').val(),
                descripcion_date: $this.find('input[name=descripcion_date]').val()
            });
        });

        console.log(data);

        api('dues_rental/set_due_paid', data, function (rsp) {

            if (rsp.ok == true) {
                toastr.success('Guardado correctamente');
                MDuesRentalPay.$modal.modal('hide');

                location.reload();

            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Registrando...');
    },

    // Pagar
    open: function (o) {
        MDuesRentalPay.show();

        MDuesRentalPay.$modal.title.text(stg.can_edit ? 'Pagar cuota' : 'Detalle de pago');

        MDuesRentalPay.$form.id.val(o.id);
        MDuesRentalPay.$form.amount_total.val(o.amount_total);
        MDuesRentalPay.$form.amount_paid.val(o.amount_paid != '0' ? o.amount_paid : '');
        MDuesRentalPay.$form.amount_paid.attr('placeholder', 'Monto total: ' + stg.coin + o.amount_total);
        //MDuesRentalPay.$form.amount_paid.val(o.amount_due);
        MDuesRentalPay.$form.amount_cabify.val(o.amount_cabify);
        MDuesRentalPay.$form.amount_penalty.val(o.amount_penalty);
        MDuesRentalPay.$form.amount_discount.val(o.amount_discount);
        MDuesRentalPay.$form.amount_additionals.val(o.amount_additionals);
        MDuesRentalPay.$form.comment_additionals.val(o.comment_additionals);
        MDuesRentalPay.$form.date_paid.val(o.date_paid == null ? o.date_due : o.date_paid);
        MDuesRentalPay.$form.voucher_code.val(o.voucher_code);

        itemss =  JSON.parse(o.sub_paids);
        MDuesRentalPay.loadPics();
        console.log(o.date_paid);

    },

    loadPics: function () {

        var id = MDuesRentalPay.$form.id.val();

        var html = '';
        if(itemss!=null) {
            console.log(itemss);
        }

        MDuesRentalPay.$form.pics.html('<tr><td colspan="100%">Cargando...</td></tr>');

        api('pics/get_all', {type: 1, id_ref: id}, function (rsp) {
            if (rsp.ok) {
                var html = '';

                if (rsp.items.length > 0) {
                    rsp.items.forEach(function (o, i) {
                        var url = 'uploads/' + o.pic;

                        html += '';
                        html += '<tr>';
                        html += '    <td>' + o.id + '</td>';
                        html += '    <td class="nowrap">' + o.date_added + '</td>';
                        html += '    <td>';

                        if ((/\.(gif|jpg|jpeg|tiff|png)$/i).test(url)) {

                            html += '<a class="bootbox" href="' + url + '" target="_blank">';
                            html += ' <img src="' + url + '" style="max-width:100%;max-height:120px">';
                            html += '</a>';

                        } else {
                            html += '<a href="' + url + '" class="btn btn-default btn-sm link" target="_blank">Mostrar archivo</a>';
                        }

                        html += '    </td>';
                        html += '    <td>';
                        if (stg.can_edit) {
                            html += '        <span class="btn btn-outline btn-circle dark btn-sm font-md" onclick="MDuesRentalPay.removePic(' + o.id + ');">';
                            html += '            <i class="fa fa-close"></i>';
                            html += '        </span>';
                        }
                        html += '    </td>';
                        html += '</tr>';
                    });
                } else {
                    html += '' +
                        '<tr>' +
                        '    <td colspan="100%"><div class="alert alert-warning" style="margin:0">No hay comprobantes.</div></td>' +
                        '</tr>';
                }

                MDuesRentalPay.$form.pics.html(html);

            } else {
                MDuesRentalPay.$form.pics.html('<tr><td colspan="100%">' + rsp.msg + '</td></tr>');
            }
        });
    }

};


$('.addFieldBtn').on('click', function() {
    var $template = $('.addField').clone();

         $template
             .removeClass('hide addField')
            .addClass('addFileItem')
            .removeAttr('id')
            .insertBefore($(".addField"));

});