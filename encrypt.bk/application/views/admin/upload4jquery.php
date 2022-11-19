<?php
$doc_root = dirname(__FILE__);
include_once $doc_root.'/upload/upload_class.php';
include_once $doc_root.'/upload/foto_upload_script.php';

$json['img'] = '';
$json['error'] = '';

if (empty($_FILES['fileToUpload']['name'])) {
	$json['error'] = 'Please select a file.';
} else {
	$foto_upload = new Foto_upload;
	$foto_upload->upload_dir = $doc_root.'/files/tmp/'; // 'files' is the folder for the uploaded files (you have to create these folder)
	$foto_upload->foto_folder = $doc_root.'/files/photo/';
	$foto_upload->thumb_folder = $doc_root.'/files/thumb/';
	$foto_upload->extensions = array('.jpg', '.gif', '.png'); // specify the allowed extension(s) here
	$foto_upload->language = 'en';
	$foto_upload->x_max_size = 120;
	$foto_upload->y_max_size = 120;
	$foto_upload->x_max_thumb_size = 120;
	$foto_upload->y_max_thumb_size = 120;

	$foto_upload->the_temp_file = $_FILES['fileToUpload']['tmp_name'];
	$foto_upload->the_file = $_FILES['fileToUpload']['name'];
	$foto_upload->http_error = $_FILES['fileToUpload']['error'];
	$foto_upload->do_filename_check = 'y';


	if ($foto_upload->upload()) {
		$foto_upload->process_image(false, false, true, 80);
		$json['img'] = $foto_upload->file_copy;
	}

	$json['error'] = strip_tags($foto_upload->show_error_string('##'));
}
echo json_encode($json);
