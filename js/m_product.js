// Unidades de medida
var MProduct = {

    tit_add: 'Agregar producto',
    tit_edit: 'Editar producto',

    $modal: null,
    $form: null,

    init: function(){
        this.$modal = $('#modal_add_area');
        this.$modal.title = $('.modal-title.prod', this.$modal);
        this.$form              = $('.frm_data', this.$modal);
        this.$form.id           = $('input[name=id]', this.$form);
        this.$form.id_area      = $('select[name=id_area]', this.$form);
        this.$form.id_category  = $('select[name=id_category]', this.$form);
        this.$form.id_unimed    = $('select[name=id_unimed]', this.$form);
        this.$form.name         = $('input[name=name]', this.$form);
        this.$form.description  = $('textarea[name=description]', this.$form);
        this.$form.notes        = $('input[name=notes]', this.$form);
        this.$form.remove       = $('.remove', this.$form);

        this.$propres           = $('.propres', this.$modal);
        this.$propres.tabs      = $('.nav', this.$propres);
        this.$propres.items     = $('.tab-content', this.$propres);

        // Actions
        this.$form.remove.click(this.remove);

        $('.save', this.$form).click(this.save);
        //$('.save', this.$propres).click(this.savePropres);

        this.$propres.on('click', '.save', this.savePropres);
        this.$propres.on('click', '.remove', this.removePropres);
        this.$propres.on('click', '.remove_supply', this.removeSupply);
        this.$propres.on('change', 'input[name=has_stock]', this.hasStockChanged);
        this.$propres.on('change', 'input[name=has_supply]', this.hasSupplyChanged);

        this.$form.submit(function(e){
            e.preventDefault();
            MProduct.save();
        });
        this.$modal.on('shown.bs.modal', function(){
            $body.addClass('noscroll');
        });
        this.$modal.on('hidden.bs.modal', function(){
            $body.removeClass('noscroll');
        });
    },

    getDropdowns: function(id_area, id_category, id_unimed){
        MProduct.$form.id_area.html('<option>Cargando...</option>').prop('disabled', true);
        MProduct.$form.id_category.html('<option>Cargando...</option>').prop('disabled', true);
        MProduct.$form.id_unimed.html('<option>Cargando...</option>').prop('disabled', true);

        api('ajax/products.php', {action:'get_dropdowns'}, function(rsp){
            var html_areas      = '<option value="">Elegir...</option>',
                html_categories = '<option value="">Elegir...</option>',
                html_unimeds    = '<option value="">Elegir...</option>';

            rsp.areas.forEach(function(o){
                html_areas += '<option value="'+o.id+'" '+(o.id==id_area?'selected':'')+'>'+o.name+'</option>';
            });

            html_categories += mkMenuSelect(rsp.categories,0,id_category);

            rsp.unimeds.forEach(function(o){
                html_unimeds += '<option value="'+o.id+'" '+(o.id==id_unimed?'selected':'')+'>'+o.name+'</option>';
            });

            MProduct.$form.id_area.html(html_areas).prop('disabled', false);
            MProduct.$form.id_category.html(html_categories).prop('disabled', false);
            MProduct.$form.id_unimed.html(html_unimeds).prop('disabled', false);
        }, false, true);

    },

    add: function(){
        MProduct.$form.id.val('');
        MProduct.$form.name.val('');
        MProduct.$form.description.val('');
        MProduct.$form.notes.val('');
        MProduct.$modal.title.html(MProduct.tit_add);
        MProduct.$form.remove.hide();
        MProduct.getDropdowns(0,0,0);

        MProduct.$propres.tabs.hide('');
        MProduct.$propres.items.html('Guarde el producto para crear presentaciones.');

        MProduct.$modal.modal('show');
    },

    edit: function(o){
        MProduct.$form.id.val(o.id);
        MProduct.$form.name.val(o.name);
        MProduct.$form.description.val(o.description);
        MProduct.$form.notes.val(o.notes);
        MProduct.$modal.title.html(MProduct.tit_edit);
        MProduct.$form.remove.show();
        MProduct.getDropdowns(o.id_area,o.id_category,o.id_unimed);

        MProduct.getPropres(o.id);

        MProduct.$modal.modal('show');
    },

    save: function(){
        var data = MProduct.$form.serializeObject();
        api('ajax/products.php', data, function(rsp){
            if(rsp.ok){
                toastr.success('Guardado correctamente');
                //MProduct.$modal.modal('hide');
                //location.reload();

                if(data.id == '' || data.id == '0'){
                    MProduct.edit(rsp.product);
                }

                //MProduct.showPropres(rsp.id);
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Guardando...');
    },

    remove: function(){
        bootbox.confirm('¿Seguro que quieres eliminarlo?', function(result){
            if(!result) return;
            api('ajax/products.php', {action:'remove', id:MProduct.$form.id.val()}, function(rsp){
                if(rsp.ok){
                    toastr.success('Eliminado correctamente...');
                    MProduct.$modal.modal('hide');
                    location.reload();
                } else {
                    bootbox.alert(rsp.msg);
                }
            }, 'Eliminando...');
        });
    },

    /**
     * Propres
     */
    // Obtener propres
    getPropres: function(id_product){
        MProduct.$propres.tabs.hide();
        MProduct.$propres.items.html('<i class="fa fa-spinner fa-pulse fa-1x fa-fw"></i> Obteniendo presentaciones...');
        api('ajax/products.php', {action:'get_propres', id_product:id_product}, function(rsp){
            if(rsp.ok){
                MProduct.showPropres(id_product,rsp.propres);
            } else {

            }
        }, false);
    },

    showPropres: function(id_product,propres){
        //MProduct.$propres.fadeIn();

        MProduct.$propres.tabs.show();
        MProduct.$propres.tabs.html('');
        MProduct.$propres.items.html('');
        MProduct.mkPropre(id_product);

        if(typeof propres != 'undefined'){
            console.log('propres:',propres);
            propres.forEach(function(o){
                MProduct.mkPropre(id_product,o);
            });
        }

        MProduct.accSupply.init();
    },

    savePropres: function(){
        var $form = $(this).parent().parent('form');
        var data = $form.serializeObject();

        api('ajax/products.php', data, function(rsp){
            if(rsp.ok){
                toastr.success('Guardado correctamente');
                //MProduct.getPropres()
                if(typeof rsp.propres != 'undefined'){
                    MProduct.showPropres(data.id_product, rsp.propres);
                }
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Guardando...');
    },
    
    removePropres: function(){
        var $form = $(this).parent().parent('form'),
            data = $form.serializeObject();

        bootbox.confirm('¿Seguro que quieres eliminarlo?', function(result){
            if(!result) return;
            api('ajax/products.php', {action:'remove_propre', id:data.id, id_product:data.id_product}, function(rsp){
                if(rsp.ok){
                    toastr.success('Eliminado correctamente...');
                    if(typeof rsp.propres != 'undefined'){
                        MProduct.showPropres(data.id_product, rsp.propres)
                    }
                } else {
                    bootbox.alert(rsp.msg);
                }
            }, 'Eliminando...');
        });
    },

    removeSupply: function(){
        var $this = $(this),
            $tr = $this.parent().parent('tr'),
            id = $this.data('id');
        if(id == '' || id == '0'){
            $tr.remove();
        } else {
            bootbox.confirm('¿Seguro que quieres eliminarlo?', function(result){
                if(!result) return;
                api('ajax/products.php', {action:'remove_propresup', id:id}, function(rsp){
                    if(rsp.ok){
                        toastr.success('Eliminado correctamente...');
                        $tr.remove();
                    } else {
                        bootbox.alert(rsp.msg);
                    }
                }, 'Eliminando...');
            });
        }
    },

    mkPropre: function(id_product,o){

        var isEdit = (typeof o != 'undefined' && o != null),

            tit_tab     = isEdit ? o.name : '<i class="fa fa-plus"></i>',
        
            id              = isEdit ? o.id             : 0,
            name            = isEdit ? o.name           : '',
            cost            = isEdit ? o.cost           : '',
            price           = isEdit ? o.price          : '',
            points          = isEdit ? o.points         : '',
            commission      = isEdit ? o.commission     : '',
            in_deli         = isEdit ? o.in_deli        : 1,
            has_stock       = isEdit ? o.has_stock      : 0,
            has_discounts   = isEdit ? o.has_discounts  : 1,
            has_supply      = isEdit ? o.has_supply     : 0,
            stocks          = isEdit ? o.stocks         : storages,
            supplies        = isEdit ? o.supplies       : [];

        if(o==null){

        } else {

        }

        var tab = '',
            html = '';


        tab += '<li '+(!isEdit?'class="active"':'')+'><a href="#tab_1_'+ id +'" data-toggle="tab"> '+ tit_tab +' </a></li>';

        html += '<div class="tab-pane fade '+(!isEdit?'active in':'')+'" id="tab_1_'+ id +'">';

        html += '    <form class="form-horizontal">';
        html += '        <input type="hidden" name="action" value="add_propre">';
        html += '        <input type="hidden" name="id" value="'+ id +'">';
        html += '        <input type="hidden" name="id_product" value="'+ id_product +'">';

        html += '        <div class="form-group">';
        html += '            <label class="col-md-3 control-label">Nombre</label>';
        html += '            <div class="col-md-9">';
        html += '                <input type="text" class="form-control" name="name" value="'+ name +'" placeholder="Nombre de la presentación">';
        html += '            </div>';
        html += '        </div>';

        html += '        <div class="form-group">';
        html += '            <label class="col-md-3 control-label">Costo</label>';
        html += '            <div class="col-md-9">';
        html += '                <input type="number" class="form-control" name="cost" value="'+ cost +'" placeholder="Costo de producción">';
        html += '            </div>';
        html += '        </div>';

        html += '        <div class="form-group">';
        html += '            <label class="col-md-3 control-label">Precio</label>';
        html += '            <div class="col-md-9">';
        html += '                <input type="number" class="form-control" name="price" value="'+ price +'" placeholder="Precio de venta">';
        html += '            </div>';
        html += '        </div>';

        html += '        <div class="form-group">';
        html += '            <label class="col-md-3 control-label">Puntos</label>';
        html += '            <div class="col-md-9">';
        html += '                <input type="number" class="form-control" name="points" value="'+ points +'" placeholder="Puntos para este producto">';
        html += '            </div>';
        html += '        </div>';

        html += '        <div class="form-group">';
        html += '            <label class="col-md-3 control-label">Comisión</label>';
        html += '            <div class="col-md-9">';
        html += '                <input type="number" class="form-control" name="commision" value="'+ commission +'" placeholder="Comisión para el mozo">';
        html += '            </div>';
        html += '        </div>';

        html += '        <div class="form-group">';
        html += '            <label class="col-md-3 control-label"></label>';
        html += '            <div class="col-md-9">';
        html += '                <label class="block">';
        html += '                    <input type="checkbox" name="in_deli" '+(in_deli==1?'checked':'')+'> Mostrar en deliverys';
        html += '                </label>';
        html += '                <label class="hide">';
        html += '                    <input type="checkbox" name="has_stock" '+(has_stock==1?'checked':'')+'> Lleva control de stock';
        html += '                </label>';
        html += '                <label class="block">';
        html += '                    <input type="checkbox" name="has_discounts" '+(has_discounts==1?'checked':'')+'> Aplica descuentos';
        html += '                </label>';
        html += '                <label class="block">';
        html += '                    <input type="checkbox" name="has_supply" '+(has_supply==1?'checked':'')+'> Lleva ingredientes';
        html += '                </label>';
        html += '            </div>';
        html += '        </div>';

        html += '        <div class="stocks hide" '+(has_stock==1?'':'style="display:none"')+'>';
        stocks.forEach(function(s){
            console.log(s);
            html += '<div class="form-group">';
            html += ' <label class="col-md-6 control-label">';
            html += '  <span class="label label-primary"> '+s.name+' </span>';
            html += ' </label>';
            html += ' <div class="col-md-6">';
            html += '  <input type="number" class="form-control" name="storage_'+s.id+'" value="'+s.stock+'">';
            html += ' </div>';
            html += '</div>';
        });
        html += '        </div>';

        html += '        <div class="form-group supplies" '+(has_supply==1?'':'style="display:none"')+'>';
        html += '            <div class="col-md-12">';
        html += '                <table class="table table-bordered" style="margin-bottom:0">';
        html += '                    <thead>';
        html += '                    <tr>';
        html += '                        <td colspan="4">';
        html += '                            <div class="input-icon">';
        html += '                                <i class="fa fa-search"></i>';
        html += '                                <input type="text" name="query" class="form-control" placeholder="Buscar insumos...">';
        html += '                            </div>';
        html += '                        </td>';
        html += '                    </tr>';
        html += '                    <tr>';
        html += '                        <th>Prodcto</th>';
        html += '                        <th width="90px">Cantidad</th>';
        html += '                        <th>UM</th>';
        html += '                        <th width="1%"></th>';
        html += '                    </tr>';
        html += '                    </thead>';
        html += '                    <tbody>';

        supplies.forEach(function(s){
            html += MProduct.mkSupplyTR(s);
        });

        html += '                    </tbody>';
        html += '                </table>';
        html += '            </div>';
        html += '        </div>';
        html += '';
        html += '        <div class="modal-footer">';
        if(isEdit) html += ' <button type="button" class="btn red remove pull-left">Eliminar</button>';
        //html += '            <button type="button" class="btn default cancel" data-dismiss="modal">Cancelar</button>';
        html += '            <button type="button" class="btn green save">Guardar</button>';
        html += '        </div>';
        html += '    </form>';
        html += '';
        html += '</div>';

        MProduct.$propres.tabs.append(tab);
        MProduct.$propres.items.append(html);
    },

    mkSupplyTR: function(s){
        var html ='';
        html += '<tr>';
        html += '<input type="hidden" name="supplies[]" value="'+ s.id +'">';
        html += '    <td>'+ s.name +'</td>';
        html += '    <td><input type="text" name="quantities[]" value="'+ (s.quantity||0) +'" class="form-control"></td>';
        html += '    <td>';
        html += '        <select class="form-control" name="id_unimeds[]">';
        html += '        <option value="">Elegir...</option>';

        unimeds.forEach(function(o){
            var id_unimed = (s.id_unimed||0);
            console.log('o.id',o.id);
            console.log('id_unimed',id_unimed);
            html += '<option value="'+o.id+'" '+(o.id==id_unimed?'selected':'')+'>'+o.name+'</option>';
        });

        html += '        </select>';
        html += '    </td>';
        html += '    <td style="vertical-align:middle">';
        html += '        <button type="button" class="close remove_supply" style="padding:10px" data-id="'+ (s.id_propresup||0) +'"></button>';
        html += '    </td>';
        html += '</tr>';

        return html;
    },

    unimedOptions: null,
    getUnimedsOptions: function(id_unimed, callback){
        api('get_unimeds', function(rsp){
            var html = '<option value="">Elegir...</option>';
            rsp.unimeds.forEach(function(o){
                html += '<option value="'+o.id+'" '+(o.id==id_unimed?'selected':'')+'>'+o.name+'</option>';
            });
            MProduct.unimedOptions = html;
            callback();
        }, false, true);
    },

    hasStockChanged: function(){
        var $box = $(this).closest('.tab-pane');

        if(this.checked){
            $box.find('.stocks').show();
        } else {
            $box.find('.stocks').hide();
        }

    },

    hasSupplyChanged: function(){

        var $box = $(this).closest('.tab-pane');

        if(this.checked){
            $box.find('.supplies').show();
        } else {
            $box.find('.supplies').hide();
        }
    },

    // Autocompletado de ingredientes
    accSupply: {

        init: function(){
            var $ipts = $('input[name=query]', MProduct.$propres.items);
            $ipts.autocomplete({
                //source: "ajax/autocomplete-clients.php",
                source: function(data, response){
                    data.action = 'autocomplete';
                    api('ajax/supplies.php', data, response, false, true);
                },
                minLength: 1,
                select: function(event, ui){
                    //AClient.assign(ui.item);

                    var html = MProduct.mkSupplyTR(ui.item);

                    $(this).closest('.supplies').find('tbody').append(html);

                    return false;
                },

                create: function () {
                    $(this).data('ui-autocomplete')._renderItem = function(ul, item){
                        return $("<li>").append('<a>' + item.name + '</a>').appendTo(ul);
                    }
                }
            });
            //$ipts.keypress();
            //$ipts.keyup(function(e){ if(e.keyCode == 8) AClient.clean(); });

        }

    }

};

function mkMenuSelect(data,level,id_active){
    var html = '';
    data.forEach(function(o){
        var name = '&nbsp;&nbsp;'.repeat(level)+o.name;
        if(o.sub.length == 0){
            html += '<option value="'+o.id+'" '+(o.id==id_active?'selected':'')+'>'+name+'</option>';
        } else { //TODO: cambiar DISABLES y value
            html += '<option value="'+o.id+'" style="color:black;background:#eee" '+(o.id==id_active?'selected':'')+'>'+name+'</option>';
            html += mkMenuSelect(o.sub, level+1, id_active);
        }
    });
    return html;
}