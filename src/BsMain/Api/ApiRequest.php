<?php /** @noinspection PhpGetterAndSetterCanBeReplacedWithPropertyHooksInspection */

namespace BsMain\Api;

use BsMain\Data\ApiEntity;
use GuzzleHttp\Psr7\Utils;
use GuzzleHttp\RequestOptions;
use RuntimeException;

class ApiRequest {

	public const array API_VERSION = [
		'lp' => '1.53',
		'le' => '1.88'
	];

	private const string API_PREFIX = '/d2l/api';

	private string $url;
	private string $description = 'Brightspace data';
	private ?string $jsonData = null;
	private array $options = [];

	public function __construct(
		private readonly RequestMethod $method,
		private readonly ?string $classname = null
	) {
		if ($this->classname !== null && !is_subclass_of($this->classname, ApiEntity::class)) {
			throw new RuntimeException(sprintf('Class %s must be a subclass of ApiEntity.', $this->classname));
		}
	}

	public static function get(): self { return new ApiRequest(RequestMethod::GET); }
	public static function post(): self { return new ApiRequest(RequestMethod::POST); }
	public static function put(): self { return new ApiRequest(RequestMethod::PUT); }
	public static function delete(): self { return new ApiRequest(RequestMethod::DELETE); }

	/**
	 * Generates the full API url. This method accepts the standard printf format
	 * and url-encodes the parameters.
	 * @param string $url Base URL with placeholders
	 * @param string[] $values The values to put in the placeholder
	 * @return self
	 */
	public function url(string $url, string ...$values): self {
		$safeValues = array_map('urlencode', $values);
		$this->url = vsprintf($url, $safeValues);
		return $this;
	}

	/**
	 * Generates a Learning Platform url with current version number included.
 	 * @param string $url
	 * @param string ...$values
	 * @return self
	 */
	public function lpUrl(string $url, string ...$values): self {
		return $this->serviceUrl('lp', $url, ...$values);
	}

	/**
	 * Generates a Learning Environment url with current version number included.
	 * @param string $url
	 * @param string ...$values
	 * @return self
	 */
	public function leUrl(string $url, string ...$values): self {
		return $this->serviceUrl('le', $url, ...$values);
	}

	/**
	 * Helper function to generate API service url with version number included,
	 * e.g. converts 'courses/123' for service 'lp' into https://host.brightspace.com/d2l/api/lp/1.53/courses/123
	 * @param string $service
	 * @param string $url
	 * @param string ...$values
	 * @return self
	 */
	private function serviceUrl(string $service, string $url, string ...$values): self {
		$resultUrl = join('/', [ self::API_PREFIX, $service, self::API_VERSION[$service], $url ]);
		return $this->url($resultUrl, ...$values);
	}

	/**
	 * @param string $description
	 * @return self
	 */
	public function description(string $description): self {
		$this->description = $description;
		return $this;
	}

	/**
	 * @param string $json
	 * @return self
	 */
	public function jsonBody(string $json): self {
		$this->jsonData = $json;
		return $this;
	}

	/**
	 * Append provided query parameter to the url, if value is not null.
	 * @param string $name
	 * @param string|null $value
	 * @return self
	 */
	public function param(string $name, ?string $value): self {
		if (!isset($this->url)) {
			throw new RuntimeException('Set the request url before appending parameters.');
		}
		if ($value !== null) {
			$queryStart = str_contains($this->url, '?') ? '&' : '?';
			$this->url .= $queryStart . $name . '=' . urlencode($value);
		}
		return $this;
	}

	public function getMethod(): RequestMethod {
		return $this->method;
	}

	public function getClassname(): ?string {
		return $this->classname;
	}

	public function getUrl(): string {
		return $this->url;
	}

	public function getDescription(): string {
		return $this->description;
	}

	public function getJsonData(): ?string {
		return $this->jsonData;
	}

	public function getOptions(): array {
		return $this->options;
	}

	public function setOption(string $name, mixed $value):  void {
		$this->options[$name] = $value;
	}

	public function addOptionListItem(string $name, mixed $value): void {
		if (!isset($this->options[$name])) {
			$this->options[$name] = [];
		}
		$this->options[$name][] = $value;
	}


}
