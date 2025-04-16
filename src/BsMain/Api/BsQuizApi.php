<?php

namespace BsMain\Api;

use BsMain\Data\QuizReadData;

class BsQuizApi extends BsResourceBaseApi {

	/**
	 * @param int $courseId
	 * @return QuizReadData[]
	 */
	public function getQuizzes(int $courseId): array {
		return $this->requestArray($this->url('/le/1.75/%d/quizzes/', $courseId),
			QuizReadData::class, true, 'list of quizzes');
	}

	public function deleteQuiz(int $courseId, int $quizId): void {
		$this->request(
			$this->url('/le/1.67/%d/quizzes/%d', $courseId, $quizId), null, 'the quiz', 'DELETE'
		);
	}

}
