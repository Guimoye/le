var AcsModalForm = function(acs){

    acs.title       = acs.title || '---';
    acs.size        = acs.size  || 'modal-md';

    acs.btn_remove  = acs.btn_remove    || 'Eliminar';
    acs.btn_cancel  = acs.btn_cancel    || 'Cancelar';
    acs.btn_save    = acs.btn_save      || 'Guardar';

    acs.$form = null;

    acs._init = function(html_body){

        acs.$form = $(
            '<form class="modal fade modal-scroll form-horizontal" data-backdrop="static" data-keyboard="false">' +
            '    <div class="modal-dialog '+acs.size+'">' +
            '        <div class="modal-content">' +
            '            <div class="modal-header">' +
            '                <button type="button" class="close" data-dismiss="modal"></button>' +
            '                <h4 class="modal-title">'+acs.title+'</h4>' +
            '            </div>' +
            '            <div class="modal-body"></div>' +
            '            <div class="modal-footer">' +
            '                <button type="button" class="btn red pull-left remove" style="display:none">'+acs.btn_remove+'</button>' +
            '                <button type="button" data-dismiss="modal" class="btn btn-outline btn-default cancel">'+acs.btn_cancel+'</button>' +
            '                <button class="btn blue save">'+acs.btn_save+'</button>' +
            '            </div>' +
            '        </div>' +
            '    </div>' +
            '</form>'
        );

        acs.$form.modal_body    = $('.modal-body', acs.$form);
        acs.$form.btn_remove    = $('.remove', acs.$form);
        acs.$form.btn_cancel    = $('.cancel', acs.$form);
        acs.$form.btn_save      = $('.save', acs.$form);

        acs.$form.submit(function(e){
            e.preventDefault();
            if(typeof acs.onSubmit === 'function') acs.onSubmit();
        });

        acs.$form.btn_remove.click(function() {
            if(typeof acs.onRemove === 'function') acs.onRemove();
        });

        acs.$form.on('shown.bs.modal', function() {
            if(typeof acs.onShown === 'function') acs.onShown();
        });

        acs.$form.on('hidden.bs.modal', function() {
            if(typeof acs.onHidden === 'function') acs.onHidden();
        });

        $body.append(acs.$form);

        if(typeof html_body == 'string'){
            acs.addHtmlInputs(html_body);
        } else if(arguments.length > 0) {
            acs.addInputs.apply(this, arguments);
        }
    };

    acs.addHtmlInputs = function(html){
        acs.$form.modal_body.append(html);
        acs.$form.find('input').each(function(){
            acs.$form[this.name] = $(this);
        });
    };

    // Agregar inputs
    acs.addInputs = function(inputs){

        var html = '';

        for(var i = 0; i < arguments.length; i++){
            var options = arguments[i];
            var io = $.extend({
                title: '',
                type: 'text',
                name: 'acs_unnamed',
                value: '',
                class_name: ''
            }, options);

            switch(io.type){
                case 'select':
                    html += '<div class="form-group">';
                    html += ' <label class="col-md-4 control-label">'+io.title+'</label>';
                    html += ' <div class="col-md-6">';
                    html += '  <select class="form-control '+io.class_name+'" name="'+io.name+'">';
                    html += '   '+io.value;
                    html += '  </select>';
                    html += ' </div>';
                    html += '</div>';
                    break;
                case 'checkbox':
                    html += '<div class="form-group">';
                    html += ' <label class="col-md-4 control-label"></label>';
                    html += ' <div class="col-md-6">';
                    html += '  <label>';
                    html += '   <input type="'+io.type+'" name="'+io.name+'" value="'+io.value+'" class="'+io.class_name+'"> '+io.title;
                    html += '  </label>';
                    html += ' </div>';
                    html += '</div>';
                    break;
                case 'hidden':
                    html += '<input type="'+io.type+'" name="'+io.name+'" value="'+io.value+'">';
                    break;
                default:
                    html += '<div class="form-group">';
                    html += ' <label class="col-md-4 control-label">'+io.title+'</label>';
                    html += ' <div class="col-md-6">';
                    html += '  <input type="'+io.type+'" class="form-control '+io.class_name+'" name="'+io.name+'" value="'+io.value+'" placeholder="'+io.title+'">';
                    html += ' </div>';
                    html += '</div>';
                    break;
            }

            console.log('addinputs: ', io);
        }

        acs.addHtmlInputs(html);
    };

    acs.show = function(){
        acs.$form.modal('show');
    };

    acs.hide = function(){
        acs.$form.modal('hide');
    };

    return acs;
};

var MTest = new AcsModalForm({
    init: function(){
        if(this.$form != null){
            this.show();
            return;
        }
        this._init(
            { type: 'hidden',   name: 'id'},
            { type: 'text',     name: 'name',       title: 'Holas', value: '' },
            { type: 'checkbox', name: 'hola2',      title: 'Guardar en el estado'},
            { type: 'select',   name: 'country',    title: 'Pais', value: '<option>Cargando...</option>'}
        );
        this.show();
    },

    add: function(){
        MTest.init();
        MTest.$form.name.val('Alvaro');
    },

    onShown: function(){
        this.$form.name.focus();
    },

    onHidden: function(){

    },

    onSubmit: function(){

    },

    onRemove: function(){
        alert('onRemove...');
    }
});

$(document).ready(function(){
    console.log(MTest);

    MTest.add();
});