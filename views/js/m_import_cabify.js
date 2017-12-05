// Modal Voucher
var MImportCabify = {

    $modal: null,
    $title: null, // Modal: Titulo
    $form: null, // Modal: Formulario

    $remove: null,

    init: function(){
        $body.append(
            '<div id="modal_import_cabify" class="modal fade modal-scroll" data-backdrop="static" data-keyboard="false">' +
            ' <div class="modal-dialog">' +
            '  <div class="modal-content">' +
            '   <div class="modal-header">' +
            '    <button type="button" class="close" data-dismiss="modal"></button>' +
            '    <h4 class="modal-title">---</h4>' +
            '   </div>' +
            '   <div class="modal-body">' +

            '    <form class="form-horizontal" action="drivers/import_cabify" method="post">' +
            '     <input type="file" name="file" accept=".csv">' +
            '    </form>' +

            '    <table class="table table-bordered table-hover" style="margin-top:10px">' +
            '     <thead>' +
            '     <tr>' +
            '      <th width="1%"> DNI </th>' +
            '      <th width="1%"> Monto </th>' +
            '      <th> Estado </th>' +
            '      <th width="1%">' +
            '      </th>' +
            '     </tr>' +
            '     </thead>' +
            '     <tbody class="items"></tbody>' +
            '    </table>' +

            '   </div>' +
            '   <div class="modal-footer">' +
            '    <button type="button" data-dismiss="modal" class="btn btn-outline btn-default cancel">Cerrar</button>' +
            '   </div>' +
            '  </div>' +
            ' </div>' +
            '</div>'
        );

        this.$modal         = $('#modal_import_cabify');
        this.$modal.title   = $('.modal-title', this.$modal);
        this.$modal.items   = $('.items', this.$modal);
        this.$form          = $('form', this.$modal);
        this.$form.file     = $('input[name=file]', this.$form);

        // Asignar eventos
        this.$form.ajaxForm(this.save);
        this.$form.file.change(function(){
            MImportCabify.$form.submit();
        });
        $('.save', this.$modal).click(this.save);

    },

    show: function(title){
        if(MImportCabify.$modal == null){
            MImportCabify.init();
        }
        MImportCabify.$modal.modal('show');
        MImportCabify.$modal.title.text(title);
    },

    // Guardar
    save: {
        beforeSend: function() {
            Loading.show();
            MImportCabify.$modal.items.html('');
        },
        complete: function(xhr) {
            Loading.hide();
            var rsp = getRsp(xhr.responseText);
            if(rsp.ok == true){
                MImportCabify.onSaved(rsp);
            } else {
                bootbox.alert(rsp.msg);
            }
        }
    },

    onSaved: function(rsp){
        var html = '';
        rsp.items.forEach(function(o,i){
            html += '<tr>';
            html += ' <td>'+o.dni+'</td>';
            html += ' <td class="nowrap">'+stg.coin+num(o.amount,2)+'</td>';
            if(o.ok){
                html += ' <td>';
                html += '  <span class="badge badge-success"> Guardado </span>';
                html += '  <span class="font-sm">';
                html += '   <a href="dues_rental/'+o.driver.id+'#num_due_'+(o.due.num_due-1)+'" target="_blank">Cuota</a>';
                html += '   ID: <b>'+o.due.id+'</b>, Número: <b>'+o.due.num_due+'</b>, Fecha: <b>'+o.due.date_due+'</b>';
                html += '  </span>';
                html += ' </td>';
            } else {
                html += ' <td><span class="badge badge-default"> '+o.msg+' </span></td>';
            }
            html += ' <td></td>';
            html += '</tr>';
        });
        MImportCabify.$modal.items.html(html);
    },

    // Abrir para nuevo NUEVO
    open: function(){
        MImportCabify.show('Importar Cabify');
    }

};