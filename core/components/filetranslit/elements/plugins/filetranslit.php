<?php
/**
 * @author Anton Andersen <anton.a.andersen@gmail.com>
 *
 * This plugin transliterates filenames on upload via MODX filemanager.
 * It should be bent to the OnFileManagerUpload event.
 * Project page: https://github.com/TriAnMan/filetranslit
 */
$currentdoc = $modx->newObject('modResource');
foreach ($files as &$file) {
	if ($file['error'] == 0) {
		$newName = $currentdoc->cleanAlias($file['name']);

		//file rename logic
		if ($file['name'] !== $newName) {
			//delete file if there is one with new name
			$source->removeObject($directory . $newName);
			//transliterate uploaded file
			$source->renameObject($directory . $file['name'], $newName);
		}
	}
}