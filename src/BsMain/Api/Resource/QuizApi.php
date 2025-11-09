<?php /** @noinspection PhpUnused */

namespace BsMain\Api\Resource;

use BsMain\Api\ApiRequest;
use BsMain\Data\Access\UserAccess;
use BsMain\Data\Quiz\QuestionData;
use BsMain\Data\Quiz\QuizAttemptData;
use BsMain\Data\Quiz\QuizData;
use BsMain\Data\Quiz\QuizReadData;

/**
 * API endpoints related to Quizzes, {@see https://docs.valence.desire2learn.com/res/quiz.html}.<br>
 * Completeness: 6 / 18
 */
class QuizApi extends ApiShell {

	public function deleteQuiz(int $courseId, int $quizId): void {
		$this->client->execute(
			ApiRequest::delete()
			->description('quiz')
			->leUrl('%d/quizzes/%d', $courseId, $quizId)
		);
	}
	/**
	 * @param int $courseId
	 * @return QuizReadData[]
	 */
	public function getQuizzesByOrgUnit(int $courseId): array {
		return $this->client->fetchArray(QuizReadData::class,
			ApiRequest::get()
			->description('list of quizzes')
			->leUrl('%d/quizzes/', $courseId)
		);
	}

	public function getQuiz(int $courseId, int $quizId): QuizReadData {
		return $this->client->fetch(QuizReadData::class,
			ApiRequest::get()
			->description('quiz')
			->leUrl('%d/quizzes/%d', $courseId, $quizId)
		);
	}

	/**
	 * Retrieve a list of users with access to a specified quiz.
	 * @param int $courseId
	 * @param int $quizId
	 * @param int|null $userId Optional. Retrieve access for a single user.
	 * @param int|null $roleId Optional. Retrieve access for users with the given role.
	 * @return UserAccess[]
	 */
	public function getUsersWithQuizAccess(int $courseId, int $quizId, ?int $userId = null, ?int $roleId = null): array {
		return $this->client->fetchArray(UserAccess::class,
			ApiRequest::get()
				->description('users with access to quiz')
				->leUrl('%d/quizzes/%d/access/', $courseId, $quizId)
				->param('userId', $userId)
				->param('roleId', $roleId)
		);
	}

	public function createQuiz(int $courseId, QuizData $quizData): QuizReadData {
		return $this->client->fetch(QuizReadData::class,
			ApiRequest::post()
			->description('quiz')
			->leUrl('%d/quizzes/', $courseId)
			->jsonBody(json_encode($quizData))
		);
	}

	/**
	 * @param int $courseId
	 * @param int $quizId
	 * @return QuestionData[]
	 */
	public function getQuizQuestions(int $courseId, int $quizId): array {
		return $this->client->fetchArray(QuestionData::class,
			ApiRequest::get()
			->description('list of quiz questions')
			->leUrl('%d/quizzes/%d/questions/', $courseId, $quizId)
		);
	}

	/**
	 * @param int $courseId
	 * @param int $quizId
 	 * @return QuizAttemptData[]
	 */
	public function getAllQuizAttempts(int $courseId, int $quizId): array {
		return $this->client->fetchArray(QuizAttemptData::class,
			ApiRequest::get()
			->description('list of attempts')
			->leUrl('%d/quizzes/%d/attempts/', $courseId, $quizId)
		);
	}



	/*public function getQuizAttempt(int $courseId, int $quizId, int $userId) {
		$response = $this->request($this->url('/customization/1.0/quizzes/%d/%d/lastquizattempt/%d', $courseId, $quizId, $userId), 'user attempt');
		return \BsMain\Data\LastQuizAttempt::create($response);
	}*/



	/*public function deleteQuiz(int $courseId, int $quizId): void {
		$this->request(
			$this->url('/le/1.67/%d/quizzes/%d', $courseId, $quizId), null, 'the quiz', 'DELETE'
		);
	}
	 */
}
