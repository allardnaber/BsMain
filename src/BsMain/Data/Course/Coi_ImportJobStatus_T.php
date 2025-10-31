<?php

namespace BsMain\Data\Course;

enum Coi_ImportJobStatus_T {

	/**
	 * Submission of import job to adaptation process.
	 */
	case UPLOADING;

	/**
	 * Currently in adaptation process for importing.
	 */
	case PROCESSING;

	/**
	 * Finished adaptation process.
	 */
	case PROCESSED;

	/**
	 * Currently importing.
	 */
	case IMPORTING;

	/**
	 * Import failure.
	 */
	case IMPORTFAILED;

	/**
	 * Import job completed successfully.
	 */
	case COMPLETED;


	/**
	 * Undocumented state
	 */
	case READYTOIMPORTNATIVELY;

	/**
	 * Undocumented state
	 */
	case Timeout;

}
