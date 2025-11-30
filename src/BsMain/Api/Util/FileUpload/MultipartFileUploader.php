<?php

namespace BsMain\Api\Util\FileUpload;

use BsMain\Api\ApiRequest;
use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\RequestOptions;

/**
 * File upload helper to attach a file to a request using the multipart options. Known usages are
 * - POST /d2l/api/le/(version)/import/(orgUnitId)/imports/  Upload course package for import.
 * - PUT /d2l/api/le/(version)/(orgUnitId)/content/topics/(topicId)/file Replace topic file
 */
class MultipartFileUploader {

	/**
	 * Adds a file to the multipart options of the request. Updates the existing request, but it should still be
	 * executed.
	 * @param ApiRequest $request
	 * @param string $name The name in the content-disposition header.
	 * @param string $localFileName The local file name, used to read the data from, and used to detect mime type.
	 * @param string|null $contentType Content mime type. Uses mime_content_type if omitted.
	 * @return void
	 */
	public static function addFile(ApiRequest $request, string $name, string $localFileName, ?string $contentType = null): void {
		if ($contentType === null) {
			$contentType = mime_content_type($localFileName) ?? 'application/octet-stream';
		}

		$fileInfo = pathinfo($localFileName);
		$request->addOptionListItem(RequestOptions::MULTIPART, [
			'name' => $name,
			'contents' => Utils::tryFopen($localFileName, 'r'),
			'filename' => $fileInfo['basename'],
			'headers' => [
				'Content-Type' => $contentType
			]
		]);
	}

}
