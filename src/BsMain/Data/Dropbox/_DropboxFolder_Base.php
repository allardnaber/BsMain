<?php

namespace BsMain\Data\Dropbox;

use BsMain\Data\ApiEntity;
use DateTimeInterface;

class _DropboxFolder_Base extends ApiEntity {

	public ?int $CategoryId;
	public string $Name;
	public ?DropboxAvailability $Availability;

	/** @var int|null If the property is present and not null, the dropbox folder is a group-submission folder;
	 * otherwise, the dropbox folder is an individual-submission folder. If the property is present with a non-null
	 * value, it contains a D2LID for the group’s GroupCategoryId.
	 */
	public ?int $GroupTypeId;
	public ?DateTimeInterface $DueDate;
	public bool $DisplayInCalendar;

	/** @var string|null A comma separated list of email addresses which will be sent a notification when any files are
	 * submitted to the folder. If this field is empty or null, no notification will be sent. */
	public ?string $NotificationEmail;
	public bool $IsHidden;
	public bool $IsAnonymous;
	public DropboxType_T $DropboxType;
	public SubmissionType_T $SubmissionType;
	public DropboxCompletionType_T $CompletionType;
	public ?int $GradeItemId;
	public ?bool $AllowOnlyUsersWithSpecialAccess;
}
