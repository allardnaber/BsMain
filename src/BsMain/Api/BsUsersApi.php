<?php

namespace BsMain\Api;

use BsMain\Data\CreateUserData;
use BsMain\Data\Locale;
use BsMain\Data\Role;
use BsMain\Data\UserData;
use BsMain\Exception\BsAppApiException;

class BsUsersApi extends BsResourceBaseApi {

	/**
	 * @return UserData[]
	 */
	public function findUsersByEmail(string $email): array {
		try {
			return $this->requestArray($this->url('/lp/1.33/users/?externalEmail=%s', $email),
				UserData::class, false, 'users');
		} catch (BsAppApiException $e) {
			if ($e->getStatusCode() === 404) {
				return [];
			}
			throw $e;
		}
	}

	public function getUser(string $username): UserData {
		return $this->request(
			$this->url('/lp/1.33/users/?username=%s', $username),
			UserData::class, 'the user');
	}

	public function createUser(CreateUserData $data): UserData {
		return $this->request(
			$this->url('/lp/1.33/users/'),
			UserData::class, 'the user',
			'POST', $data->getJson(true)
		);
	}

	public function setUserPassword(int $userId, string $password): void {
		$this->request(
			$this->url('/lp/1.33/users/%d/password', $userId), null, 'the password',
			'PUT', json_encode(['Password' => $password])
		);
	}

	public function getMyLocale(): Locale {
		return $this->request(
			$this->url('/lp/1.31/accountSettings/mySettings/locale/'),
			Locale::class, 'your preferred language');
	}

	/**
	 * @param int $orgUnit
	 * @return Role[]
	 */
	public function getRolesInOrgUnit(int $orgUnit): array {
		return $this->requestArray($this->url('/lp/1.31/%d/roles/', $orgUnit),
			Role::class, false, 'available roles');
	}


}