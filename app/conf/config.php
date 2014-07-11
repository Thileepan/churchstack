<?php
//app configurations
define('APP_VERSION', '1.0');
define('APP_BUILD_NUMBER', '20140118');

//mysql database information
define('APPLICATION_PATH', dirname(dirname(_FILE_)));
define('DB_DSN', 'churchstack');
define('DB_USER_NAME', 'root');
define('DB_PASSWORD', 'admin');
define('DB_NAME', 'churchstack');

//email configurations
define('SMTP_SERVER', 'smtp.gmail.com');
define('SMTP_USERNAME', 'help@churchstack.com');
define('SMTP_PASSWORD', 'churchstack123$');
define('FROM_ADDRESS', 'help@churchstack.com');
define('FROM_NAME', 'CHURCHSTACK');
?>