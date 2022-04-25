<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$destination_folder = 'assets/uploads/';
if (!empty($_FILES)) {
    $temp_file = $_FILES['file']['tmp_name'];

    $image_size_data = getimagesize($temp_file);

    if( !$image_size_data ){
        header($_SERVER['SERVER_PROTOCOL'] . ' Error uploading the file', true, 500);
        echo "Only image files are allowed.";exit;
    }

    $file_name = explode(".",$_FILES["file"]["name"]);
    $extension = $file_name[1];
    $new_name = $file_name[0].'_'.round(microtime(true) * 1000);
    $new_file_name = $new_name .".".$extension;
    $target_file =  __DIR__ .'/../../assets/uploads/'. $new_file_name;
    $result = move_uploaded_file($temp_file,$target_file);
    if($result){
        echo 'File uploaded successfully.';

        session_start();
        $data = array(
            'mold_id' => isset($_SESSION['mold_id']) ? $_SESSION['mold_id'] : 0,
            'photo_url' => $destination_folder. $new_file_name
        );

        include_once(dirname(dirname(__FILE__)) . '/classes/functions.php');
        addMoldPhoto($data);
    }else{
        header($_SERVER['SERVER_PROTOCOL'] . ' Error uploading the file', true, 500);
        echo "Error occurred!";
    }
    exit;
}