<?php
declare(strict_types=1);
require_once __DIR__ . '/../../Exceptions/DatabaseException.php';
require_once __DIR__ . '/../models/adminLockerModel.php';

class adminLockerController {
    protected $lockerModel;

    public function __construct() {
        $this->lockerModel = new adminLockerModel();
    }

    public function apiGetAllFiles(int $staffId) : array {
        try {
            $data = $this->lockerModel->getAllFiles($staffId);
            return [
                'httpcode' => 200,
                'success' => true,
                'message' => 'Files fetched successfully',
                'data' => $data
            ];
        }
        catch(DatabaseException $e) {
            return [
                'httpcode' => 500,
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage(),
                'data' => []
            ];
        }
        catch(Exception $e) {
            return [
                'httpcode' => 400,
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    public function apiUploadFile(int $staffId, array $file, ?string $description) : array {
        try {
            if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
                return [
                    'httpcode' => 400,
                    'success' => false,
                    'message' => 'No file uploaded',
                    'data' => []
                ];
            }

            // Validate file size (max 50MB)
            $maxSize = 50 * 1024 * 1024; // 50MB
            if ($file['size'] > $maxSize) {
                return [
                    'httpcode' => 400,
                    'success' => false,
                    'message' => 'File size exceeds 50MB limit',
                    'data' => []
                ];
            }

            // Get file extension and validate
            $originalFileName = $file['name'];
            $fileExt = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
            
            // Allowed file types
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf', 'doc', 'docx', 'ppt', 'pptx', 'xls', 'xlsx', 'txt', 'zip', 'rar'];
            
            if (!in_array($fileExt, $allowedTypes)) {
                return [
                    'httpcode' => 400,
                    'success' => false,
                    'message' => 'File type not allowed. Allowed types: ' . implode(', ', $allowedTypes),
                    'data' => []
                ];
            }

            // Create locker directory
            $lockerDir = __DIR__ . '/../../../LockerFiles/' . date('Y') . '/';
            if (!is_dir($lockerDir)) {
                if (!mkdir($lockerDir, 0777, true)) {
                    return [
                        'httpcode' => 500,
                        'success' => false,
                        'message' => 'Failed to create upload directory',
                        'data' => []
                    ];
                }
            }

            // Generate unique filename
            $time = time();
            $randomString = bin2hex(random_bytes(5));
            $uniqueFileName = $staffId . '-' . $time . '-' . $randomString . '.' . $fileExt;
            $filePath = $lockerDir . $uniqueFileName;

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $filePath)) {
                return [
                    'httpcode' => 500,
                    'success' => false,
                    'message' => 'Failed to upload file',
                    'data' => []
                ];
            }

            // Get file type category
            $fileType = $this->getFileTypeCategory($fileExt);

            // Insert into database
            $fileId = $this->lockerModel->insertFile(
                $staffId,
                $uniqueFileName,
                $originalFileName,
                $filePath,
                $fileType,
                $file['size'],
                $description
            );

            return [
                'httpcode' => 200,
                'success' => true,
                'message' => 'File uploaded successfully',
                'data' => ['fileId' => $fileId]
            ];
        }
        catch(DatabaseException $e) {
            // Clean up file if database insert fails
            if (isset($filePath) && file_exists($filePath)) {
                unlink($filePath);
            }
            return [
                'httpcode' => 500,
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage(),
                'data' => []
            ];
        }
        catch(Exception $e) {
            // Clean up file if error occurs
            if (isset($filePath) && file_exists($filePath)) {
                unlink($filePath);
            }
            return [
                'httpcode' => 400,
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    public function apiDeleteFile(int $fileId, int $staffId) : array {
        try {
            $result = $this->lockerModel->deleteFile($fileId, $staffId);
            if ($result) {
                return [
                    'httpcode' => 200,
                    'success' => true,
                    'message' => 'File deleted successfully',
                    'data' => []
                ];
            } else {
                return [
                    'httpcode' => 404,
                    'success' => false,
                    'message' => 'File not found',
                    'data' => []
                ];
            }
        }
        catch(DatabaseException $e) {
            return [
                'httpcode' => 500,
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage(),
                'data' => []
            ];
        }
        catch(Exception $e) {
            return [
                'httpcode' => 400,
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    private function getFileTypeCategory(string $extension) : string {
        $imageTypes = ['jpg', 'jpeg', 'png', 'gif'];
        $documentTypes = ['pdf', 'doc', 'docx', 'txt'];
        $presentationTypes = ['ppt', 'pptx'];
        $spreadsheetTypes = ['xls', 'xlsx'];
        $archiveTypes = ['zip', 'rar'];

        if (in_array($extension, $imageTypes)) {
            return 'Image';
        } elseif (in_array($extension, $documentTypes)) {
            return 'Document';
        } elseif (in_array($extension, $presentationTypes)) {
            return 'Presentation';
        } elseif (in_array($extension, $spreadsheetTypes)) {
            return 'Spreadsheet';
        } elseif (in_array($extension, $archiveTypes)) {
            return 'Archive';
        } else {
            return 'Other';
        }
    }
}
?>

