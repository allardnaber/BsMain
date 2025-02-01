<?php

namespace BsMain\Api\OauthToken;

use BsMain\Configuration\Configuration;
use BsMain\Exception\BsAppRuntimeException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use PDO;

/**
 * Service token handler, with the token stored in the database.
 */
class OauthDatabaseServiceTokenHandler extends OauthServiceTokenHandler {

	private const DEFAULT_TABLE_NAME = 'service_token';

	private PDO $connection;
	private string $tableName;

	public function __construct(AbstractProvider $provider, Configuration $config) {
		parent::__construct($provider, $config);
		$this->connection = $this->getDbConnection();
		$this->tableName = $config->getOptional('oauth2', 'serviceTokenTableName') ?? self::DEFAULT_TABLE_NAME;
	}

	public function retrieveAccessToken(): void {
		$this->setAccessToken($this->getAccessTokenFromDb());
	}

	public function refreshAccessToken(): void {
		$this->connection->beginTransaction();

		try {
			$token = $this->getAccessTokenFromDb();
			if ($token->getToken() === $this->getCurrentAccessToken()->getToken()) {
				$this->renewTokenWithProvider();
				$this->saveAccessTokenToDb($this->getCurrentAccessToken());
			} else {
				$this->setAccessToken($token);
			}
			$this->connection->commit();
		} catch (\PDOException $e) {
			$this->connection->rollBack();
			throw $e;
		}
	}

	private function getDbConnection(): PDO {
		$dbConfig = $this->getFullConfig()->get('db');
		return new PDO(
			$dbConfig['dsn'],
			$dbConfig['username'] ?? null,
			$dbConfig['password'] ?? null,
			$dbConfig['pdo_options'] ?? null);
	}

	private function optionallyCreateTable(): void {
		$this->connection->exec(
			sprintf('create table if not exists %1$s ( token text ); insert into %1$s (token) select \'\' where not exists (select * from %1$s)', $this->tableName)
		);
	}

	private function getAccessTokenFromDb(): AccessTokenInterface {
		try {
			$stmt = $this->connection->query(sprintf('select * from %s', $this->tableName), PDO::FETCH_ASSOC);
			if ($stmt !== false && $stmt->rowCount() > 0) {
				$result = $stmt->fetch();
				return new AccessToken(json_decode($result['token'], true));
			}
			else {
				throw new BsAppRuntimeException('Brightspace service account has not yet been configured or cannot be read.');
			}
		} catch (\PDOException $e) {
			throw new BsAppRuntimeException('Brightspace service account has not yet been configured or cannot be read.', 0, $e);
		}
	}

	private function saveAccessTokenToDb(AccessTokenInterface $accessToken): void {
		$stmt = $this->connection->prepare(sprintf('update %s set token=?', $this->tableName));
		$stmt->execute([ json_encode($accessToken->jsonSerialize()) ]);
	}

	public function setServiceToken(AccessTokenInterface $serviceToken): void {
		$this->optionallyCreateTable();
		$this->saveAccessTokenToDb($serviceToken);
	}
}
