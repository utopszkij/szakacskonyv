<?php 
    //process the ckedior image upload 
    $uploadDir = __DIR__.'/images/uploads/';
    $uploadUrl = 'images/uploads/'; 
	if (!is_dir($uploadDir)) {
		mkdir($uploadDir);
	}
    foreach ($_FILES as $fn => $fv) {
		$uploadFile = $uploadDir . basename($_FILES[$fn]['name']);	
		if (strpos($uploadFile,'.php') > 0) {
			echo JSON_encode(array('error'=>'php upload not enabled'));
			exit();
		}
		if (file_exists($uploadFile)) {
			unlink($uploadFile);
		}
		if (move_uploaded_file($_FILES[$fn]['tmp_name'], $uploadFile)) {
			$url = $uploadUrl.basename($_FILES[$fn]['name']);
			echo JSON_encode(array('url'=>$url));
			exit();
		} else {
			echo JSON_encode(array('error'=>'error in upload'));
			exit();
		}
	}
	echo JSON_encode(array('error'=>'not uploaded file'));
	exit();
   
?>
