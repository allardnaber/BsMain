<?php

namespace BsMain\Api;

use BsMain\Data\Locale;
use BsMain\Data\UserData;

class BsUsersApi extends BsResourceBaseApi {

	public function getUser(string $username): UserData {
		return new UserData(
			$this->request($this->url('/lp/1.33/users/?username=%s', $username), 'the user')
		);
	}

	public function getMyLocale(): Locale {
		return new Locale(
			$this->request($this->url('/lp/1.31/accountSettings/mySettings/locale/'), 'your preferred language')
		);
	}


}