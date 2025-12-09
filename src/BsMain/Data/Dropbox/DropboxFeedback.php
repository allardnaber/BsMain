<?php

namespace BsMain\Data\Dropbox;

use BsMain\Data\CompositeRichText;
use BsMain\Data\GenericObject;

/**
 * <p><strong>PLEASE NOTE:</strong> Feedback is a {@see CompositeRichText} object, from which the Html property has the
 * highest priority, Text is used if Html is omitted. This should have been CompositeRichTextInput, but that object type
 * is silently ignored.</p>
 * <p>D2L expects:
 * see https://docs.valence.desire2learn.com/res/dropbox.html#Dropbox.DropboxFolder
 * <pre>{
 * "Score": <number:decimal>|null,
 * "Feedback": { <composite:RichText> },
 * "RubricAssessments": [ // Array of RubricAssessment blocks
 * { <composite:RubricAssessment> },
 * { <composite:RubricAssessment> }, ...
 * ],
 * "IsGraded": <boolean>,
 * "GradedSymbol": <string>|null
 * }</pre></p>
 */
class DropboxFeedback extends GenericObject {

	protected function getAvailableFields(): array {
		return [ 'Score', 'Feedback', 'RubricAssessments', 'IsGraded', 'GradedSymbol' ];
	}
}
