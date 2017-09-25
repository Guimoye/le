// Modal
var CSupply = {

    callback: null,
    $modal: null,

    items: null,

    init: function(callback){
        this.callback = (typeof callback === 'function') ? callback : null;

        $body.append(
            '<div id="modal_choose_supply" class="modal fade modal-scroll">'+
            ' <div class="modal-dialog">'+
            '  <div class="modal-content">'+
            '   <div class="modal-header">'+
            '    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>'+
            '    <h4 class="modal-title">Elegir insumo</h4>'+
            '   </div>'+
            '   <div class="modal-body">'+

            '    <table class="table table-bordered table-striped mdl-td">'+
            '     <thead>'+
            '     <tr>'+
            '      <th> Nombre </th>'+
            '      <th> U. M. </th>'+
            '      <th> Precio </th>'+
            '      <th width="1%"> Stock </th>'+
            '      <th width="1%"> </th>'+
            '     </tr>'+
            '     </thead>'+
            '     <tbody class="list"></tbody>'+
            '    </table>'+

            '   </div>'+
            '   <div class="modal-footer">'+
            '    <button type="button" data-dismiss="modal" class="btn cancel">Cancelar</button>'+
            '   </div>'+
            '  </div>'+
            ' </div>'+
            '</div>'
        );

        this.$modal         = $('#modal_choose_supply');
        this.$modal.title   = $('.modal-title', this.$modal);
        this.$modal.list    = $('.list', this.$modal);

        // Asignar eventos
    },

    // Guardar
    save: function(){
        api('ajax/clients.php', CSupply.$form.serializeObject(), function(rsp){
            if(rsp.ok == true){
                toastr.success('Guardado correctamente');
                CSupply.$modal.modal('hide');

                if(CSupply.callback == null){
                    location.reload();
                } else {
                    CSupply.callback(rsp.client, false);
                }

            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Registrando...');
    },
    
    // Abrir para nuevo NUEVO
    choose: function(){
        api('ajax/supplies.php', {action:'get_all'}, function(rsp){
            if(rsp.ok){
                var html = '';
                CSupply.items = rsp.items;
                CSupply.items.forEach(function(o,i){
                    html += '<tr>';
                    html += ' <td> '+o.name+' </td>';
                    html += ' <td> '+o.un_name+' </td>';
                    html += ' <td> '+stg.coin+num(o.cost,2)+' </td>';
                    html += ' <td> '+o.stock+' </td>';
                    html += ' <td style="padding:5px">';
                    html += '  <button class="btn btn-primary btn-sm btn-block" onclick="CSupply.back(CSupply.items['+i+']);">Elegir</button>';
                    html += ' </td>';
                    html += '</tr>';
                });
                CSupply.$modal.list.html(html);
                CSupply.$modal.modal('show');
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Obteniendo insumos...', true);
    },

    back: function(o){
        CSupply.callback(o);
        CSupply.$modal.modal('hide');
    }

};