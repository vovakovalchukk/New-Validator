<?php 

// Controller

define('BASE_DIR', __DIR__ . DIRECTORY_SEPARATOR);

if ($_POST) {
	require 'action/post.php';
} 
else {    
    require 'action/show.php';
}
