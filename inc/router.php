<?php

/**
 * Class Route
 *
 * ========================
 * Manual
 * $router = new Router();
 * $router->add('foo/:num', 'controller', 'method');
 * $router->add('foo/:any', 'controller', 'method');
 * $router->add('foo/(expresion_regular)/(.*)', 'controller', 'method');
 *
 * ========================
 * Dinamico
 *
 * uso: $route->add(':any', '%', '%');
 *
 * Aplica para:
 *
 * /foo             -> $foo->index();
 * /foo/bar         -> $foo->bar();
 * /foo/50          -> $foo->item(50);
 * /foo/50/bar      -> $foo->bar(50);
 * /foo/50/60/bar   -> $foo->bar(50,60);
 * /foo/bar/50      -> $foo->bar(50);
 * /foo/bar/50/60   -> $foo->bar(50,60);
 */

class Route{

    // Lista de URI para coincidir con
    private $routes = array();

    /**
     * Agrega un URI
     */
    public function add($uri, $controller, $method = 'index'){
        $this->routes[$uri] = [
            'controller' => $controller,
            'method' => $method
        ];
    }

    /**
     * Busca una coincidencia para el URI y ejecuta la función relacionada
     */
    public function send(){
        $uri = isset($_REQUEST['uri']) ? $_REQUEST['uri'] : '';

        $segs = empty($uri) ? array() : explode('/', $uri);
        $segs_count = count($segs);

        $args = $segs;

        //print_r($segs);
        //exit;

        // Lista a través de los URI almacenados
        foreach($this->routes as $key => $item){

            $key = str_replace(':any', '(.+)', str_replace(':num', '([0-9]+)', $key));

            $useAutomatic = false;

            if(isset($segs[0]) && $item['controller'] == '%'){
                $item['controller'] = $segs[0];
                $useAutomatic = true;
            }

            if($item['method'] == '%'){
                //$item['method'] = ($segs_count>1) ? $segs[$segs_count-1] : 'index';
                $item['method'] = ($segs_count>1) ? $segs[1] : 'index';

                if(is_numeric($item['method']) && $segs_count > 2){
                    $item['method'] = $segs[$segs_count-1];
                }

                if(is_numeric($item['method'])){
                    $item['method'] = 'item';
                }

                $useAutomatic = true;
            }

            //Ver si hay un partido
            if(preg_match("#^".$key."$#", $uri, $preg_output)){

                if($useAutomatic){
                    $this->unsetValue($args, $item['controller']);
                    $this->unsetValue($args, $item['method']);
                    $this->validate($item['controller'], $item['method'], $args);
                } else {
                    unset($preg_output[0]); // el primer valor es uri
                    $this->validate($item['controller'], $item['method'], $preg_output);
                }

                return;
            }

        }

        $this->show404();

    }

    function unsetValue(&$array, $value, $strict = FALSE){
        //echo 'unset: '.$value."\n";
        if(($key = array_search($value, $array, $strict)) !== FALSE) {
            unset($array[$key]);
        }
    }

    private function validate($controller, $method, $args = array()){

        $controller = 't_'.$controller;

        if(file_exists($controller.'.php'))
        {
            include($controller.'.php');
            if(class_exists($controller))
            {
                $object = new $controller();
                if(method_exists($object, $method))
                {
                    if(is_callable(array($object, $method)))
                    {
                        $refl = new ReflectionMethod($object, $method);
                        $numParams = $refl->getNumberOfRequiredParameters();

                        if($numParams == count($args))
                        {
                            call_user_func_array(array($object, $method), $args);

                        } else $this->show404('Se espera ('.$numParams.') parametros, hay ('.count($args).')->('.implode(', ',$args).') en el metodo: '.$method);
                    } else $this->show404('Este metodo es privado: '.$method);
                } else $this->show404('Metodo no existe: '.$method);
            } else $this->show404('Controlador no existe: '.$controller);
        } else $this->show404('Archivo controlador no existe: '.$controller);
    }

    private function show404($text = ''){
        echo $text;
        /*$object = new t_base();
        $ui = $object->ui();
        $ui->display('e404.tpl');*/
    }

}