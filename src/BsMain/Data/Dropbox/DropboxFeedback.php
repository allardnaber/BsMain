<?php

namespace BsMain\Data\Dropbox;

use BsMain\Data\GenericObject;

/**
 * D2L expects:
 * see https://docs.valence.desire2learn.com/res/dropbox.html#Dropbox.DropboxFolder
 * {
 * "Score": <number:decimal>|null,
 * "Feedback": { <composite:RichText> },
 * "RubricAssessments": [ // Array of RubricAssessment blocks
 * { <composite:RubricAssessment> },
 * { <composite:RubricAssessment> }, ...
 * ],
 * "IsGraded": <boolean>,
 * "GradedSymbol": <string>|null
 * }
 */
class DropboxFeedback extends GenericObject {

	protected function getAvailableFields(): array {
		return [ 'Score', 'Feedback', 'RubricAssessments', 'IsGraded', 'GradedSymbol' ];
	}
}
