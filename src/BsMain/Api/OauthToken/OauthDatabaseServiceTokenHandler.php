<?php

namespace BsMain\Api\OauthToken;

use BsMain\Configuration\Configuration;
use BsMain\Exception\BsAppRuntimeException;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Token\AccessTokenInterface;
use PDO;

/**
 * Service token handler, with the token stored in the database..
 */
class OauthDatabaseServiceTokenHandler extends OauthTokenHandler {

	private PDO $connection;

	private const TABLE_NAME = 'service_token';

	public function __construct(AbstractProvider $provider, Configuration $config) {
		$this->connection = self::getDbConnection($config);
		parent::__construct($provider, $config);
	}

	public function retrieveAccessToken(): void {
		$this->setAccessToken(self::getAccessTokenFromDb($this->connection));
	}

	public function refreshAccessToken(): void {
		self::optionallyCreateTable($this->connection);
		$this->connection->beginTransaction();

		try {
			$token = self::getAccessTokenFromDb($this->connection);
			if ($token->getToken() === $this->getCurrentAccessToken()->getToken()) {
				$this->renewTokenWithProvider();
				$this->saveAccessTokenToDb($this->connection, $this->getCurrentAccessToken());
			} else {
				$this->setAccessToken($token);
			}
			$this->connection->commit();
		} catch (\PDOException $e) {
			$this->connection->rollBack();
			throw $e;
		}
	}

	private static function getDbConnection(Configuration $config): PDO {
		$dbConfig = $config->get('db');
		return new PDO(
			$dbConfig['dsn'],
			$dbConfig['username'] ?? null,
			$dbConfig['password'] ?? null,
			$dbConfig['pdo_options'] ?? null);
	}

	private static function optionallyCreateTable(PDO $connection): void {
		$connection->exec(
			sprintf('create table if not exists %1$s ( token text ); insert into %1$s values (\'\');', self::TABLE_NAME)
		);
	}

	private static function getAccessTokenFromDb(PDO $connection): AccessTokenInterface {
		try {
			$stmt = $connection->query(sprintf('select * from %s', self::TABLE_NAME), PDO::FETCH_ASSOC);
			if ($stmt !== false && $stmt->rowCount() > 0) {
				$result = $stmt->fetch();
				return new AccessToken(json_decode($result['token'], true));
			}
			else {
				throw new BsAppRuntimeException('Could not find or read Brightspace service token');
			}
		} catch (\PDOException $e) {
			throw new BsAppRuntimeException('Brightspace service account has not yet been configured or cannot be read.', 0, $e);
		}
	}

	private static function saveAccessTokenToDb(PDO $connection, AccessToken $accessToken): void {
		$stmt = $connection->prepare(sprintf('update %s set token=?', self::TABLE_NAME));
		$stmt->execute([ json_encode($accessToken->jsonSerialize()) ]);
	}

	/**
	 * Registration of access token initialized externally
	 * @param Configuration $config
	 * @param AccessTokenInterface $token
	 * @return void
	 */
	public static function saveAccessToken(Configuration $config, AccessTokenInterface $token): void {
		$connection = self::getDbConnection($config);
		self::optionallyCreateTable($connection);
		self::saveAccessTokenToDb($connection, $token);
	}
}
