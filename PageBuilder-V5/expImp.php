<?php
	if(isset($_POST['status']) && $_POST['status'] === "exp" && !empty($_POST['JSONProject'])) {
		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: application/octet-stream' );
		header( 'Content-Disposition: attachment; filename=project.pbproject' );
		header( 'Content-Transfer-Encoding: binary' );
		header( 'Connection: Keep-Alive' );
		header( 'Expires: 0' );
		header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' );
		header( 'Pragma: public' );

		echo $_POST['JSONProject'];

	} elseif($_POST['status'] === "imp") {
		if (isset($_FILES['projectImp'])) {
			$fileData = file_get_contents($_FILES['projectImp']['tmp_name']);
			$pos = strrpos($_FILES['projectImp']['name'], '.');
			$typeFile = substr($_FILES['projectImp']['name'], $pos);

			if(!$fileData || $typeFile != '.pbproject') {
				$error = "Error opening file!";
				exit(json_encode(array('error' => $error, 'data' => '')));
			}
			exit(json_encode(array('error' => '', 'data' => $fileData)));
		}
	} else {
		echo "Update your PHP version please!";
	}
