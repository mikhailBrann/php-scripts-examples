<?php
session_start();

echo json_encode(array(
    'registr_status' => 'logout'
));

session_unset();
session_destroy();
