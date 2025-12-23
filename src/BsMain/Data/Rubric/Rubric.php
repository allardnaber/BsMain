<?php

namespace BsMain\Data\Rubric;

use BsMain\Api\Fields\Attributes\ArrayOf;
use BsMain\Data\ApiEntity;
use BsMain\Data\RichText;

/**
 * @see https://docs.valence.desire2learn.com/res/assessment.html#Rubric.Rubric
 */
class Rubric extends ApiEntity {
	public int $RubricId;
	public string $Name;
	public RichText $Description;
	public Rubric_T $RubricType;
	public Scoring_M $ScoringMethod;

	/** @var CriteriaGroup[] */
	#[ArrayOf(CriteriaGroup::class)]
	public array $CriteriaGroups;

	/** @var OverallLevel[] */
	#[ArrayOf(OverallLevel::class)]
	public array $OverallLevels;

	// not documented
	public bool $IsScoreVisibleToAssessedUsers;
}
