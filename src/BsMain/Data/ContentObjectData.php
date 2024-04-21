<?php

namespace BsMain\Data;

/*
 * D2L expects:
 * see https://docs.valence.desire2learn.com/res/content.html#Content.ContentObjectData
 * {
Description
This property is optional and may be empty or null. Note also that it uses the RichTextInput structure type.

ShortTitle
This field may be empty or null.

Title
This field may not be empty, null, or consist only of white-space characters.

Type
In modules, this property has the value 0; in topics, this property has the value 1.

Url
In link-type topics (TopicType value 3), this property should be the URL you want to fetch when the user opens the link-type topic.

In file-type topics (TopicType value 1), this property should contain a new path that is valid within the course offering’s content space and indicate where the system should place the file, for example:

/content/enforced/8083-EXT-101/test_topic_file.pdf
*/
class ContentObjectData extends GenericObject {

	protected function getAvailableFields(): array {
		return [ 'Description', 'ShortTitle', 'Title', 'Type', 'IsHidden', 'IsLocked' ];
	}
}
