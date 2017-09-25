// Unidades de medida
var MRoom = {

    tit_add: 'Agregar sala',
    tit_edit: 'Editar sala',

    $modal: null,
    $form: null,

    init: function(){
        this.$modal = $('#modal_add_room');
        this.$modal.title = $('.modal-title', this.$modal);
        this.$modal.remove = $('.remove', this.$modal);
        this.$form          =    $('form', this.$modal);
        this.$form.id           = $('input[name=id]', this.$form);
        this.$form.name         = $('input[name=name]', this.$form);
        this.$form.description  = $('textarea[name=description]', this.$form);

        // Actions
        this.$modal.remove.click(this.remove);
        $('.save', this.$modal).click(this.save);
        this.$form.submit(function(e){
            e.preventDefault();
            MRoom.save();
        });
        this.$modal.on('shown.bs.modal', function() {
            MRoom.$form.name.focus();
        });

        $('.room').on('shown.bs.collapse', function(){
            Cookies.set('id_room', $(this).data('id'));
        });

    },

    add: function(){
        MRoom.$form.id.val('');
        MRoom.$form.name.val('');
        MRoom.$form.description.val('');;
        MRoom.$modal.title.html(MRoom.tit_add);
        MRoom.$modal.remove.hide();
        MRoom.$modal.modal('show');
    },

    edit: function(o){
        MRoom.$form.id.val(o.id);
        MRoom.$form.name.val(o.name);
        MRoom.$form.description.val(o.description);
        MRoom.$modal.title.html(MRoom.tit_edit);
        MRoom.$modal.remove.show();
        MRoom.$modal.modal('show');
    },

    save: function(){
        api('ajax/rooms.php', MRoom.$form.serializeObject(), function(rsp){
            if(rsp.ok){
                Cookies.set('id_room',rsp.id);

                toastr.success('Guardado correctamente');
                MRoom.$modal.modal('hide');
                location.reload();
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Guardando...');
    },

    remove: function(){
        bootbox.confirm('¿Seguro que quieres eliminarlo?', function(result){
            if(!result) return;
            api('ajax/rooms.php', {action:'remove', id:MRoom.$form.id.val()}, function(rsp){
                if(rsp.ok){
                    toastr.success('Eliminado correctamente...');
                    MRoom.$modal.modal('hide');
                    location.reload();
                } else {
                    bootbox.alert(rsp.msg);
                }
            }, 'Eliminando...');
        });
    },

    /**
     * Mesas
     */
    addTable: function(id_room){
        var $box = $('#room_'+id_room),
            $add = $box.find('.add');

        var data = {
            action:'add_table',
            id_room: id_room,
            name:''
        };

        api('ajax/rooms.php', data, function(rsp){
            if(rsp.ok){
                $add.before('<div class="item"><span>'+rsp.name+'</span></div>');
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Creando mesa...');
        
    },
    
    editTable: function(id,name){
        bootbox.prompt({
            title: 'Editar mesa',
            placeholder: 'Nombre...',
            value: name,
            size: 'small',
            buttons: {
                confirm: { label: 'Guardar' },
                cancel: { label: 'Cancelar' }
            },
            callback: function(content){
                if(content==null)return;
                api('ajax/rooms.php', {action:'edit_table',id:id,name:content}, function(rsp){
                    if(rsp.ok){
                        
                    } else {
                        bootbox.alert(rsp.msg, function(){
                            MRoom.editTable(id,name);
                        });
                    }
                }, 'Guardando...');
            }
        });
    }

};

// Table
var MTable = {

    tit_add: 'Agregar mesa',
    tit_edit: 'Editar mesa',

    $modal: null,
    $form: null,

    init: function(){
        this.$modal = $('#modal_add_table');
        this.$modal.title = $('.modal-title', this.$modal);
        this.$modal.remove = $('.remove', this.$modal);
        this.$form          = $('form', this.$modal);
        this.$form.id       = $('input[name=id]', this.$form);
        this.$form.id_room  = $('input[name=id_room]', this.$form);
        this.$form.name     = $('input[name=name]', this.$form);

        // Actions
        this.$modal.remove.click(this.remove);
        $('.save', this.$modal).click(this.save);
        this.$form.submit(function(e){
            e.preventDefault();
            MTable.save();
        });
        this.$modal.on('shown.bs.modal', function() {
            MTable.$form.name.focus();
        });
    },

    add: function(id_room){
        MTable.$form.id.val('');
        MTable.$form.id_room.val(id_room);
        MTable.$form.name.val('');
        MTable.$modal.title.html(MTable.tit_add);
        MTable.$modal.remove.hide();
        MTable.$modal.modal('show');
    },

    edit: function(o){
        MTable.$form.id.val(o.id);
        MTable.$form.id_room.val(o.id_room);
        MTable.$form.name.val(o.name);
        MTable.$modal.title.html(MTable.tit_edit);
        MTable.$modal.remove.show();
        MTable.$modal.modal('show');
    },

    save: function(){
        var data = MTable.$form.serializeObject();

        var $box = $('#room_'+data.id_room),
            $add = $box.find('.add');
        
        api('ajax/rooms.php', data, function(rsp){
            if(rsp.ok){
                toastr.success('Guardado correctamente');
                MTable.$modal.modal('hide');

                location.reload();
                /*if(rsp.isEdit){
                    location.reload();
                } else {
                    $add.before('<div class="item" id="table_'+rsp.table.id+'"><span>'+rsp.table.name+'</span></div>');
                }*/
                
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Guardando...');
    },

    remove: function(){
        bootbox.confirm('¿Seguro que quieres eliminarlo?', function(result){
            if(!result) return;
            api('ajax/rooms.php', {action:'remove_table', id:MTable.$form.id.val()}, function(rsp){
                if(rsp.ok){
                    toastr.success('Eliminado correctamente...');
                    MTable.$modal.modal('hide');
                    location.reload();
                } else {
                    bootbox.alert(rsp.msg);
                }
            }, 'Eliminando...');
        });
    }

};