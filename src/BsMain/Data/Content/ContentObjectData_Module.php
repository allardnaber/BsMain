<?php

namespace BsMain\Data\Content;

use BsMain\Data\Content_T;

class ContentObjectData_Module extends ContentObjectData {

	public ?string $ModuleStartDate; // @todo
	public ?string $ModuleEndDate; // @todo
	public ?string $ModuleDueDate; // @todo

	public function onCreate(): void {
		$this->Type = Content_T::Module;
	}
}
