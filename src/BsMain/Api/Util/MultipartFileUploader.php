<?php

namespace BsMain\Api\Util;

use BsMain\Api\BsResourceBaseApi;
use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\RequestOptions;

/**
 * File upload helper to attach a file to a request using the multipart options. Known usages are
 * - POST /d2l/api/le/(version)/import/(orgUnitId)/imports/  Upload course package for import.
 */
class MultipartFileUploader {

	/**
	 * Adds a file to the multipart options, used in a request. Pass the initial options array (if any) and the resulting
	 * array should be passed to the {@see BsResourceBaseApi::request()} or {@see BsResourceBaseApi::requestArray()}
	 * methods.
	 * @param string $visibleName
	 * @param string $actualFilename
	 * @param string $contentType
	 * @param array $options
	 * @return array
	 */
	public static function addFileToMultipartOptions(string $visibleName, string $actualFilename, string $contentType, array $options = []): array {
		if (!isset($options[RequestOptions::MULTIPART])) {
			$options[RequestOptions::MULTIPART] = [];
		}
		$fileInfo = pathinfo($actualFilename);
		$options[RequestOptions::MULTIPART][] = [
			'name' => $visibleName,
			'contents' => Utils::tryFopen($actualFilename, 'r'),
			'filename' => $fileInfo['basename'],
			'headers' => [
				'Content-Type' => $contentType
			]
		];
		return $options;
	}
}
