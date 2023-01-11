<?php

namespace BsMain\Api;

use BsMain\Data\UserData;

class BsUsersApi extends BsResourceBaseApi {

	public function getUser(string $username): UserData {
		$response = $this->request($this->url('/lp/1.33/users/?username=%s', $username), 'the user');
		return new UserData($response);
	}


}