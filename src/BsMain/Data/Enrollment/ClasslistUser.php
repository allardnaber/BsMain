<?php

namespace BsMain\Data\Enrollment;

use BsMain\Data\ApiEntity;

/**
 * {@see https://docs.valence.desire2learn.com/res/enroll.html#Enrollment.ClasslistUser}
 */
class ClasslistUser extends ApiEntity {

	public string $Identifier;
	public string $ProfileIdentifier;
	public string $DisplayName;
	public ?string $Username;
	public ?string $OrgDefinedId;
	public ?string $Email;
	public ?string $FirstName;
	public ?string $LastName;
	public ?int $RoleId;
	public ?string $LastAccessed;
	public bool $IsOnline;
	public string $ClasslistRoleDisplayName;
	public ?string $Pronouns;

}
