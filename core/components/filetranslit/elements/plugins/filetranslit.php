<?php
/**
 * @author Anton Andersen <anton.a.andersen@gmail.com>
 * @author exside
 *
 * This plugin transliterates filenames on upload via MODX filemanager.
 * It should be bent to the OnFileManagerUpload event.
 * Project page: https://github.com/TriAnMan/filetranslit
 *
 * @todo
 * - Make use of 2.3 OnFileManagerBeforeUpload system event but keep BC in mind for 2.2
 * - Make a system setting to configure what should happen when a file already exists, either replace or rename!
 */

foreach ($files as &$file) {
	if ($file['error'] == 0) {
		$newName = $modx->call('modResource', 'filterPathSegment', array(&$modx, $file['name']));

		//file rename logic
		if ($file['name'] !== $newName) {
			$arDirFiles = $source->getObjectsInContainer($directory);
			foreach ($arDirFiles as &$dirFile){
				if($dirFile['name']===$newName){
					//delete file if there is one with new name
					$source->removeObject($directory . $newName);
				}
			}
			//transliterate uploaded file
			$source->renameObject($directory . $file['name'], $newName);

			// re-assing new filename to the $_FILES variable
			$_FILES['file']['name'] = $newName;
		}
	}
}