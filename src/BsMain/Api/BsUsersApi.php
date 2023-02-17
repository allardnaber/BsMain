<?php

namespace BsMain\Api;

use BsMain\Data\Locale;
use BsMain\Data\UserData;

class BsUsersApi extends BsResourceBaseApi {

	public function getUser(string $username): UserData {
		return $this->request(
			$this->url('/lp/1.33/users/?username=%s', $username),
			UserData::class, 'the user');
	}

	public function getMyLocale(): Locale {
		return $this->request(
			$this->url('/lp/1.31/accountSettings/mySettings/locale/'),
			Locale::class, 'your preferred language');
	}


}