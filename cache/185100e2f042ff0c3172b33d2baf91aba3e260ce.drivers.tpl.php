<?php
/* Smarty version 3.1.30, created on 2017-04-13 11:04:28
  from "C:\xampp\htdocs\Liner\cms\templates\drivers.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.30',
  'unifunc' => 'content_58efa18c906189_43367579',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '76676462768b988ba8ffc2389a0d8acfbe905ee5' => 
    array (
      0 => 'C:\\xampp\\htdocs\\Liner\\cms\\templates\\drivers.tpl',
      1 => 1492099466,
      2 => 'file',
    ),
    'e28ef656dab4ae312e2ca7969d39695416d4201d' => 
    array (
      0 => 'C:\\xampp\\htdocs\\Liner\\cms\\templates\\_header.tpl',
      1 => 1492090416,
      2 => 'file',
    ),
    '0a9d55163a667c48d9bcd06f8e31273da2f7479d' => 
    array (
      0 => 'C:\\xampp\\htdocs\\Liner\\cms\\templates\\_footer.tpl',
      1 => 1492064227,
      2 => 'file',
    ),
  ),
  'cache_lifetime' => 3600,
),true)) {
function content_58efa18c906189_43367579 (Smarty_Internal_Template $_smarty_tpl) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>Conductores | Liner</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />

    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet"/>
    <link href="assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet"/>
    <link href="assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet"/>
    <link href="assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="assets/global/plugins/uniform/css/uniform.default.css" rel="stylesheet"/>
    <link href="assets/global/plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet"/>

    <link href="assets/global/plugins/bootstrap-toastr/toastr.min.css" rel="stylesheet" type="text/css" />

    <link href="assets/plugins/animate/animate.css" rel="stylesheet"/>

    <link href="assets/layouts/layout2/css/layout.min.css" rel="stylesheet"/>
    <link href="assets/layouts/layout2/css/themes/blue.min.css" rel="stylesheet"/>

        
    <link href="assets/global/css/components.min.css" rel="stylesheet" id="style_components"/>
    <link href="assets/global/css/plugins.min.css" rel="stylesheet"/>

    <link href="css/core.css" rel="stylesheet" type="text/css"/>

    <!-- END THEME LAYOUT STYLES -->
    <link rel="shortcut icon" href="favicon.ico" />


    <script>
        var stg = {"brand":"Liner","slogan":"R\u00e1pido y seguro","page":"drivers","url_web":"http:\/\/localhost\/liner\/","url_cms":"http:\/\/localhost\/liner\/cms\/","url_cdn":"http:\/\/localhost\/liner\/cdn\/"};
    </script>
</head>

<body class="page-header-fixed page-sidebar-closed-hide-logo page-container-bg-solid">
    <!-- BEGIN HEADER -->
    <div class="page-header navbar navbar-fixed-top">
        <!-- BEGIN HEADER INNER -->
        <div class="page-header-inner ">
            <!-- BEGIN LOGO -->
            <div class="page-logo">
                <a href="http://localhost/liner/cms/">Liner</a>
                <div class="menu-toggler sidebar-toggler"></div>
            </div>
            <!-- END LOGO -->

            <div class="page-actions">
                <!--<h3 class="page-title">Conductores</h3>-->
            </div>

            <!-- BEGIN RESPONSIVE MENU TOGGLER -->
            <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse"> </a>
            <!-- END RESPONSIVE MENU TOGGLER -->
            <!-- BEGIN PAGE TOP -->
            <div class="page-top">
                <!-- BEGIN TOP NAVIGATION MENU -->
                <div class="top-menu">
                    <ul class="nav navbar-nav pull-right">
                        <!-- BEGIN USER LOGIN DROPDOWN -->
                        <li class="dropdown dropdown-user">
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <img alt="" class="img-circle" src="img/avatar.png"/>
                                <span class="username username-hide-on-mobile"> Root </span>
                                <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-default">
                                <li>
                                    <a href="logout.php">
                                        <i class="icon-key"></i> Cerrar sesión
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <!-- END USER LOGIN DROPDOWN -->
                    </ul>
                </div>
                <!-- END TOP NAVIGATION MENU -->
            </div>
            <!-- END PAGE TOP -->
        </div>
        <!-- END HEADER INNER -->
    </div>
    <!-- END HEADER -->
    <!-- BEGIN HEADER & CONTENT DIVIDER -->
    <div class="clearfix"> </div>
    <!-- END HEADER & CONTENT DIVIDER -->
    <!-- BEGIN CONTAINER -->
    <div class="page-container">
        <!-- BEGIN SIDEBAR -->
        <div class="page-sidebar-wrapper">
            <!-- END SIDEBAR -->
            <div class="page-sidebar navbar-collapse collapse">

                <!-- BEGIN SIDEBAR MENU -->
                <ul class="page-sidebar-menu  page-header-fixed page-sidebar-menu-compact" data-slide-speed="200">
                    <li class="nav-item start ">
                        <a href="index.php" class="nav-link nav-toggle">
                            <i class="fa fa-home"></i>
                            <span class="title">Inicio</span>
                            <span class="selected"></span>
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a href="javascript:;" class="nav-link nav-toggle">
                            <i class="fa fa-microphone"></i>
                            <span class="title">Emisoras</span>
                            <span class="selected"></span>
                            <span class="arrow"></span>
                        </a>
                        <ul class="sub-menu">
                            <li class="nav-item ">
                                <a href="stations_add.php" class="nav-link ">
                                    <span class="title">Agregar</span>
                                </a>
                            </li>
                            <li class="nav-item ">
                                <a href="stations.php" class="nav-link ">
                                    <span class="title">Listar</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item ">
                        <a href="javascript:;" class="nav-link nav-toggle">
                            <i class="fa fa-mobile"></i>
                            <span class="title">Dispositivos</span>
                            <span class="selected"></span>
                            <span class="arrow"></span>
                        </a>
                        <ul class="sub-menu">
                            <li class="nav-item ">
                                <a href="devices_notify.php" class="nav-link ">
                                    <span class="title">Notificar</span>
                                </a>
                            </li>
                            <li class="nav-item ">
                                <a href="devices.php" class="nav-link ">
                                    <span class="title">Listar</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item ">
                        <a href="javascript:;" class="nav-link nav-toggle">
                            <i class="fa fa-calendar"></i>
                            <span class="title">Eventos</span>
                            <span class="selected"></span>
                            <span class="arrow"></span>
                        </a>
                        <ul class="sub-menu">
                            <li class="nav-item ">
                                <a href="events_add.php" class="nav-link ">
                                    <span class="title">Agregar</span>
                                </a>
                            </li>
                            <li class="nav-item ">
                                <a href="events.php" class="nav-link ">
                                    <span class="title">Listar</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item ">
                        <a href="javascript:;" class="nav-link nav-toggle">
                            <i class="fa fa-list-alt"></i>
                            <span class="title">Noticias</span>
                            <span class="selected"></span>
                            <span class="arrow"></span>
                        </a>
                        <ul class="sub-menu">
                            <li class="nav-item ">
                                <a href="events_add.php?type=1" class="nav-link ">
                                    <span class="title">Agregar</span>
                                </a>
                            </li>
                            <li class="nav-item ">
                                <a href="news.php" class="nav-link ">
                                    <span class="title">Listar</span>
                                </a>
                            </li>
                        </ul>
                    </li>
                    <li class="nav-item ">
                        <a href="drivers.php" class="nav-link nav-toggle">
                            <i class="fa fa-sitemap"></i>
                            <span class="title">Conductores</span>
                            <span class="selected"></span>
                        </a>
                    </li>
                    <li class="nav-item ">
                        <a href="countries.php" class="nav-link nav-toggle">
                            <i class="fa fa-dashboard"></i>
                            <span class="title">Paises</span>
                            <span class="selected"></span>
                        </a>
                    </li>
                </ul>
                <!-- END SIDEBAR MENU -->
            </div>
            <!-- END SIDEBAR -->
        </div>
        <!-- END SIDEBAR -->
        <!-- BEGIN CONTENT -->
        <div class="page-content-wrapper">
            <!-- BEGIN CONTENT BODY -->
            <div class="page-content">
<div class="row">
    <div class="col-md-12">

        <div class="portlet light">

            <div class="portlet-title">
                <div class="caption">
                    <span class="caption-subject font-dark bold uppercase">Conductores</span>
                </div>
                <div class="actions">
                    <span class="btn btn-circle green btn-outline"> <i class="fa fa-plus"></i> Nuevo </span>
                    <span class="btn btn-circle red btn-outline"> <i class="fa fa-bell"></i> Mensaje </span>
                </div>
            </div>
aaaa
            <div class="portlet-body">

                <!-- FILTERS -->
                <form class="form-inline" id="filters" action="ajax/pager-drivers.php">
                    <div class="form-group">
                        <label>Rango de fechas</label><br>
                        <div class="input-group input-large date-picker input-daterange" data-date-format="yyyy-mm-dd" data-date-end-date="+0d">
                            <input type="text" class="form-control" name="desde" value="" readonly>
                            <span class="input-group-addon"> to </span>
                            <input type="text" class="form-control" name="hasta" value="" readonly>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Consulta</label><br>
                        <input class="form-control" name="word" placeholder="nombre,email,celular"/>
                    </div>
                    <div class="form-group">
                        <label>Resultados</label><br>
                        <select class="form-control select2-multiple" name="max">
                            <option>10</option>
                            <option>20</option>
                            <option>50</option>
                            <option>100</option>
                            <option>200</option>
                            <option>500</option>
                            <option>1000</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>---</label><br>
                        <a href="?" class="btn grey">Reiniciar</a>
                        <button type="button" class="btn blue apply">Aplicar</button>
                    </div>
                </form>
                <!-- END FILTERS -->

                <table class="table table-striped table-bordered table-hover dt-responsive" style="margin-top:10px">
                    <thead>
                    <tr>
                        <th> # </th>
                        <th> Nombre </th>
                        <th width="1%"> DNI </th>
                        <th width="1%"> Email </th>
                        <th> Celular </th>
                        <th> Placa </th>
                        <th width="1%"> Estado </th>
                        <th width="1%"></th>
                    </tr>
                    </thead>
                    <tbody id="pager_content"></tbody>
                </table>

                <div id="pager">
                    <ul class="pagination">
                        <li class="prev"><a><i class="fa fa-angle-left"></i> Anterior</a></li>
                        <li class="disabled"><span class="curr"> 0 </span></li>
                        <li class="next"><a>Siguiente <i class="fa fa-angle-right"></i></a></li>
                    </ul>
                </div>

            </div>

        </div>

    </div>
</div>


            </div>
            <!-- END CONTENT BODY -->
        </div>
        <!-- END CONTENT -->
    </div>
    <!-- END CONTAINER -->

    <!-- BEGIN FOOTER -->
    <div class="page-footer">
        <div class="page-footer-inner"> 2016 &copy; Liner
        </div>
        <div class="scroll-to-top">
            <i class="icon-arrow-up"></i>
        </div>
    </div>
    <!-- END FOOTER -->

    <script src="assets/global/plugins/jquery.min.js"></script>
    <script src="assets/global/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/global/plugins/js.cookie.min.js"></script>
    <script src="assets/global/plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js"></script>
    <script src="assets/global/plugins/jquery.blockui.min.js"></script>
    <script src="assets/global/plugins/uniform/jquery.uniform.min.js"></script>
    <script src="assets/global/plugins/bootstrap-switch/js/bootstrap-switch.min.js"></script>

    <script src="assets/global/plugins/bootstrap-toastr/toastr.min.js" type="text/javascript"></script>
    <script src="assets/global/plugins/bootbox/bootbox.min.js" type="text/javascript"></script>
    <script src="assets/global/plugins/holder.js" type="text/javascript"></script>

    <script src="assets/global/scripts/app.min.js"></script>
    <script src="assets/layouts/layout2/scripts/layout.min.js"></script>

    <script src="js/core.js"></script>

                        <script src="js/pager.js" type="text/javascript"></script>
        
    
</body>
</html><?php }
}
