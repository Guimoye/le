var Print = {

    init: function(){

    },

    print: function(data){
        Loading.show('Printing...');
        $.ajax({
            url: this.getUrlPrint(),
            data: data,
            type: 'POST',
            dataType: 'json',
            success: function(rsp){
                Loading.hide();
                console.log('success:',rsp);
                if(rsp.ok){
                    toastr.success('Impreso correctamente');
                } else {
                    bootbox.alert(rsp.msg);
                }
            },
            error: function(r){
                Loading.hide();
                bootbox.alert('Se produjo un error');
            }
        });
    },

    precuenta: function(id_order,total,ordpros){
        var data = {
            action: 'precuenta',
            id_order: id_order,
            total: total,
            ordpros: ordpros
        };
        api('ajax/print.php', data, function(rsp){
            if(rsp.ok){
                Print.print(rsp.data);
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Obteniendo datos de impresión...');
    },

    transaction: function(id_transaction){
        var data = {
            action: 'transaction',
            id_transaction: id_transaction
        };
        api('ajax/print.php', data, function(rsp){
            if(rsp.ok){
                Print.print(rsp.data);
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Obteniendo datos de impresión...');
    },

    // TODO: Test
    test: function(printer_ip,printer_name,printer_serial,printer_line_letters){
        var data = {
            type: 'precuenta',
            coin_sign: 'S/',
            coin_name: 'Soles',
            printer_ip: printer_ip,
            printer_name: printer_name,
            printer_serial: printer_serial,
            line_letters: printer_line_letters,
            comp_name: 'Company S.A.C',
            comp_ruc: '00000000000001',
            branch: 'Central',
            address: 'Address, District, City',
            phone: '(--) 000-0000',
            box: '000 - Name',
            table: 'Room - 000',
            waiter: 'Waiter Name',
            num_service: 'TEST-0000000001',
            num_voucher: 'TEST-1000000000',
            price_base: 1,
            price_igv: 0.5,
            price_total: 1.5,
            items: [
                { name: 'Test Item 001', value: 0.0 },
                { name: 'Test Item 002', value: 0.0 }
            ]
        };
        this.print(data);
    },


    getUrlPrint: function(){
        return 'http://'+ stg.ip_local_server +'/EasyRestMini/print/print.php';
    }

};


$(document).ready(function(){

    //Print.init();

});