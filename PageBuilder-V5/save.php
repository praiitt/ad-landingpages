<?php


	/* CONFIG */

	$pathToAssets = array("elements/css", "elements/fonts", "elements/images", "elements/scripts", "elements/video");
	
	$filename = "tmp/" . uniqid() . "_website.zip"; //use the /tmp folder to circumvent any permission issues on the root folder

	/* END CONFIG */
		

	$zip = new ZipArchive();
	
	$zip->open($filename, ZipArchive::CREATE);

	$images = Array();
//	$video = Array();

	if(isset($_POST['images'])) {
		$images = explode('||',$_POST['images']);
	}

//	if(isset($_POST['video'])) {
//		$video = explode('||',$_POST['video']);
//	}

	//add folder structure

	foreach( $pathToAssets as $thePath ) {

		// Create recursive directory iterator
		$files = new RecursiveIteratorIterator(
			new RecursiveDirectoryIterator( $thePath ),
			RecursiveIteratorIterator::LEAVES_ONLY
		);

		if ( $thePath === "elements/images" ) {

			foreach ( $files as $name => $file ) {

				foreach ( $images as $image ) {

					if( $file->getFilename() != '.' && $file->getFilename() != '..' && $file->getFilename() === $image) {

						// Get real path for current file
						$filePath = $file->getRealPath();

						$temp = explode("/", $name);

						array_shift( $temp );

						$newName = implode("/", $temp);

						// Add current file to archive
						$zip->addFile($filePath, $newName);

					}
				}
			}

		} elseif ($thePath === "elements/video") {

			if (isset($_POST['video'])) {
				foreach ( $files as $name => $file ) {

					if ( $file->getFilename() != '.' && $file->getFilename() != '..' ) {

						// Get real path for current file
						$filePath = $file->getRealPath();

						$temp = explode( "/", $name );

						array_shift( $temp );

						$newName = implode( "/", $temp );

						// Add current file to archive
						$zip->addFile( $filePath, $newName );

					}
				}
			}

		} else {

			foreach ($files as $name => $file) {

				if( $file->getFilename() != '.' && $file->getFilename() != '..' ) {

					// Get real path for current file
					$filePath = $file->getRealPath();

					$temp = explode("/", $name);

					array_shift( $temp );

					$newName = implode("/", $temp);

					// Add current file to archive
					$zip->addFile($filePath, $newName);

				}
			}

		}

	}

	
	
	foreach( $_POST['pages'] as $page=>$content ) {
		$patternTitle = "/\<title\>(.*)\<\/title\>/i";
		$content = preg_replace($patternTitle, "<title>".$_POST['title']."</title>", $content);
        $patternMetaDescription = "/\<!\-\-META-DESCRIPTION\-\-\>/i";
		$content = preg_replace($patternMetaDescription, '<meta name="description" content="'.$_POST['meta-description'].'">', $content);
        $patternMetaKeywords = "/\<!\-\-META-KEYWORDS\-\-\>/i";
		$content = preg_replace($patternMetaKeywords, '<meta name="keywords" content="'.$_POST['meta-keywords'].'">', $content);
        $patternCustomJs = "/\<!\-\-CUSTOM-JS\-\-\>/i";
		$content = preg_replace($patternCustomJs, $_POST['js-include'], $content);
		$patternPreloader = "/\<!\-\-PRELOADER\-\-\>/i";
		if (isset($_POST['preloader'])) {
			$content = preg_replace($patternPreloader, '<!--PRELOADER-->
			    <div id="preloader">
			        <div class="loading-data"></div>
			    </div>', $content);
		} else {
			$content = preg_replace($patternPreloader, "", $content);
		}
		$zip->addFromString($page.".html", "<!DOCTYPE html>\n".stripslashes($content));
	
	}
	
	$zip->close();
	
	$file_name = basename($filename);
	
	header("Content-Type: application/zip");
	header("Content-Transfer-Encoding: Binary");
	header("Content-Disposition: attachment; filename=$file_name");
	header("Content-Length: " . filesize($filename));
	
	readfile($filename);
    
    //$filename = file_get_contents($filename);
    //print $filename;

	unlink($filename);
	
	exit;
?>
