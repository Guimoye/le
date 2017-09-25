// Unidades de medida
var MSupply = {

    tit_add: 'Agregar insumo',
    tit_edit: 'Editar insumo',

    $modal: null,
    $form: null,

    init: function(){
        this.$modal = $('#modal_add_supply');
        this.$modal.title = $('.modal-title', this.$modal);
        this.$modal.remove = $('.remove', this.$modal);
        this.$form          = $('form', this.$modal);
        this.$form.id       = $('input[name=id]', this.$form);
        this.$form.name     = $('input[name=name]', this.$form);
        this.$form.id_unimed= $('select[name=id_unimed]', this.$form);
        this.$form.cost     = $('input[name=cost]', this.$form);
        this.$form.cost_max = $('input[name=cost_max]', this.$form);
        this.$form.cost.x   = $('.cost_x', this.$form);
        this.$form.stock_min= $('input[name=stock_min]', this.$form);
        this.$form.tipo_adq = $('select[name=tipo_adq]', this.$form);
        this.$form.storages = $('.storages', this.$form);

        // Actions
        this.$modal.remove.click(this.remove);
        $('.save', this.$modal).click(this.save);
        this.$form.submit(function(e){
            e.preventDefault();
        });
        this.$modal.on('shown.bs.modal', function() {
            MSupply.$form.name.focus();
        });
        this.$form.id_unimed.change(function(){
            if(this.value == '0' || this.value == ''){
                MSupply.$form.cost.x.html('x');
            } else {
                MSupply.$form.cost.x.html($(this).find('option:selected').text());
            }
        });
    },
    
    getUnimeds: function(id_unimed){
        MSupply.$form.id_unimed.html('<option>Cargando...</option>');
        api('get_unimeds', function(rsp){
            var html = '<option value="">Elegir...</option>';
            rsp.unimeds.forEach(function(o){
                html += '<option value="'+o.id+'" '+(o.id==id_unimed?'selected':'')+'>'+o.name+'</option>';
            });
            MSupply.$form.id_unimed.html(html);
            MSupply.$form.id_unimed.change();
        }, false, true);
    },

    getStorages: function(id_supply){
        MSupply.$form.storages.html('Cargando almacenes...');
        api('ajax/supplies.php', {action:'get_storages', id_supply:id_supply}, function(rsp){
            var html = '';
            rsp.items.forEach(function(o){
                html += '<div class="form-group">';
                html += ' <label class="col-md-5 control-label">';
                html += '  <span class="label label-primary"> '+o.name+' </span>';
                html += ' </label>';
                html += ' <div class="col-md-7">';
                html += '  <input type="number" class="form-control" name="storage_'+o.id+'" value="'+o.stock+'">';
                html += ' </div>';
                html += '</div>';
            });
            MSupply.$form.storages.html(html);
        }, false, true);
    },

    add: function(){
        MSupply.$form.id.val('');
        MSupply.$form.name.val('');
        MSupply.$form.id_unimed.val('');
        MSupply.$form.id_unimed.change();
        MSupply.$form.cost.val('');
        MSupply.$form.cost_max.val('');
        MSupply.$form.stock_min.val('');
        MSupply.$modal.title.html(MSupply.tit_add);
        MSupply.$modal.remove.hide();
        MSupply.getUnimeds(0);
        MSupply.getStorages(0);

        MSupply.$modal.modal('show');
    },

    edit: function(o){
        MSupply.$form.id.val(o.id);
        MSupply.$form.name.val(o.name);
        MSupply.$form.id_unimed.val(o.id_unimed);
        MSupply.$form.id_unimed.change();
        MSupply.$form.cost.val(o.cost);
        MSupply.$form.cost_max.val(o.cost_max);
        MSupply.$form.stock_min.val(o.stock_min);
        MSupply.$form.tipo_adq.val(o.tipo_adq);
        MSupply.$modal.title.html(MSupply.tit_edit);
        MSupply.$modal.remove.show();
        MSupply.getUnimeds(o.id_unimed);
        MSupply.getStorages(o.id);

        MSupply.$modal.modal('show');
    },

    save: function(){
        api('ajax/supplies.php', MSupply.$form.serializeObject(), function(rsp){
            if(rsp.ok){
                toastr.success('Guardado correctamente');
                MSupply.$modal.modal('hide');
                location.reload();
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Guardando...');
    },

    remove: function(){
        bootbox.confirm('¿Seguro que quieres eliminarlo?', function(result){
            if(!result) return;
            api('ajax/supplies.php', {action:'remove', id:MSupply.$form.id.val()}, function(rsp){
                if(rsp.ok){
                    toastr.success('Eliminado correctamente...');
                    MSupply.$modal.modal('hide');
                    location.reload();
                } else {
                    bootbox.alert(rsp.msg);
                }
            }, 'Eliminando...');
        });
    }

};