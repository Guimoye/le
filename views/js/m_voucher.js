// Modal Voucher
var MVoucher = {

    $modal: null,
    $title: null, // Modal: Titulo
    $form: null, // Modal: Formulario

    $remove: null,

    init: function(){
        $body.append(
            '' +
            '<div id="modal_add_voucher" class="modal fade modal-scroll" data-backdrop="static" data-keyboard="false">' +
            '    <div class="modal-dialog">' +
            '        <div class="modal-content">' +
            '            <div class="modal-header">' +
            '                <button type="button" class="close" data-dismiss="modal"></button>' +
            '                <h4 class="modal-title">---</h4>' +
            '            </div>' +
            '            <div class="modal-body">' +
            '' +
            '                <form class="form-horizontal" action="pics/upload" method="post">' +
            '                    <input type="hidden" name="action" value="upload_voucher">' +
            '                    <input type="hidden" name="type" value="">' +
            '                    <input type="hidden" name="id_ref" value="">' +
            '' +
            '                    <div class="fileinput fileinput-new" data-provides="fileinput">' +
            '                        <span class="btn green btn-file">' +
            '                            <span class="fileinput-new"> Elegir imagen... </span>' +
            '                            <span class="fileinput-exists"> Cambiar... </span>' +
            '                            <input type="file" name="photo">' +
            '                        </span>' +
            '                        <span class="fileinput-filename"> </span> &nbsp;' +
            '                        <a href="javascript:;" class="close fileinput-exists" data-dismiss="fileinput"> </a>' +
            '                    </div>' +
            '' +
            '                    <table class="table table-bordered table-hover" style="margin-top:10px">' +
            '                        <thead>' +
            '                        <tr>' +
            '                            <th width="1%"> ID </th>' +
            '                            <th width="1%"> Fecha </th>' +
            '                            <th> Voucher </th>' +
            '                            <th width="1%">' +
            '                            </th>' +
            '                        </tr>' +
            '                        </thead>' +
            '                        <tbody class="pics"></tbody>' +
            '                    </table>' +
            '                </form>' +
            '' +
            '            </div>' +
            '            <div class="modal-footer">' +
            '                <button type="button" data-dismiss="modal" class="btn btn-outline btn-default cancel">Cerrar</button>' +
            '            </div>' +
            '        </div>' +
            '    </div>' +
            '</div>'
        );

        this.$modal         = $('#modal_add_voucher');
        this.$modal.title   = $('.modal-title', this.$modal);
        this.$modal.pics    = $('.pics', this.$modal);
        this.$form          = $('form', this.$modal);
        this.$form.type     = $('input[name=type]', this.$form);
        this.$form.id_ref   = $('input[name=id_ref]', this.$form);
        this.$form.photo    = $('input[name=photo]', this.$form);

        // Asignar eventos
        this.$form.ajaxForm(this.save);
        this.$form.photo.change(function(){
            MVoucher.$form.submit();
        });
        $('.save', this.$modal).click(this.save);

    },

    show: function(){
        if(MVoucher.$modal == null){
            MVoucher.init();
        }
        MVoucher.$modal.modal('show');
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
                toastr.success('Guardado correctamente');
                MVoucher.loadData();
            } else {
                bootbox.alert(rsp.msg);
            }
        }
    },

    // Abrir para nuevo NUEVO
    type: 0,
    id_ref: 0,
    open: function(type,id_ref){
        MVoucher.type = type;
        MVoucher.id_ref = id_ref;
        MVoucher.show();
        MVoucher.$modal.title.text('Comprobantes de pago');
        MVoucher.$form.type.val(type);
        MVoucher.$form.id_ref.val(id_ref);

        MVoucher.loadData();
    },

    loadData: function(){
        api('pics/get_all', {type:MVoucher.type, id_ref:MVoucher.id_ref}, function(rsp){
            if(rsp.ok){
                var html = '';

                if(rsp.items.length > 0){
                    rsp.items.forEach(function(o,i){
                        var url = 'uploads/'+o.pic;

                        html += '';
                        html += '<tr>';
                        html += '    <td>'+o.id+'</td>';
                        html += '    <td class="nowrap">'+o.date_added+'</td>';
                        html += '    <td>';

                        if((/\.(gif|jpg|jpeg|tiff|png)$/i).test(url)){
                            html += '<a class="bootbox" href="'+url+'" target="_blank">';
                            html += ' <img src="'+url+'" style="max-width:100%;max-height:120px">';
                            html += '</a>';

                        } else {
                            html += '<a href="'+url+'" class="btn btn-default btn-sm link" target="_blank">Mostrar archivo</a>';
                        }

                        html += '    </td>';
                        html += '    <td>';
                        html += '        <span class="btn btn-outline btn-circle dark btn-sm font-md" onclick="MVoucher.removePic('+o.id+');">';
                        html += '            <i class="fa fa-close"></i>';
                        html += '        </span>';
                        html += '    </td>';
                        html += '</tr>';
                    });
                } else {
                    html += '' +
                        '<tr>' +
                        '    <td colspan="100%"><div class="alert alert-warning">No hay comprobantes.</div></td>' +
                        '</tr>';
                }

                MVoucher.$modal.pics.html(html);

            } else {
                bootbox.alert(rsp.msg);
            }
        });
    },

    removePic: function(id) {
        bootbox.confirm('¿Eliminar comprobante?', function(result){
            if(result){
                api('pics/remove', {id:id}, function(rsp){
                    if(rsp.ok){
                        toastr.success('Eliminado correctamente');
                        MVoucher.loadData();

                    } else {
                        bootbox.alert(rsp.msg);
                    }
                });
            }
        });
    }

};