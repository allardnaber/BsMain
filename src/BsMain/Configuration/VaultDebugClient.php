<?php

namespace BsMain\Configuration;

use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Vault\Client;
use Vault\Exceptions\RequestException;

class VaultDebugClient extends Client {

	public function send(string $method, string $path, string $body = ''): ResponseInterface
	{
		$headers = [
			'User-Agent' => 'VaultPHP/1.0.0',
			'Content-Type' => 'application/json',
		];

		if ($this->token) {
			$headers['X-Vault-Token'] = $this->token->getAuth()->getClientToken();
		}

		if ($this->namespace) {
			$headers['X-Vault-Namespace'] = $this->getNamespace();
		}

		if (strpos($path, '?') !== false) {
			[$path, $query] = explode('?', $path, 2);
			$this->baseUri = $this->baseUri->withQuery($query);
		}

		$request = $this->requestFactory->createRequest(strtoupper($method), $this->baseUri->withPath($path));

		foreach ($headers as $name => $value) {
			$request = $request->withHeader($name, $value);
		}

		$request = $request->withBody($this->streamFactory->createStream($body));

		$this->logger->debug('Request.', [
			'method' => $method,
			'uri' => $request->getUri(),
			'headers' => $headers,
			'body' => $body,
		]);

		try {
			$response = $this->client->sendRequest($request);

			if ($response->getStatusCode() > 399) {
				echo ' response: ' . $response->getBody()->getContents() . "<BR>\n";
				echo ' code: ' . $response->getStatusCode();
				throw new RequestException(
					'Bad status received from Vault',
					$response->getStatusCode(),
					null,
					$request
				);
			}
		} catch (ClientExceptionInterface $e) {
			$this->logger->error('Something went wrong when calling Vault.', [
				'code' => $e->getCode(),
				'message' => $e->getMessage(),
			]);

			$this->logger->debug('Trace.', ['exception' => $e]);

			throw new RequestException($e->getMessage(), $e->getCode(), $e, $request);
		}

		$this->logger->debug('Response.', [
			'statusCode' => $response->getStatusCode(),
			'reasonPhrase' => $response->getReasonPhrase(),
			'headers ' => $response->getHeaders(),
			'body' => $response->getBody()->getContents(),
		]);

		return $response;
	}

}