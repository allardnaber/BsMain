<?php

namespace BsMain\Data;

/**
 * D2L returns:
 * see https://docs.valence.desire2learn.com/res/grade.html#Grade.GradeObject
 * If Numeric {
 * "MaxPoints": <number:decimal>,
 * "CanExceedMaxPoints": <boolean>,
 * "IsBonus": <boolean>,
 * "ExcludeFromFinalGradeCalculation": <boolean>,
 * "GradeSchemeId": <number:D2LID>|null,
 * "Id": <number:D2LID>,  // Not on input actions
 * "Name": <string>,
 * "ShortName": <string>,
 * "GradeType": "Numeric",
 * "CategoryId": <number:D2LID>|null,
 * "Description": { <composite:RichText> },  // { <composite:RichTextInput> } on input actions
 * "GradeSchemeUrl": <string:APIURL>,  // Not on input actions
 * "Weight": <number:decimal>,  // Not on input actions
 * "AssociatedTool": { <composite:AssociatedTool> }|null,
 * "IsHidden": <bool>
 * }
 *
 * If PassFail {
 * "MaxPoints": <number:decimal>,
 * "IsBonus": <boolean>,
 * "ExcludeFromFinalGradeCalculation": <boolean>,
 * "GradeSchemeId": <number:D2LID>|null,
 * "Id": <number:D2LID>,  // Not on input actions
 * "Name": <string>,
 * "ShortName": <string>,
 * "GradeType": "PassFail",
 * "CategoryId": <number:D2LID>|null,
 * "Description": { <composite:RichText> },  // { <composite:RichTextInput> } on input actions
 * "Weight": <number:decimal>,  // Not on input actions
 * "GradeSchemeUrl": <string:APIURL>,  // Not on input actions
 * "AssociatedTool": { <composite:AssociatedTool> }|null,
 * "IsHidden": <bool>
 * }
 *
 * If Selectbox {
 * "MaxPoints": <number:decimal>,
 * "IsBonus": <boolean>,
 * "ExcludeFromFinalGradeCalculation": <boolean>,
 * "GradeSchemeId": <number:D2LID>,  // Cannot be null on input actions
 * "Id": <number:D2LID>,  // Not on input actions
 * "Name": <string>,
 * "ShortName": <string>,
 * "GradeType": "SelectBox",
 * "CategoryId": <number:D2LID>|null,
 * "Description": { <composite:RichText> },  // { <composite:RichTextInput> } on input actions
 * "Weight": <number:decimal>,  // Not on input actions
 * "GradeSchemeUrl": <string:APIURL>,  // Not on input actions
 * "AssociatedTool": { <composite:AssociatedTool> }|null,
 * "IsHidden": <bool>
 * }
 *
 * If Text {
 * "Id": <number:D2LID>,  // Not on input actions
 * "Name": <string>,
 * "ShortName": <string>,
 * "GradeType": "Text",
 * "CategoryId": <number:D2LID>|null,
 * "Description": { <composite:RichText> },  // { <composite:RichTextInput> } on input actions
 * "Weight": <number:decimal>,  // Not on input actions
 * "AssociatedTool": { <composite:AssociatedTool> }|null,
 * "IsHidden": <bool>
 * }
 */

class GradeObject extends GenericObject {

	protected function getAvailableFields(): array {
		// only fields that are available for all grade item types
		return [ 'Id', 'Name', 'ShortName', 'GradeType' ];
	}
}
