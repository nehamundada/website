<?php

define('PEAR_INCLUDE_PATH', 'Mail.php');
define('PEAR_INCLUDE_PATH', 'Mail/mime.php');
require_once 'Mail.php';
require_once 'Mail/mime.php';

// get the form parameters
$name = $_POST["name"];
$email = $_POST["email"];
$comment = $_POST["message"];
$source = $_POST["source"];
$knownPerson = $_POST["joinform-who"];
$phoneNumber = $_POST["joinform-tel"];
$fileName = $_POST["joinform-cv"];

//sanitize the string. remove single quotes
$name = str_replace("'", "", $name);
$email = str_replace("'", "", $email);
$comment = str_replace("'", "", $comment);
$source = str_replace("'", "", $source);
$knownPerson = str_replace("'", "", $knownPerson);
$phoneNumber = str_replace("'", "", $phoneNumber);
$fileName = str_replace("'", "", $fileName);
$sourceHeading = " ";
$errors = '';


try {
	$subject = "Mail from EE-Website";
	$message = new Mail_mime();
	$text = "\n\nNew user registered on Equal Experts Webiste";
	$text .= "\n\nName -" . $name;
	$text .= "\n\nEmail Address -" . $email;
set_include_path(get_include_path() . PATH_SEPARATOR . '/usr/bin/pear');
	
	if ($source == "contactUs") {
		$sourceHeading = "Comments";
		$to = "info@equalexperts.com";
		
	} else {

		$sourceHeading = "Expertise";
		$to = "ndavies@equalexperts.com";

		if ($phoneNumber != "") {
			$text .= "\n\nContact number - " . $phoneNumber;
		}
		if ($knownPerson != "") {
			$text .= "\n\nAlready know someone in the network - " . $knownPerson;
		}

		if ($_FILES["joinform-cv"]["name"] != "") {
			$name_of_uploaded_file = basename($_FILES["joinform-cv"]["name"]);
			$type_of_uploaded_file = substr($name_of_uploaded_file, strrpos($name_of_uploaded_file, '.') + 1);
			$allowed_extensions = array("doc", "docx", "odt", "pdf");
			$allowed_ext = false;
			for ($i = 0; $i < sizeof($allowed_extensions); $i++) {
				if (strcasecmp($allowed_extensions[$i], $type_of_uploaded_file) == 0) {
					$allowed_ext = true;
				}

			}

			if (!$allowed_ext) {
				$errors .= "Please check if your file is of type: doc, docx, odt ,pdf";
				
			}

			$max_allowed_file_size = 5;
			$size_of_uploaded_file = $_FILES["joinform-cv"]["size"] / 1048576;

			if ($size_of_uploaded_file > $max_allowed_file_size) {
				$errors .= "File size must be less than 5 MB";
			}

			$upload_folder = "/tmp/";
			$path_of_uploaded_file = $upload_folder . $name_of_uploaded_file;
			$tmp_path = $_FILES["joinform-cv"]["tmp_name"];
			if (is_uploaded_file($tmp_path)) {

				if (!copy($tmp_path, $path_of_uploaded_file)) {
					$errors .= '\n error while copying the uploaded file';
				}
			}

			$message -> addAttachment($path_of_uploaded_file);
		}
	}

	if (empty($errors)) {

		$text .= "\n\n" . $sourceHeading . "-" . $comment;
		$message -> setTXTBody($text);
		$body = $message -> get();

		$extraheaders = array("From" => $email, "Subject" => $subject, "Reply-To" => $email);
		$headers = $message -> headers($extraheaders);

		$mail = Mail::factory("mail");

		$mail -> send($to, $headers, $body);
		
		if(PEAR::isError($mail)) {
			echo json_encode(array('result' => 'Email can not be sent at the moment,please try again later','success'=> TRUE));
		} else {
			echo json_encode(array('result' => 'Thank you,We will get in touch soon','success'=> TRUE));
		}
	}
	else
		{
			echo json_encode(array('result' =>$errors, 'success'=> FALSE));
		}

}

catch(Exception $e) {

	echo $e->getMessage();
}

header('Content-Type : application/json ; charset=UTF-8');

?>

