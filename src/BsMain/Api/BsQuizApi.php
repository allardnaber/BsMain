<?php

namespace BsMain\Api;

use BsMain\Data\QuestionData;
use BsMain\Data\Quiz\QuizAttemptData;
use BsMain\Data\Quiz\QuizReadData;

class BsQuizApi extends BsResourceBaseApi {

	/**
	 * @param int $courseId
	 * @return QuizReadData[]
	 */
	public function getQuizzes(int $courseId): array {
		return $this->requestArray($this->url('/le/1.75/%d/quizzes/', $courseId),
			QuizReadData::class, true, 'list of quizzes');
	}

	public function getQuiz(int $courseId, int $quizId): QuizReadData {
		return $this->request($this->url('/le/1.51/%d/quizzes/%d', $courseId, $quizId), QuizReadData::class, 'the quiz');
	}

	/**
	 * @param int $courseId
	 * @param int $quizId
	 * @return QuizAttemptData[]
	 */
	public function getQuizAttempts(int $courseId, int $quizId): array {
		return $this->requestArray($this->url('/le/1.51/%d/quizzes/%d/attempts/', $courseId, $quizId),
			QuizAttemptData::class, true, 'list of attempts');
	}

	/**
	 * @param int $courseId
	 * @param int $quizId
	 * @return QuestionData[]
	 */
	public function getQuizQuestions(int $courseId, int $quizId): array {
		return $this->requestArray($this->url('/le/1.51/%d/quizzes/%d/questions/', $courseId, $quizId),
			QuestionData::class, true, 'quiz questions');
	}

	/*public function getQuizAttempt(int $courseId, int $quizId, int $userId) {
		$response = $this->request($this->url('/customization/1.0/quizzes/%d/%d/lastquizattempt/%d', $courseId, $quizId, $userId), 'user attempt');
		return \BsMain\Data\LastQuizAttempt::create($response);
	}*/



	public function deleteQuiz(int $courseId, int $quizId): void {
		$this->request(
			$this->url('/le/1.67/%d/quizzes/%d', $courseId, $quizId), null, 'the quiz', 'DELETE'
		);
	}

}
