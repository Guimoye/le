// Modal Driver
var MDriver = {

    callback: null,

    title_add:  'Registrar conductor',
    title_edit: 'Editar conductor',

    $modal: null,
    $title: null, // Modal: Titulo
    $form: null, // Modal: Formulario

    $remove: null,

    init: function(callback){
        this.callback = (typeof callback === 'function') ? callback : null;

        this.$modal         = $('#modal_add_driver');
        this.$modal.title   = $('.modal-title', this.$modal);
        this.$modal.remove  = $('.remove', this.$modal);

        this.$form                      = $('form', this.$modal);
        this.$form.id                   = $('input[name="id"]', this.$form);
        this.$form.name 			    = $('input[name="name"]', this.$form);
        this.$form.surname 			    = $('input[name="surname"]', this.$form);
        this.$form.date_birth 		    = $('input[name="date_birth"]', this.$form);
        this.$form.dni 			        = $('input[name="dni"]', this.$form);
        this.$form.ruc 			        = $('input[name="ruc"]', this.$form);
        this.$form.driver_licence 	    = $('input[name="driver_licence"]', this.$form);
        this.$form.city 			    = $('input[name="city"]', this.$form);
        this.$form.district 			= $('input[name="district"]', this.$form);
        this.$form.phone_cell 		    = $('input[name="phone_cell"]', this.$form);
        this.$form.phone_house 		    = $('input[name="phone_house"]', this.$form);
        this.$form.email 			    = $('input[name="email"]', this.$form);
        this.$form.civil_status 		= $('select[name="civil_status"]', this.$form);
        this.$form.wife_name 			= $('input[name="wife_name"]', this.$form);
        this.$form.wife_dni 			= $('input[name="wife_dni"]', this.$form);
        this.$form.bank_name 			= $('input[name="bank_name"]', this.$form);
        this.$form.bank_account 		= $('input[name="bank_account"]', this.$form);

        this.$form.gt_name 			    = $('input[name="gt_name"]', this.$form);
        this.$form.gt_dni 			    = $('input[name="gt_dni"]', this.$form);
        this.$form.gt_city 		        = $('input[name="gt_city"]', this.$form);
        this.$form.gt_district 		    = $('input[name="gt_district"]', this.$form);
        this.$form.gt_address 		    = $('input[name="gt_address"]', this.$form);
        this.$form.gt_phone 			= $('input[name="gt_phone"]', this.$form);
        this.$form.gt_email 			= $('input[name="gt_email"]', this.$form);
        this.$form.gt_job_place 		= $('input[name="gt_job_place"]', this.$form);
        this.$form.gt_job_role 		    = $('input[name="gt_job_role"]', this.$form);
        this.$form.gt_job_address 	    = $('input[name="gt_job_address"]', this.$form);
        this.$form.gt_job_city 	        = $('input[name="gt_job_city"]', this.$form);
        this.$form.gt_job_district 	    = $('input[name="gt_job_district"]', this.$form);
        this.$form.gt_job_phone 		= $('input[name="gt_job_phone"]', this.$form);
        this.$form.gt_job_boss_name 	= $('input[name="gt_job_boss_name"]', this.$form);
        this.$form.gt_job_boss_role 	= $('input[name="gt_job_boss_role"]', this.$form);
        this.$form.gt_job_boss_email    = $('input[name="gt_job_boss_email"]', this.$form);

        this.$form.id_brand 			= $('select[name="id_brand"]', this.$form);
        this.$form.id_model 			= $('select[name="id_model"]', this.$form);
        this.$form.vh_plate 			= $('input[name="vh_plate"]', this.$form);
        this.$form.vh_year 			    = $('input[name="vh_year"]', this.$form);
        this.$form.vh_color 			= $('input[name="vh_color"]', this.$form);
        this.$form.vh_engine_number 	= $('input[name="vh_engine_number"]', this.$form);
        this.$form.vh_serial_chassis 	= $('input[name="vh_serial_chassis"]', this.$form);
        this.$form.vh_fuel 			    = $('input[name="vh_fuel"]', this.$form);
        this.$form.vh_gps_number 		= $('input[name="vh_gps_number"]', this.$form);

        // Asignar eventos
        this.$modal.remove.click(function(){
            MDriver.remove(MDriver.$id.val());
        });
        $('.save', this.$modal).click(this.save);

        this.$form.id_brand.change(function(){
            MDriver.getModelsBrand(0);
        });

    },

    getModelsBrand: function(id_model_selected){
        var id_brand = MDriver.$form.id_brand.val();
        if(id_brand == ''){
            MDriver.$form.id_model.html('<option value="">---</option>');
            MDriver.$form.id_model.attr('disabled', true);

        } else {
            MDriver.$form.id_model.html('<option value="">Cargando...</option>');
            MDriver.$form.id_model.attr('disabled', true);
            api('drivers/get_models_brand/'+id_brand, function(rsp){
                var html = '<option value="">Elegir...</option>';
                rsp.models.forEach(function(o){
                    html += '<option value="'+o.id+'" '+(o.id==id_model_selected?'selected':'')+'>'+o.name+'</option>';
                });
                MDriver.$form.id_model.html(html);
                MDriver.$form.id_model.attr('disabled', false);
            }, false, true);
        }
    },

    // Guardar
    save: function(){
        api('drivers/add', MDriver.$form.serializeObject(), function(rsp){
            if(rsp.ok == true){
                toastr.success('Guardado correctamente');
                MDriver.$modal.modal('hide');

                if(MDriver.callback == null){
                    location.reload();
                } else {
                    MDriver.callback(rsp.id, false);
                }

            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Registrando...');
    },
    
    // Abrir para nuevo NUEVO
    add: function(){
        MDriver.$modal.title.text(MDriver.title_add);
        MDriver.$modal.remove.hide();

        MDriver.$form.id.val('');
        MDriver.$form.name.val('');
        MDriver.$form.surname.val('');
        MDriver.$form.date_birth.val('');
        MDriver.$form.dni.val('');
        MDriver.$form.ruc.val('');
        MDriver.$form.driver_licence.val('');
        MDriver.$form.city.val('');
        MDriver.$form.district.val('');
        MDriver.$form.phone_cell.val('');
        MDriver.$form.phone_house.val('');
        MDriver.$form.email.val('');
        //MDriver.$form.civil_status.val('');
        MDriver.$form.wife_name.val('');
        MDriver.$form.wife_dni.val('');
        MDriver.$form.bank_name.val('');
        MDriver.$form.bank_account.val('');

        MDriver.$form.gt_name.val('');
        MDriver.$form.gt_dni.val('');
        MDriver.$form.gt_city.val('');
        MDriver.$form.gt_district.val('');
        MDriver.$form.gt_address.val('');
        MDriver.$form.gt_phone.val('');
        MDriver.$form.gt_email.val('');
        MDriver.$form.gt_job_place.val('');
        MDriver.$form.gt_job_role.val('');
        MDriver.$form.gt_job_address.val('');
        MDriver.$form.gt_job_city.val('');
        MDriver.$form.gt_job_district.val('');
        MDriver.$form.gt_job_phone.val('');
        MDriver.$form.gt_job_boss_name.val('');
        MDriver.$form.gt_job_boss_role.val('');
        MDriver.$form.gt_job_boss_email .val('');

        MDriver.$form.id_brand.val('');
        MDriver.$form.id_model.html('');
        MDriver.$form.id_model.attr('disabled',true);
        MDriver.$form.vh_plate.val('');
        MDriver.$form.vh_year.val('');
        MDriver.$form.vh_color.val('');
        MDriver.$form.vh_engine_number.val('');
        MDriver.$form.vh_serial_chassis.val('');
        MDriver.$form.vh_fuel.val('');
        MDriver.$form.vh_gps_number.val('');

        MDriver.$modal.modal('show');
    },
    
    // Editar
    edit: function(o){
        MDriver.$modal.title.text(MDriver.title_edit);
        MDriver.$modal.remove.show();

        MDriver.$form.id.val(o.id);
        MDriver.$form.name.val(o.name);
        MDriver.$form.surname.val(o.surname);
        MDriver.$form.date_birth.val(o.date_birth);
        MDriver.$form.dni.val(o.dni);
        MDriver.$form.ruc.val(o.ruc);
        MDriver.$form.driver_licence.val(o.driver_licence);
        MDriver.$form.city.val(o.city);
        MDriver.$form.district.val(o.district);
        MDriver.$form.phone_cell.val(o.phone_cell);
        MDriver.$form.phone_house.val(o.phone_house);
        MDriver.$form.email.val(o.email);
        MDriver.$form.civil_status.val(o.civil_status);
        MDriver.$form.wife_name.val(o.wife_name);
        MDriver.$form.wife_dni.val(o.wife_dni);
        MDriver.$form.bank_name.val(o.bank_name);
        MDriver.$form.bank_account.val(o.bank_account);

        MDriver.$form.gt_name.val(o.gt_name);
        MDriver.$form.gt_dni.val(o.gt_dni);
        MDriver.$form.gt_city.val(o.gt_city);
        MDriver.$form.gt_district.val(o.gt_district);
        MDriver.$form.gt_address.val(o.gt_address);
        MDriver.$form.gt_phone.val(o.gt_phone);
        MDriver.$form.gt_email.val(o.gt_email);
        MDriver.$form.gt_job_place.val(o.gt_job_place);
        MDriver.$form.gt_job_role.val(o.gt_job_role);
        MDriver.$form.gt_job_address.val(o.gt_job_address);
        MDriver.$form.gt_job_city.val(o.gt_job_city);
        MDriver.$form.gt_job_district.val(o.gt_job_district);
        MDriver.$form.gt_job_phone.val(o.gt_job_phone);
        MDriver.$form.gt_job_boss_name.val(o.gt_job_boss_name);
        MDriver.$form.gt_job_boss_role.val(o.gt_job_boss_role);
        MDriver.$form.gt_job_boss_email .val(o.gt_job_boss_email);

        MDriver.$form.id_brand.val(o.id_brand);
        MDriver.$form.id_model.val(o.id_model);
        MDriver.$form.vh_plate.val(o.vh_plate);
        MDriver.$form.vh_year.val(o.vh_year);
        MDriver.$form.vh_color.val(o.vh_color);
        MDriver.$form.vh_engine_number.val(o.vh_engine_number);
        MDriver.$form.vh_serial_chassis.val(o.vh_serial_chassis);
        MDriver.$form.vh_fuel.val(o.vh_fuel);
        MDriver.$form.vh_gps_number.val(o.vh_gps_number);

        MDriver.getModelsBrand(o.id_model);

        MDriver.$modal.modal('show');
    },

    // Eliminar
    remove: function(id){
        bootbox.confirm('¿Realmente desea eliminar?', function(result){
            if(result){
                api('drivers/remove', {action:'remove', id:id}, function(rsp){
                    if(rsp.ok == true){
                        toastr.success('Eliminado correctamente');
                        location.reload();
                    } else {
                        bootbox.alert(rsp.msg);
                    }
                }, 'Eliminando...');
            }
        });
    }
};