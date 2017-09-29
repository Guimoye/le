<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

class Route{

    // Lista de URI para coincidir con
    private $_listUri = array();

    // Artículos de toda la clase para limpiar
    private $_trim = '/\^$';

    /**
     * Agrega un URI y una función a las dos listas
     */
    public function add($uri, $controller, $method){
        $uri = trim($uri, $this->_trim);
        $this->_listUri[] = [
            'uri' => $uri,
            'controller' => $controller,
            'method' => $method
        ];
    }

    /**
     * Busca una coincidencia para el URI y ejecuta la función relacionada
     */
    public function send(){
        $uri = isset($_REQUEST['uri']) ? $_REQUEST['uri'] : '/';
        $uri = trim($uri, $this->_trim);

        $replacementValues = array();

        // Lista a través de los URI almacenados
        foreach ($this->_listUri as $item){

            //Ver si hay un partido
            if(preg_match("#^".$item['uri']."$#", $uri)){

                // Reemplazar los valores
                $realUri = explode('/', $uri);
                $fakeUri = explode('/', $item['uri']);

                //Reúna los valores. + Con los valores reales en el URI
                foreach($fakeUri as $key => $value){
                    if($value == '.+'){
                        $replacementValues[] = $realUri[$key];
                    }
                }


                $className = 't_'.$item['controller'];
                $classFile = $className.'.php';
                include($classFile);
                $object = new $className();
                call_user_func_array(array($object, $item['method']), $replacementValues);

                // Pasar una matriz de argumentos
                //call_user_func_array($item['method'], $replacementValues);
            }

        }

    }

}

include('t_base.php');

$route = new Route();
$route->add('/drivers',                 'drivers', 'index');
$route->add('/drivers/.+',              'drivers', 'list');
$route->add('/drivers/.+/dues_retal',   'page', 'index');
$route->add('/drivers/.+/dues_sale',    'page', 'index');
$route->add('/drivers/.+/expenses',     'page', 'index');

$route->add('/settings/users',          'page', 'index');
$route->add('/settings/levels',         'page', 'index');
$route->add('/drivers/system',          'page', 'index');
$route->send();

/*class Route{

    // Lista de URI para coincidir con
    private $_listUri = array();

    // Lista de cierres a llamar
    private $_listCall = array();

    // Artículos de toda la clase para limpiar
    private $_trim = '/\^$';

    // Agrega un URI y una función a las dos listas
    public function add($uri, $function){
        $uri = trim($uri, $this->_trim);
        $this->_listUri[] = $uri;
        $this->_listCall[] = $function;
    }

    // Busca una coincidencia para el URI y ejecuta la función relacionada
    public function submit(){
        $uri = isset($_REQUEST['uri']) ? $_REQUEST['uri'] : '/';
        $uri = trim($uri, $this->_trim);

        $replacementValues = array();

        // Lista a través de los URI almacenados
        foreach ($this->_listUri as $listKey => $listUri){

            //Ver si hay un partido
            if(preg_match("#^$listUri$#", $uri)){

                // Reemplazar los valores
                $realUri = explode('/', $uri);
                $fakeUri = explode('/', $listUri);

                //Reúna los valores. + Con los valores reales en el URI
                foreach ($fakeUri as $key => $value){
                    if ($value == '.+'){
                        $replacementValues[] = $realUri[$key];
                    }
                }

                // Pasar una matriz de argumentos
                call_user_func_array($this->_listCall[$listKey], $replacementValues);
            }

        }

    }

}*/