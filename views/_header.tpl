<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <title>{$page_title} | {$stg->brand}</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />


    <base href="{$stg->url_cms}">

    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet"/>
    <link href="assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet"/>
    <link href="assets/global/plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet"/>
    <link href="assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="assets/global/plugins/uniform/css/uniform.default.min.css" rel="stylesheet"/>

    <link href="assets/global/plugins/bootstrap-toastr/toastr.min.css" rel="stylesheet" type="text/css" />

    <link href="assets/plugins/animate/animate.css" rel="stylesheet"/>

    <link href="assets/layouts/layout/css/layout.min.css" rel="stylesheet"/>
    <link href="assets/layouts/layout/css/themes/darkblue.min.css" rel="stylesheet"/>

    {if isset($css)}
        {foreach item=s from=$css}
            <link href="{$s}?v={$v}" rel="stylesheet"/>
        {/foreach}
    {/if}
    
    <link href="assets/global/css/components.min.css" rel="stylesheet" id="style_components"/>
    <link href="assets/global/css/plugins.min.css" rel="stylesheet"/>

    <link href="views/css/core.css?v={$v}" rel="stylesheet" type="text/css"/>

    <!-- END THEME LAYOUT STYLES -->
    <link rel="shortcut icon" href="favicon.ico" />

    <script>
        var stg = {$stg|@json_encode};
    </script>
</head>

<body class="page-header-fixed page-sidebar-closed-hide-logo page-container-bg-solid {if $stg->menu_collapsed==1}page-sidebar-closed{/if}">

    <!-- HEADER -->
    <div class="page-header navbar navbar-fixed-top">
        <!-- HEADER INNER -->
        <div class="page-header-inner ">

            <div class="ofl_bar" style="display:none"></div>

            <!-- LOGO -->
            <div class="page-logo">
                <a href="{$url_home}">{$stg->brand}</a>
                <div class="menu-toggler sidebar-toggler"></div>
            </div>
            <!-- END LOGO -->

            <!-- RESPONSIVE MENU TOGGLER -->
            <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse"> </a>
            <!-- END RESPONSIVE MENU TOGGLER -->

            <!-- PAGE TOP -->
            <div class="page-top">

                <!-- SHORTCUTS -->
                <div class="top-menu pull-left">
                    <ul class="nav navbar-nav menu-shortcuts">

                        {foreach $menu.shortcuts as $m}
                            <li class="dropdown dropdown-user {if $m.active}active{/if}">
                                <a href="{$m.url}">
                                    <i class="{$m.icon}"></i>
                                    <span class="hidden-xs">{$m.name}</span>
                                </a>
                            </li>
                        {/foreach}

                    </ul>
                </div>
                <!-- END SHORTCUTS -->

                <!-- TOP NAVIGATION MENU -->
                <div class="top-menu">

                    <ul class="nav navbar-nav pull-right">

                        <!-- USER LOGIN DROPDOWN -->
                        <li class="dropdown dropdown-user">
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <i class="icon-user"></i>
                                <span class="username hidden-xs"> {$u.name} </span>
                                <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-default">
                                <!--<li> <a href="#"> Option 1 </a> </li>
                                <li> <a href="#"> Option 2 </a> </li>
                                <li class="divider"></li>-->
                                <li> <a href="login/logout"> Cerrar sesión </a> </li>
                            </ul>
                        </li>
                        <!-- END USER LOGIN DROPDOWN -->

                        <!-- FULL -->
                        <li class="dropdown dropdown-user">
                            <a id="head_fullscreen" class="dropdown-toggle" onclick="toggleFullScreen();">
                                <i class="fa fa-arrows-alt"></i>
                            </a>
                        </li>
                        <!-- END FULL -->

                    </ul>
                </div>
                <!-- END TOP NAVIGATION MENU -->
            </div>
            <!-- END PAGE TOP -->

        </div>
        <!-- END HEADER INNER -->
    </div>
    <!-- END HEADER -->

    <div class="clearfix"> </div>

    <!-- CONTAINER -->
    <div class="page-container">

        <!-- SIDEBAR -->
        <div class="page-sidebar-wrapper">
            <!-- SIDEBAR -->
            <div class="page-sidebar navbar-collapse collapse">

                <!-- SIDEBAR MENU -->
                <ul class="page-sidebar-menu  page-header-fixed {if $stg->menu_collapsed==1}page-sidebar-menu-closed{/if}"
                    data-keep-expanded="false" data-auto-scroll="true" data-slide-speed="200" style="padding-top:10px">
                    <li class="sidebar-toggler-wrapper hide">
                        <!-- SIDEBAR TOGGLER BUTTON -->
                        <div class="sidebar-toggler"> </div>
                        <!-- END SIDEBAR TOGGLER BUTTON -->
                    </li>

                    {function mkMenu level=0}
                        {foreach $data as $m}
                            <li class="nav-item start {if $m.active}active{/if}">
                                <a href="{$m.url}" class="nav-link nav-toggle" {if empty($m.sub)&&$m.ready==0}style="opacity:0.3"{elseif empty($m.sub)&&$m.ready==2}style="color:red"{/if}>
                                    {if $level==0}<i class="{$m.icon}"></i>{/if}
                                    <span class="title">{$m.name}</span>
                                    {if !empty($m.sub)}<span class="arrow"></span>{/if}
                                </a>
                                {if !empty($m.sub)}
                                    <ul class="sub-menu">
                                        {mkMenu data=$m.sub level=$level+1}
                                    </ul>
                                {/if}
                            </li>
                        {/foreach}
                    {/function}
                    {mkMenu data=$menu.main}


                </ul>
                <!-- END SIDEBAR MENU -->

            </div>
            <!-- END SIDEBAR -->
        </div>
        <!-- END SIDEBAR -->

        <!-- CONTENT -->
        <div class="page-content-wrapper">
            <!-- CONTENT BODY -->
            <div class="page-content">