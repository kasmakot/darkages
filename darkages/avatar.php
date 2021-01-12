<?
	$contentType = 'image/jpeg';

	$path = 'images/avatars/';

	$path .= (!key_exists('id', $_GET) || !($extension = ImageExtension($path, $_GET['id'])))
		? 0
		: ($_GET['id'] . '.' . $extension);

	$contentType = ($imageType = exif_imagetype($path)) ? image_type_to_mime_type($imageType) : $contentType;

	header('Content-Type: ' . $contentType);
	echo file_get_contents($path);


	function ImageExtension($path, $id)
	{
		$extensions = array('jpeg', 'jpg', 'png');

		foreach ($extensions as $ext) {
			if (file_exists($path . $id . '.' . $ext)) {
				return $ext;
			}
		}

		return false;
	}
?>