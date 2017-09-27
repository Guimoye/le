var isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

toastr.options.timeOut = 2000;


// Calcular el camaño de un objeto
function objSize(obj){
	var count = 0;
	for(var prop in obj){
		count++;
	}
	return count;
}

// Saber si una variable es numero
function isNumber(n){
	return !isNaN(parseFloat(n)) && isFinite(n);
}

/*
 Loading
 */
var Loading = {
	$capa: null,

	show: function(msg){
		$('#loading').remove();
		var html = '<div id="loading">';
		html += '<div class="_body">';
		html += '<i class="fa fa-spinner fa-pulse fa-3x fa-f"></i>';
		html += '<div class="_message">'+(typeof msg == 'undefined' ? 'Cargando...' : msg )+'</div>';
		html += '</div>';
		html += '</div>';

		$('body').append(html);
	},

	hide: function(){
		$('#loading').fadeOut(200);
	}
};

/**
 * Calcular distancia entre dos Puntos
 * @return: (int) KM´s
 */
function calculateDistance(lat1, lon1, lat2, lon2) {
	var R = 6371; // km
	var dLat = toRad(lat2-lat1);
	var dLon = toRad(lon2-lon1);
	var lat1 = toRad(lat1);
	var lat2 = toRad(lat2);

	var a = Math.sin(dLat/2) * Math.sin(dLat/2) +
		Math.sin(dLon/2) * Math.sin(dLon/2) * Math.cos(lat1) * Math.cos(lat2);
	var c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
	var d = R * c;
	return d;
}

/**
 * Serialzie form to Object
 */
$.fn.serializeObject = function(){
	var o = {};
	var a = this.serializeArray();
	$.each(a, function() {
		if (o[this.name]) {
			if (!o[this.name].push) {
				o[this.name] = [o[this.name]];
			}
			o[this.name].push(this.value || '');
		} else {
			o[this.name] = this.value || '';
		}
	});
	return o;
};

/**
 * Cache Local
 */
var Cache = {
	data: {},
	remove: function (url) {
		delete Cache.data[url];
	},
	exist: function (url) {
		return Cache.data.hasOwnProperty(url) && Cache.data[url] !== null;
	},
	get: function (url) {
		return Cache.data[url];
	},
	set: function (url, cachedData){
		Cache.remove(url);
		Cache.data[url] = cachedData;
	}
};

/**
 * Api general
 * @param api :: Funcion de api
 * @param data :: parametros
 * @param callback :: respuesta, dejar null de no llamar
 * @param loading :: cargando, puede ser false para no mostrar, por defecto muestra, puede ser texto 'Car...'
 */
function api(api,data,callback,loading,cache){
	if(typeof data == 'function'){
		cache = loading;
		loading = callback;
		callback = data;
		data = {};
	}

	var isCustomUrl = api.indexOf('.') !== -1; // Saber si es url personalizada

	if(!isCustomUrl){
		data.api = api;
	}

	cache = !(typeof cache == 'undefined') && cache;

	var url = (isCustomUrl ? api : 'ajax/index.php') + '?' + $.param(data);


	if(cache && Cache.exist(url)){
		console.warn("API:cache: ", url);
		if(loading) Loading.hide();
		if(callback) callback(Cache.get(url));
	} else {
        console.log('API:url: ',url);
		if(typeof loading == 'undefined'){
			loading = true;
			Loading.show();
		} else if(loading){
			Loading.show(loading);
		}
		$.post(url, data, function(rsp){
			Cache.set(url,rsp);

			if(loading) Loading.hide();
			if(callback) callback(rsp);

			//console.log(this);
		}, 'JSON');
	}
}

// Cargar JS dinamicamente
function js(url){
    var script_tag = document.createElement('script');
    script_tag.setAttribute("type", "text/javascript");
    script_tag.setAttribute("src", url);
    (document.getElementsByTagName("head")[0] || document.documentElement).appendChild(script_tag);
}

var $body;

$(document).ready(function(){
	$body = $('body');
	$body.ofl_bar = $('.ofl_bar');
	$body.head_fullscreen = $('#head_fullscreen');

	if(typeof $Ready === 'function') $Ready();

    menuActive($('.page-sidebar-menu li.active'));

    if(isMobile){
        $body.head_fullscreen.hide();
    } else {
        $body.head_fullscreen.show();
    }
    //CGBranch.open(0);

    FOffline.changedConnection();
});

function fullScreen(){
    var el = document.documentElement,
        rfs = el.requestFullscreen
            || el.webkitRequestFullScreen
            || el.mozRequestFullScreen
            || el.msRequestFullscreen;
    rfs.call(el);
}

