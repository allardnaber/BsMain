<?php /** @noinspection PhpUnused */

namespace BsMain\Data\Toc;

use BsMain\Data\ApiEntity;

class Module extends ApiEntity {

	public int $ModuleId;
	public string $Title;
	public int $SortOrder;
	public bool $IsHidden;
	public $DefaultPath;
	public $Description;

	/**
	 * @var Module[]
	 */
	public array $Modules;

	/**
	 * @var Topic[]
	 */
	public array $Topics;


	protected function onCreate(): void {
		$this->Modules = array_map(fn($o) => Module::newInstance($o), $this->Modules);
		$this->Topics = array_map(fn($o) => Topic::newInstance($o), $this->Topics);
	}
}
