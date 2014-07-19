<?php
//app configurations
define('APP_VERSION', '1.0');
define('APP_BUILD_NUMBER', '20140118');
define('PRODUCT_NAME', 'ChurchStack');

//mysql database information
define('APPLICATION_PATH', dirname(dirname(_FILE_)));
define('DB_DSN', 'churchstack');
define('DB_USER_NAME', 'root');
define('DB_PASSWORD', 'admin');
define('DB_NAME', 'churchstack');

//email configurations
define('EMAIL_FROM_SALES', 1);
define('EMAIL_FROM_SUPPORT', 2);
define('EMAIL_FROM_DONOTREPLY', 3);
define('EMAIL_FROM_INFO', 4);

define('SMTP_SERVER', 'smtp.gmail.com');
define('SMTP_USERNAME', 'help@churchstack.com');
define('SMTP_PASSWORD', 'churchstack123$');
define('FROM_ADDRESS', 'help@churchstack.com');
define('FROM_NAME', 'ChurchStack');
define('FROM_SALES_ADDRESS', 'sales@churchstack.com');
define('FROM_SALES_NAME', 'ChurchStack.com');
define('FROM_SUPPORT_ADDRESS', 'support@churchstack.com');
define('FROM_SUPPORT_NAME', 'ChurchStack.com');
define('FROM_DONOTREPLY_ADDRESS', 'do-not-reply@churchstack.com');
define('FROM_DONOTREPLY_NAME', 'ChurchStack.com');
define('FROM_INFO_ADDRESS', 'info@churchstack.com');
define('FROM_INFO_NAME', 'ChurchStack.com');
?>