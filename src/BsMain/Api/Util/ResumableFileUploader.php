<?php

namespace BsMain\Api\Util;

use BsMain\Api\BsResourceBaseApi;
use GuzzleHttp\RequestOptions;

/**
 * File upload helper to send a file using the resumable file upload method,
 * {@see https://docs.valence.desire2learn.com/basic/fileupload.html#resumable-uploads}.
 *
 * Known usages:
 * - POST /d2l/api/le/(version)/(orgUnitId)/dropbox/folders/(folderId)/feedback/(entityType)/(entityId)/attach
 *      Upload feedback to an assignment submission.
 */
class ResumableFileUploader {

	public const int MAX_CHUNK_SIZE_KB = -1;

	public function __construct(private BsResourceBaseApi $api) {}

	/**
	 * @param string $initiateUrl URL to the API to initiate the upload
	 * @param string $uploadUrl URL to the API to actually perform the upload.
	 * @param string $visibleName
	 * @param string $mimeType
	 * @param string $localFileName The file name on the current system.
	 * @return void
	 */
	public function upload(string $initiateUrl, string $uploadUrl, string $visibleName, string $mimeType, string $localFileName): void {
		if (!is_readable($localFileName)) {
			throw new \RuntimeException(sprintf('Local file %s is not readable and cannot be uploaded.', $localFileName));
		}
		$this->initiateUpload($initiateUrl, $visibleName, $mimeType, $localFileName);
	}

	private function initiateUpload(string $initiateUrl, string $visibleName, string $mimeType, string $localFileName): void {
		$filesize = filesize($localFileName);
		if ($filesize === false) {
			throw new \RuntimeException(sprintf('File size for local file %s could not be determined in preparation of upload.', $localFileName));
		}
		$response = $this->api->requestRaw($initiateUrl, 'file upload', 'POST', null, [
			RequestOptions::HEADERS => [
				'X-Upload-Content-Type' => $mimeType,
				'X-Upload-Content-Length' => $filesize,
				'X-Upload-File-Name' => $visibleName
			]
		]);
		print_r($response);
	}




}