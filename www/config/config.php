<?php
Config::set('site_name', 'NEWS');

Config::set('languages', array('en', 'fr'));
Config::set('routes',array(
    'default' => '',
    'admin' => 'admin_',
    'user' => 'user_'
));

Config::set('default_route', 'default');
Config::set('default_language', 'en');
Config::set('default_controller', 'pages');
Config::set('default_action', 'index');

Config::set('db.host', '');
Config::set('db.user', '');
Config::set('db.password', '');
Config::set('db.db_name', '');


Config::set('salt','rijeeh35sda766eue');
