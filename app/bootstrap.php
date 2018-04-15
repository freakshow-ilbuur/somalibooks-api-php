 <?php 

//include configuration 
require_once 'config/config.php';

// include libraries
//  require_once 'libraries/core.php';
//  require_once 'libraries/database.php';
//  require_once 'libraries/controller.php';
require_once 'helpers/url_helper.php';
require_once 'helpers/pagination.php';
require_once 'helpers/jwt_helper.php';


 spl_autoload_register(function($className){
     require_once 'libraries/'. $className .'.php';
 });
 