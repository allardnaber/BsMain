<?php

namespace BsMain\Data\Dropbox;

use BsMain\Api\Fields\Attributes\ArrayOf;
use BsMain\Data\ApiEntity;
use BsMain\Data\File;
use BsMain\Data\Link;
use BsMain\Data\RichText;
use DateTimeInterface;

/**
 * {@see https://docs.valence.desire2learn.com/res/dropbox.html#Dropbox.DropboxFolder}
 */
class DropboxFolder extends ApiEntity {

	public int $Id;
	public ?int $CategoryId;
	public string $Name;
	public RichText $CustomInstructions;

	/** @var File[] */
	#[ArrayOf(File::class)]
	public array $Attachments;
	public int $TotalFiles;
	public int $UnreadFiles;
	public int $FlaggedFiles;
	public int $TotalUsers;
	public int $TotalUsersWithSubmissions;
	public int $TotalUsersWithFeedback;
	public ?DropboxAvailability $Availability;

	/** @var int|null If the property is present and not null, the dropbox folder is a group-submission folder;
	 * otherwise, the dropbox folder is an individual-submission folder. If the property is present with a non-null
	 * value, it contains a D2LID for the group’s GroupCategoryId.
	 */
	public ?int $GroupTypeId;
	public ?DateTimeInterface $DueDate;
	public bool $DisplayInCalendar;
	public DropboxAssessment $Assessment;

	/** @var string|null A comma separated list of email addresses which will be sent a notification when any files are
	 * submitted to the folder. If this field is empty or null, no notification will be sent. */
	public ?string $NotificationEmail;
	public bool $IsHidden;
	/** @var Link[] */
	#[ArrayOf(Link::class)]
	public array $LinkAttachments;
	public ?string $ActivityId;
	public bool $IsAnonymous;
	public DropboxType_T $DropboxType;
	public SubmissionType_T $SubmissionType;
	public DropboxCompletionType_T $CompletionType;
	public ?int $GradeItemId;
	public ?bool $AllowOnlyUsersWithSpecialAccess;
}