function toggleFullScreen(){
    var isInFullScreen = (document.fullscreenElement && document.fullscreenElement !== null) ||
        (document.webkitFullscreenElement && document.webkitFullscreenElement !== null) ||
        (document.mozFullScreenElement && document.mozFullScreenElement !== null) ||
        (document.msFullscreenElement && document.msFullscreenElement !== null);

    var docElm = document.documentElement;
    if (!isInFullScreen) {
        if (docElm.requestFullscreen) {
            docElm.requestFullscreen();
        } else if (docElm.mozRequestFullScreen) {
            docElm.mozRequestFullScreen();
        } else if (docElm.webkitRequestFullScreen) {
            docElm.webkitRequestFullScreen();
        } else if (docElm.msRequestFullscreen) {
            docElm.msRequestFullscreen();
        }
    } else {
        if (document.exitFullscreen) {
            document.exitFullscreen();
        } else if (document.webkitExitFullscreen) {
            document.webkitExitFullscreen();
        } else if (document.mozCancelFullScreen) {
            document.mozCancelFullScreen();
        } else if (document.msExitFullscreen) {
            document.msExitFullscreen();
        }
    }
}

function menuActive($box){
    if($box.length > 0){
        var $b = $box.parent().parent('li');
        $b.addClass('active');
        menuActive($b);
    }
}

/*function toFixed(num){
    var n = Number(num);
    return n.toFixed(2);
}*/
// Convertir cualquier cosa a numero
function num(str, decimals, defult){
    var n = Number(str),
        df = (typeof defult === 'number') ? defult : 0;
    n = isNaN(n) || n==0 ? df : n;
    return (typeof decimals === 'number' && decimals>=0) ? n.toFixed(decimals) : n;
}

function pad(num, size) {
    var s = num+"";
    while (s.length < size) s = "0" + s;
    return s;
}

// Cambiar sucursal
var CGBranch = {

    $modal: null,
    $form: null,

    initViews: function(){
        if(this.$modal != null) return;
        $body.append(
            '<div id="modal_cgbranch" class="modal fade modal-scroll" data-backdrop="static" data-keyboard="false">'+
            ' <div class="modal-dialog" style="max-width:400px">'+
            '  <div class="modal-content">'+
            '   <div class="modal-header">'+
            '    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>'+
            '    <h4 class="modal-title">Cambio de sucursal</h4>'+
            '   </div>'+
            '   <div class="modal-body">'+
            '    <form class="form-horizontal">'+
            '     <input type="hidden" name="action" value="add">'+
            '     <table class="table table-bordered" style="margin:0"></table>'+
            '    </form>'+
            '   </div>'+
            '   <div class="modal-footer">'+
            '    <button type="button" data-dismiss="modal" class="btn cancel">Cancelar</button>'+
            '    <button type="button" class="btn blue save">Guardar</button>'+
            '   </div>'+
            '  </div>'+
            ' </div>'+
            '</div>'
        );

        this.$modal = $('#modal_cgbranch');
        this.$form = $('form', this.$modal);
        this.$form.list = $('table', this.$form);

        // Eventos
        $('.save', this.$modal).click(this.save);

    },

    open: function(id_branch){
        CGBranch.initViews();
        api('get_branches', function(rsp){
            var html = '';
            rsp.branches.forEach(function(o){
                html += '<tr>';
                html += ' <td width="1%">';
                html += '  <input type="radio" class="icheck" name="id_branch" value="'+o.id+'" '+(o.id==id_branch?'checked':'')+'>';
                html += ' </td>';
                html += ' <td> '+o.name+' </td>';
                html += '</tr>';
            });
            CGBranch.$form.list.html(html);
            CGBranch.$modal.modal('show');

        }, 'Obteniendo sucursales...', true);
    },

    save: function(){
        var id_branch = $('input[name=id_branch]:checked', CGBranch.$form).val() || 0;
        api('set_local_branch', {id_branch:id_branch}, function(rsp){
            if(rsp.ok){
                toastr.success('Guardado correctamente');
                CGBranch.$modal.modal('hide');
                location.reload();
            } else {
                bootbox.alert(rsp.msg);
            }
        }, 'Guardando...');
    }

};

// Mostrar Overlay negro cada que se abren multiples modals
$(document).on('show.bs.modal', '.modal', function (event){
    var zIndex = 10050 + (10 * $('.modal:visible').length);
    $(this).css('z-index', zIndex);
    setTimeout(function(){
        $('.modal-backdrop').not('.modal-stack').css('z-index', zIndex - 1).addClass('modal-stack');
    }, 0);
});


// manejador de offline
var FOffline = {

    changedConnection: function(){
        console.log('FOffline.changedConnection: ', navigator.onLine);

        if(navigator.onLine){
            $body.ofl_bar.slideUp();
        } else {
            $body.ofl_bar.slideDown();
        }
    }

};

window.addEventListener("online", FOffline.changedConnection);
window.addEventListener("offline", FOffline.changedConnection);
