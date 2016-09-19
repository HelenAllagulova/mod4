<?php

class App{
    protected static $router;

    public static $db;

    public static function getRouter()
    {
        return self::$router;
    }

    public static function run($uri){
       
        self::$router = new Router($uri);
        
        self::$db = DB::getInstance(Config::get('db.host'), Config::get('db.user'), Config::get('db.password'), Config::get('db.db_name'));

        Lang::load(self::$router->getLanguage());

        $controller_class = ucfirst(self::$router->getController()).'Controller';

        $controller_method = strtolower(self::$router->getMethodPrefix()).self::$router->getAction();

        $layout = self::$router->getRoute();
        
        if( $layout == 'admin' && Session::get('role') != 'admin')
        {
            if ($controller_method != 'admin_index' || $controller_class != 'SigninController')
            {
                Router::redirect('/admin/signin');
            }
        }

        if( $layout == 'user' && Session::get('role') != 'user' && Session::get('role') != 'admin')
        {
            Router::redirect('/signin');
        }

        $controller_object = new $controller_class();

        if(method_exists($controller_object, $controller_method)){
            
            $view_path = $controller_object->$controller_method();
            $view_object = new View($controller_object->getData(), $view_path);

            if(!empty($_POST['loyout_flag']) && $_POST['loyout_flag'] == 1){
                echo $content = $view_object->render();
                exit;
            }
            $content = $view_object->render();
            
            $controller_object = new BarsController();

            // Блок левый
            $controller_object->left_bar();
            $view_path = VIEWS_PATH.DS.'bars'.DS.'left_bar.html';
            $view_object = new View($controller_object->getData(), $view_path);
            $left_bar = $view_object->render();

            // Блок реклама левый
            $controller_object->left_ad();
            $view_path = VIEWS_PATH.DS.'bars'.DS.'bars_ad.html';
            $view_object = new View($controller_object->getData(), $view_path);
            $left_ad = $view_object->render();

            // Блок реклама правый
            $controller_object->right_ad();
            $view_path = VIEWS_PATH.DS.'bars'.DS.'bars_ad.html';
            $view_object = new View($controller_object->getData(), $view_path);
            $right_ad = $view_object->render();

            // Блок меню
            $controller_object->nav_bar();
            $view_path = VIEWS_PATH.DS.'bars'.DS.'nav_bar.html';
            $view_object = new View($controller_object->getData(), $view_path);
            $nav_bar = $view_object->render();
            
            // Цвет фона и панели
            $controller_object->style();
            $view_path = VIEWS_PATH.DS.'bars'.DS.'header_style.html';
            $view_object = new View($controller_object->getData(), $view_path);
            $header_style = $view_object->render();

            $controller_object->style();
            $view_path = VIEWS_PATH.DS.'bars'.DS.'body_style.html';
            $view_object = new View($controller_object->getData(), $view_path);
            $body_style = $view_object->render();


        } else {
            throw new Exception('Method '.$controller_method.' of class '.$controller_class.' does not exist.');
        }

        $layout_path = VIEWS_PATH.DS.$layout.'.html';

        $layout_path = preg_replace('/user\.html$/', 'default.html', $layout_path);

        $layout_view_object = new View(compact('content', 'left_bar', 'left_ad', 'right_ad', 'nav_bar', 'header_style', 'body_style'), $layout_path);
        
        echo $layout_view_object->render();
    }
}