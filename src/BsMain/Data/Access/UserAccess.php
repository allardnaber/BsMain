<?php

namespace BsMain\Data\Access;

use BsMain\Data\ApiEntity;

/**
 * see https://docs.valence.desire2learn.com/res/apiprop.html#Access.UserAccess
 */
class UserAccess extends ApiEntity {

	public int $UserId;
	public bool $HasAccess;

}
