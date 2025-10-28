<?php

namespace BsMain\Api\Util;

use BsMain\Api\BsApiClient;
use BsMain\Api\BsResourceBaseApi;
use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\RequestOptions;

/**
 * File upload helper to send a file using the resumable file upload method,
 * {@see https://docs.valence.desire2learn.com/basic/fileupload.html#resumable-uploads}.
 *
 * Known usages:
 * - POST /d2l/api/le/(version)/(orgUnitId)/dropbox/folders/(folderId)/feedback/(entityType)/(entityId)/attach
 *      Upload feedback to an assignment submission.
 */
class ResumableFileUploader extends BsResourceBaseApi {

	public const int MAX_CHUNK_SIZE_KB = -1;

	public function __construct(private readonly BsApiClient $client) {
		parent::__construct($client);
	}

	/**
	 * @param string $initiateUrl URL to the API to initiate the upload
	 * @param string $attachUrl URL to the API to actually perform the upload.
	 * @param string $visibleName
	 * @param string $mimeType
	 * @param string $localFileName The file name on the current system.
	 * @return void
	 */
	public function upload(string $initiateUrl, string $attachUrl, string $visibleName, string $mimeType, string $localFileName): void {
		if (!is_readable($localFileName)) {
			throw new \RuntimeException(sprintf('Local file %s is not readable and cannot be uploaded.', $localFileName));
		}
		$filesize = filesize($localFileName);
		if ($filesize === false) {
			throw new \RuntimeException(sprintf('File size for local file %s could not be determined in preparation of upload.', $localFileName));
		}
		$uploadPath = $this->initiateUpload($initiateUrl, $visibleName, $mimeType, $filesize);
		$this->performUpload($uploadPath, $mimeType, $filesize, $localFileName);

		$pathTokens = explode('/', $uploadPath);
		$fileKey = array_pop($pathTokens);
		$this->attachUploadedFile($attachUrl, $fileKey, $visibleName);
	}

	/**
	 * Initiates the file upload and return the associated file upload key.
	 * @param string $initiateUrl
	 * @param string $visibleName
	 * @param string $mimeType
	 * @param int $filesize
	 * @return string Path to upload the file to, which also contains the upload key.
	 */
	private function initiateUpload(string $initiateUrl, string $visibleName, string $mimeType, int $filesize): string {
		// Should return a response with a header like:
		// Location = [ /d2l/upload/m8zFEpk6Lr]    (array with single value)
		$response = $this->requestRaw($initiateUrl, 'file upload', 'POST', null,
			[
				RequestOptions::HEADERS => [
					'X-Upload-Content-Type' => $mimeType,
					'X-Upload-Content-Length' => $filesize,
					'X-Upload-File-Name' => $visibleName
				],
				RequestOptions::ALLOW_REDIRECTS => false // to prevent following the Location header!
			]);
		$uploadPath = $response->getHeader('Location')[0] ?? null;
		if ($uploadPath === null) {
			throw new \RuntimeException(sprintf('Received unexpected upload path "%s" for this upload action.', $uploadPath));
		}
		return $uploadPath;
	}

	private function performUpload(string $uploadPath, string $mimeType, int $filesize, string $localFileName): void {
		$domain = $this->client->getConfig('brightspace', 'url');
		$url = $domain . $uploadPath;
		$this->requestRaw($url, 'file data', 'POST', null,
			[
				RequestOptions::HEADERS => [
					'Content-Type' => $mimeType,
					'Content-Range' => sprintf('bytes %d-%d/%d', 0, $filesize - 1, $filesize)
				],
				RequestOptions::BODY => Utils::tryFopen($localFileName, 'r')
			]
		);
	}

	private function attachUploadedFile(string $attachUrl, string $fileKey, string $visibleName): void {
		$this->requestRaw($attachUrl, 'file attachment', 'POST', null,
		[
			RequestOptions::FORM_PARAMS => [
				'fileKey' => $fileKey,
				'fileName' => $visibleName
			]
		]);
	}

}
