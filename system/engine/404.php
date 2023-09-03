<?php
if (!DEFINED('EGP'))
    exit(header('Refresh: 0; URL=http://' . $_SERVER['SERVER_NAME'] . '/404'));

http_response_code(404);
exit(file_get_contents(DIR . '404.html'));
?>