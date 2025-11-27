<?php

namespace BsMain\Data\Content;

use BsMain\Data\ApiEntity;
use BsMain\Data\Content_T;
use BsMain\Data\RichText;
use BsMain\Exception\BrightspaceException;

/**
 * ContentObject is the combined object for modules and topics. Common properties are in this object, more specific
 * properties are in the overloaded classes ContentObjectModule and ContentObjectTopic. The {@see instance}
 * method differentiates between the two.
 */
abstract class ContentObject extends ApiEntity {

	public bool $IsLocked;
	public Content_T $Type;
	public bool $IsHidden;
	public int $Id;
	public string $Title;
	public string $ShortTitle;
	public ?RichText $Description;
	public ?string $LastModifiedDate;

	public static function getSubClass(?array $props): ?string {
		if ($props === null || !isset($props['Type'])) {
			throw new BrightspaceException('Cannot create ContentObject instance if Type is undefined.');
		}
		return match ($props['Type']) {
			Content_T::Module->value => ContentObject_Module::class,
			Content_T::Topic->value => ContentObject_Topic::class,
			default => throw new BrightspaceException(sprintf(
					'Content object type %d is unknown, it should be a value from the Content_T enum.', $props['Type'])
			),
		};
	}

}
