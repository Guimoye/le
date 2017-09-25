html += '<div class="tab-pane fade active in" id="tab_1_2">';
html += '';
html += '  <form class="form-horizontal">';
html += '    <input type="hidden" name="action" value="add">';
html += '    <input type="hidden" name="id" value="">';
html += '';
html += '    <div class="form-group">';
html += '      <label class="col-md-3 control-label">Nombre</label>';
html += '      <div class="col-md-9">';
html += '        <input type="text" class="form-control" name="name" placeholder="Nombre de la presentación">';
html += '      </div>';
html += '    </div>';
html += '';
html += '    <div class="form-group">';
html += '      <label class="col-md-3 control-label">Costo</label>';
html += '      <div class="col-md-9">';
html += '        <input type="number" class="form-control" name="cost" placeholder="Costo de producción">';
html += '      </div>';
html += '    </div>';
html += '';
html += '    <div class="form-group">';
html += '      <label class="col-md-3 control-label">Precio</label>';
html += '      <div class="col-md-9">';
html += '        <input type="number" class="form-control" name="price" placeholder="Precio de venta">';
html += '      </div>';
html += '    </div>';
html += '';
html += '    <div class="form-group">';
html += '      <label class="col-md-3 control-label">Puntos</label>';
html += '      <div class="col-md-9">';
html += '        <input type="number" class="form-control" name="points" placeholder="Puntos para este producto">';
html += '      </div>';
html += '    </div>';
html += '';
html += '    <div class="form-group">';
html += '      <label class="col-md-3 control-label">Comisión</label>';
html += '      <div class="col-md-9">';
html += '        <input type="number" class="form-control" name="commision" placeholder="Comisión para el mozo">';
html += '      </div>';
html += '    </div>';
html += '';
html += '    <div class="form-group">';
html += '      <label class="col-md-3 control-label"></label>';
html += '      <div class="col-md-9">';
html += '        <label>';
html += '          <input type="checkbox" name="in_deli"> Mostrar en deliverys';
html += '        </label>';
html += '        <label>';
html += '          <input type="checkbox" name="in_deli"> Lleva control de stock';
html += '        </label>';
html += '        <label>';
html += '          <input type="checkbox" name="in_deli"> Lleva ingredientes';
html += '        </label>';
html += '      </div>';
html += '    </div>';
html += '';
html += '    <div class="form-group">';
html += '      <div class="col-md-12">';
html += '        <table class="table table-bordered" style="margin-bottom:0">';
html += '          <thead>';
html += '          <tr>';
html += '            <td colspan="4">';
html += '              <div class="input-icon">';
html += '                <i class="fa fa-search"></i>';
html += '                <input type="text" name="query" class="form-control" placeholder="Buscar insumos...">';
html += '              </div>';
html += '            </td>';
html += '          </tr>';
html += '          <tr>';
html += '            <th>Prodcto</th>';
html += '            <th>Cantidad</th>';
html += '            <th>UM</th>';
html += '            <th width="1%"></th>';
html += '          </tr>';
html += '          </thead>';
html += '          <tbody>';
html += '          <tr>';
html += '            <td>Milanessa</td>';
html += '            <td><input type="number" name="quantity" value="1" class="form-control"></td>';
html += '            <td>';
html += '              <select class="form-control" name="id_unimed">';
html += '                <option value="">Elegir...</option>';
html += '              </select>';
html += '            </td>';
html += '            <td style="vertical-align:middle">';
html += '              <button type="button" class="close" style="padding:10px"></button>';
html += '            </td>';
html += '          </tr>';
html += '          </tbody>';
html += '        </table>';
html += '      </div>';
html += '    </div>';
html += '';
html += '    <div class="modal-footer">';
html += '      <button type="button" class="btn red remove pull-left">Eliminar</button>';
html += '      <button type="button" class="btn default cancel" data-dismiss="modal">Cancelar</button>';
html += '      <button type="button" class="btn green save">Guardar</button>';
html += '    </div>';
html += '  </form>';
html += '';
html += '</div>';