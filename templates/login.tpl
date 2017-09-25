<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Iniciar sesión | {$stg->brand}</title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />

    <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet"/>
    <link href="assets/global/plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet"/>
    <link href="assets/global/plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet"/>
    <link href="assets/global/css/components.min.css" rel="stylesheet"/>
    <link href="assets/pages/css/login.min.css" rel="stylesheet"/>
    <link href="css/core.css" rel="stylesheet"/>

    <link rel="shortcut icon" href="favicon.ico" />
</head>

<body class=" login">
    <!-- BEGIN LOGO -->
    <div class="logo">
        <a href="{$stg->url_cms}">{$stg->brand}</a>
    </div>
    <!-- END LOGO -->
    <!-- BEGIN LOGIN -->
    <div class="content">
        <!-- BEGIN LOGIN FORM -->
        <form class="login-form" method="post">
            <h3 class="form-title font-green">Iniciar sesión</h3>
            <div class="alert alert-danger {if !$error}display-hide{/if}">
                <button class="close" data-close="alert"></button>
                <span>Datos incorrectos.</span>
            </div>
            <div class="form-group">
                <input class="form-control form-control-solid placeholder-no-fix" type="text" autocomplete="off" placeholder="Usuario" name="username" />
            </div>
            <div class="form-group">
                <input class="form-control form-control-solid placeholder-no-fix" type="password" autocomplete="off" placeholder="Contraseña" name="password" />
            </div>
            <div class="form-actions">
                <button type="submit" class="btn green uppercase">Ingresar</button>
            </div>
        </form>
        <!-- END LOGIN FORM -->
    </div>
    <div class="copyright"> {'Y'|date} © {$stg->brand} </div>

    <script src="assets/global/plugins/jquery.min.js"></script>
    <script src="assets/global/plugins/bootstrap/js/bootstrap.min.js"></script>
    <script src="assets/global/plugins/jquery-validation/js/jquery.validate.min.js"></script>
    <script src="assets/global/scripts/app.min.js"></script>
    <script src="assets/pages/scripts/login.min.js"></script>

</body>
</html>