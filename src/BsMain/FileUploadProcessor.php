<?php

namespace BsMain;

use BsMain\Exception\BsAppRuntimeException;

class FileUploadProcessor {

	/**
	 * File size is limited in three ways. See below how it can be observed:
	 * 1. Webserver request limitation: request is aborted, _FILES is empty,
	 *    while the Content-Length does not exceed PHP settings.
	 * 2. PHP post_max_size exceeded: request is aborted, _FILES is empty, and
	 *    Content-Length exeeds PHP Limit. However, next limit is ALWAYS lower,
	 *    so that value should be used in the error message.
	 * 3. PHP upload_max_filesize. Request is complete, _FILES is filled but the
	 *    error member has value UPLOAD_ERR_INI_SIZE.
	 * 
	 * When one of these situations occur, show the lowest limit, as that's the 
	 * effective limit for the user. We do not know the webserver limit, so we
	 * assume this was the trigger if the Content-Length is within PHP limits.
	 * 
	 * @param string $fieldName Form field name of the file upload.
	 * @throws Exception\BsAppRuntimeException If the file was not uploaded correctly.
	 */
	public static function verifyUpload(string $fieldName): void {
		$fileError = $_FILES[$fieldName]['error'] ?? '';
		if (empty($_FILES) || $fileError === UPLOAD_ERR_INI_SIZE) {
			$maxFileSize = self::convertConfigSizeToBytes(ini_get('upload_max_filesize'));
			$maxPostSize = self::convertConfigSizeToBytes(ini_get('post_max_size'));
			$maxAllowedSize = $maxFileSize < $maxPostSize ? ini_get('upload_max_filesize') : ini_get('post_max_size');

			// If the request is smaller than the PHP limits,
			// the webserver has most likely blocked the request.
			if (isset($_SERVER['CONTENT_LENGTH']) &&
					$_SERVER['CONTENT_LENGTH'] <= self::convertConfigSizeToBytes($maxAllowedSize)) {
				$maxAllowedSize = '?? (check webserver)';
			}
			$ex = new BsAppRuntimeException('{#error_fileSizeLimitExceeded#}');
			$ex->addParam($maxAllowedSize);
			throw $ex;
		}

		switch ($fileError) {
			case UPLOAD_ERR_OK:
				break;
			case UPLOAD_ERR_NO_FILE:
				throw new BsAppRuntimeException('{#error_fileUploadMissing#}');
			default:
				$ex = new BsAppRuntimeException('{#error_fileUploadUnknownError#}');
				$ex->addParam($fileError);
				throw $ex;
		}
	}

	private static function convertConfigSizeToBytes($configSize): int {
		if (preg_match('/^(\\d+)([KMGT]?)$/', strtoupper($configSize), $matches)) {
			$factor = array_search($matches[2], ['', 'K', 'M', 'G', 'T']);
			return $matches[1] * (1024 ** $factor);
		}
		return (int) $configSize;
	}

}
