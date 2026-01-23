<?php

namespace BsMain\Api\OauthToken;

use BsMain\Configuration\Configuration;
use BsMain\Exception\BsAppRuntimeException;
use GuzzleHttp\Exception\GuzzleException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use PDO;
use PDOException;
use Sald\Connection\ConnectionManager;

/**
 * Service token handler, with the token stored in the database.
 */
class OauthDatabaseServiceTokenHandler extends OauthServiceTokenHandler {

	private const DEFAULT_TABLE_NAME = 'service_token';

	private PDO $connection;
	private string $tableName;

	public function __construct(BrightspaceProvider $provider, Configuration $config, ServiceAuthType $authType) {
		parent::__construct($provider, $config, $authType);
		$this->connection = ConnectionManager::get();
		$this->tableName = $config->getOptional('oauth2', 'serviceTokenTableName') ?? self::DEFAULT_TABLE_NAME;
	}

	/**
	 * @throws GuzzleException
	 * @throws IdentityProviderException
	 */
	public function refreshAccessToken(): void {
		$this->connection->beginTransaction();

		try {
			// lock table to be sure we are the only one to refresh the service token
			$this->connection->exec(sprintf('lock table %s in row exclusive mode', $this->tableName));

			$storedToken = $this->getStoredServiceToken(true);
			if ($storedToken->getToken() === $this->getCurrentAccessToken()?->getToken()) {
				// if the stored token has not been renewed yet, renew
				$this->renewTokenWithProvider();
				$this->saveAccessTokenToDb($this->getCurrentAccessToken());
				$this->connection->commit();
			} else {
				// otherwise, use the new token.
				$this->setAccessToken($storedToken);
				$this->connection->rollBack();
			}
		} catch (PDOException $e) {
			$this->connection->rollBack();
			throw $e;
		}
	}

	private function optionallyCreateTable(): void {
		$this->connection->exec(
			sprintf('create table if not exists %1$s ( token text ); insert into %1$s (token) select \'\' where not exists (select * from %1$s)', $this->tableName)
		);
	}

	protected function getStoredServiceToken(bool $withLock = false): ?AccessTokenInterface {
		try {
			$stmt = $this->connection->query(sprintf('select * from %s %s', $this->tableName, $withLock ? ' for update' : ''), PDO::FETCH_ASSOC);
			if ($stmt !== false && $stmt->rowCount() > 0) {
				$result = $stmt->fetch();
				$tokenArr = json_decode($result['token'] ?? '', true);
				if ($tokenArr === null) {
					throw new BsAppRuntimeException(
						sprintf('Could not read service token: [%d] %s', json_last_error(), json_last_error_msg()));
				}
				return new AccessToken($tokenArr);
			}
			return null;

		} catch (PDOException $e) {
			throw new BsAppRuntimeException('Brightspace service account could not be read from database.', 0, $e);
		}
	}

	private function saveAccessTokenToDb(AccessTokenInterface $accessToken): void {
		$stmt = $this->connection->prepare(sprintf('update %s set token=?', $this->tableName));
		$stmt->execute([ json_encode($accessToken->jsonSerialize()) ]);
	}

	protected function storeServiceToken(AccessTokenInterface $serviceToken): void {
		$this->optionallyCreateTable();
		$this->saveAccessTokenToDb($serviceToken);
	}
}
