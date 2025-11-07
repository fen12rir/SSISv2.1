<?php
declare(strict_types=1);
require_once __DIR__ . '/../models/teacherDashboardModel.php';
require_once __DIR__ . '/../../Exceptions/DatabaseException.php';

class teacherDashboardController {
    protected $model;

    public function __construct() {
        $this->model = new teacherDashboardModel();
    }

    public function getDashboardData(int $staffId): array {
        try {
            $subjectsCount = $this->model->getSubjectsCount($staffId);
            $studentsCount = $this->model->getTotalStudentsToGrade($staffId);
            $lockerFilesCount = $this->model->getLockerFilesCount($staffId);
            $isAdviser = $this->model->isAdviser($staffId);
            $advisorySectionId = $isAdviser ? $this->model->getAdvisorySectionId($staffId) : null;

            return [
                'success' => true,
                'data' => [
                    'subjects_count' => $subjectsCount,
                    'students_count' => $studentsCount,
                    'locker_files_count' => $lockerFilesCount,
                    'is_adviser' => $isAdviser,
                    'advisory_section_id' => $advisorySectionId
                ]
            ];
        }
        catch(DatabaseException $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
        catch(Exception $e) {
            return [
                'success' => false,
                'message' => 'An error occurred while fetching dashboard data'
            ];
        }
    }
}

