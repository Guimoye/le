// Unidades de medida
var MBox = {

    tit_add: 'Agregar caja',
    tit_edit: 'Editar caja',

    $modal: null,
    $form: null,

    init: function(){
        this.$modal = $('#modal_add_box');
        this.$modal.title = $('.modal-title', this.$modal);
        this.$modal.remove = $('.remove', this.$modal);
        this.$form                  = $('form', this.$modal);
        this.$form.id               = $('input[name=id]', this.$form);
        this.$form.name             = $('input[name=name]', this.$form);
        this.$form.printer_ip               = $('input[name=printer_ip]', this.$form);
        this.$form.printer_name             = $('input[name=printer_name]', this.$form);
        this.$form.printer_serial           = $('input[name=printer_serial]', this.$form);
        this.$form.printer_line_letters     = $('input[name=printer_line_letters]', this.$form);
        this.$form.printer2_ip              = $('input[name=printer2_ip]', this.$form);
        this.$form.printer2_name            = $('input[name=printer2_name]', this.$form);
        this.$form.printer2_serial          = $('input[name=printer2_serial]', this.$form);
        this.$form.printer2_line_letters    = $('input[name=printer2_line_letters]', this.$form);

        // Actions
        this.$modal.remove.click(this.remove);
        $('.save', this.$modal).click(this.save);
        $('.test_print', this.$modal).click(this.testPrint);
        $('.test_print2', this.$modal).click(this.testPrint2);
        this.$form.submit(function(e){
            e.preventDefault();
            MBox.save();
        });
        this.$modal.on('shown.bs.modal', function() {
            MBox.$form.name.focus();
        })
    },

    add: function(){
        MBox.$form.id.val('');
        MBox.$form.name.val('');
        MBox.$form.printer_ip.val('');
        MBox.$form.printer_name.val('');
        MBox.$form.printer_serial.val('');
        MBox.$form.printer_line_letters.val('');
        MBox.$form.printer2_ip.val('');
        MBox.$form.printer2_name.val('');
        MBox.$form.printer2_serial.val('');
        MBox.$form.printer2_line_letters.val('');
        MBox.$modal.title.html(MBox.tit_add);
        MBox.$modal.remove.hide();
        MBox.$modal.modal('show');
    },

    edit: function(o){
        MBox.$form.id.val(o.id);
        MBox.$form.name.val(o.name);
        MBox.$form.printer_ip.val(o.printer_ip);
        MBox.$form.printer_name.val(o.printer_name);
        MBox.$form.printer_serial.val(o.printer_serial);
        MBox.$form.printer_line_letters.val(o.printer_line_letters > 0 ? o.printer_line_letters : '');
        MBox.$form.printer2_ip.val(o.printer2_ip);
        MBox.$form.printer2_name.val(o.printer2_name);
        MBox.$form.printer2_serial.val(o.printer2_serial);
        MBox.$form.printer2_line_letters.val(o.printer2_line_letters > 0 ? o.printer2_line_letters : '');
        MBox.$modal.title.html(MBox.tit_edit);
        MBox.$modal.remove.show();
        MBox.$modal.modal('show');
    },

    save: function(){
        api('ajax/boxes.php', MBox.$form.serializeObject(), function(rsp){
            if(rsp.ok){
                toastr.success('Guardado correctamente');
                MBox.$modal.modal('hide');
                location.reload();
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Guardando...');
    },

    remove: function(){
        bootbox.confirm('¿Eliminar unidad de medida?', function(result){
            if(!result) return;
            api('ajax/boxes.php', {action:'remove', id:MBox.$form.id.val()}, function(rsp){
                if(rsp.ok){
                    toastr.success('Eliminado correctamente...');
                    MBox.$modal.modal('hide');
                    location.reload();
                } else {
                    bootbox.alert(rsp.msg);
                }
            }, 'Eliminando...');
        });
    },

    testPrint: function(){
        Print.test(
            MBox.$form.printer_ip.val(),
            MBox.$form.printer_name.val(),
            MBox.$form.printer_serial.val(),
            MBox.$form.printer_line_letters.val()
        );
    },

    testPrint2: function(){
        Print.test(
            MBox.$form.printer2_ip.val(),
            MBox.$form.printer2_name.val(),
            MBox.$form.printer2_serial.val(),
            MBox.$form.printer2_line_letters.val()
        );
    }

};