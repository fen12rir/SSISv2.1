<?php
declare(strict_types=1);
require_once __DIR__ . '/../../Exceptions/DatabaseException.php';
require_once __DIR__ . '/../models/adminAnnouncementsModel.php';

class adminAnnouncementsController {
    protected $announcementsModel;

    public function __construct() {
        $this->announcementsModel = new adminAnnouncementsModel();
    }

    public function apiGetAllAnnouncements() : array {
        try {
            $data = $this->announcementsModel->getAllAnnouncements();
            return [
                'httpcode' => 200,
                'success' => true,
                'message' => 'Announcements fetched successfully',
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

    public function apiGetPublicAnnouncements(int $limit = 10) : array {
        try {
            $data = $this->announcementsModel->getPublicAnnouncements($limit);
            return [
                'httpcode' => 200,
                'success' => true,
                'message' => 'Announcements fetched successfully',
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

    public function apiCreateAnnouncement(string $title, string $text, ?array $imageFile, string $datePublication) : array {
        try {
            if (empty($title)) {
                return [
                    'httpcode' => 400,
                    'success' => false,
                    'message' => 'Title is required',
                    'data' => []
                ];
            }

            if (empty($text)) {
                return [
                    'httpcode' => 400,
                    'success' => false,
                    'message' => 'Text/Explanation is required',
                    'data' => []
                ];
            }

            if (empty($datePublication)) {
                return [
                    'httpcode' => 400,
                    'success' => false,
                    'message' => 'Date publication is required',
                    'data' => []
                ];
            }

            $imagePath = null;
            
            // Handle image upload if provided
            if ($imageFile !== null && isset($imageFile['tmp_name']) && is_uploaded_file($imageFile['tmp_name'])) {
                $imagePath = $this->handleImageUpload($imageFile);
                if (!$imagePath['success']) {
                    return [
                        'httpcode' => 400,
                        'success' => false,
                        'message' => $imagePath['message'],
                        'data' => []
                    ];
                }
                $imagePath = $imagePath['filepath'];
            }

            $announcementId = $this->announcementsModel->insertAnnouncement(
                $title,
                $text,
                $imagePath,
                $datePublication
            );

            return [
                'httpcode' => 200,
                'success' => true,
                'message' => 'Announcement created successfully',
                'data' => ['announcementId' => $announcementId]
            ];
        }
        catch(DatabaseException $e) {
            // Clean up image if database insert fails
            if (isset($imagePath) && is_string($imagePath) && file_exists(__DIR__ . '/../../../' . $imagePath)) {
                unlink(__DIR__ . '/../../../' . $imagePath);
            }
            return [
                'httpcode' => 500,
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage(),
                'data' => []
            ];
        }
        catch(Exception $e) {
            // Clean up image if error occurs
            if (isset($imagePath) && is_string($imagePath) && file_exists(__DIR__ . '/../../../' . $imagePath)) {
                unlink(__DIR__ . '/../../../' . $imagePath);
            }
            return [
                'httpcode' => 400,
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
                'data' => []
            ];
        }
    }

    public function apiUpdateAnnouncement(int $announcementId, string $title, string $text, ?array $imageFile, string $datePublication, bool $removeImage = false) : array {
        try {
            if (empty($title)) {
                return [
                    'httpcode' => 400,
                    'success' => false,
                    'message' => 'Title is required',
                    'data' => []
                ];
            }

            if (empty($text)) {
                return [
                    'httpcode' => 400,
                    'success' => false,
                    'message' => 'Text/Explanation is required',
                    'data' => []
                ];
            }

            if (empty($datePublication)) {
                return [
                    'httpcode' => 400,
                    'success' => false,
                    'message' => 'Date publication is required',
                    'data' => []
                ];
            }

            // Get existing announcement
            $existingAnnouncement = $this->announcementsModel->getAnnouncementById($announcementId);
            if (!$existingAnnouncement) {
                return [
                    'httpcode' => 404,
                    'success' => false,
                    'message' => 'Announcement not found',
                    'data' => []
                ];
            }

            $imagePath = $existingAnnouncement['Image_Path'];

            // Handle image removal
            if ($removeImage) {
                if ($imagePath && file_exists(__DIR__ . '/../../../' . $imagePath)) {
                    unlink(__DIR__ . '/../../../' . $imagePath);
                }
                $imagePath = null;
            }
            // Handle new image upload
            elseif ($imageFile !== null && isset($imageFile['tmp_name']) && is_uploaded_file($imageFile['tmp_name'])) {
                // Delete old image if exists
                if ($imagePath && file_exists(__DIR__ . '/../../../' . $imagePath)) {
                    unlink(__DIR__ . '/../../../' . $imagePath);
                }
                
                $uploadResult = $this->handleImageUpload($imageFile);
                if (!$uploadResult['success']) {
                    return [
                        'httpcode' => 400,
                        'success' => false,
                        'message' => $uploadResult['message'],
                        'data' => []
                    ];
                }
                $imagePath = $uploadResult['filepath'];
            }

            $result = $this->announcementsModel->updateAnnouncement(
                $announcementId,
                $title,
                $text,
                $imagePath,
                $datePublication
            );

            if ($result) {
                return [
                    'httpcode' => 200,
                    'success' => true,
                    'message' => 'Announcement updated successfully',
                    'data' => []
                ];
            } else {
                return [
                    'httpcode' => 400,
                    'success' => false,
                    'message' => 'Failed to update announcement',
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

    public function apiDeleteAnnouncement(int $announcementId) : array {
        try {
            $result = $this->announcementsModel->deleteAnnouncement($announcementId);
            if ($result) {
                return [
                    'httpcode' => 200,
                    'success' => true,
                    'message' => 'Announcement deleted successfully',
                    'data' => []
                ];
            } else {
                return [
                    'httpcode' => 404,
                    'success' => false,
                    'message' => 'Announcement not found',
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

    private function handleImageUpload(array $imageFile) : array {
        try {
            // Validate file size (max 5MB for images)
            $maxSize = 5 * 1024 * 1024; // 5MB
            if ($imageFile['size'] > $maxSize) {
                return [
                    'success' => false,
                    'message' => 'Image size exceeds 5MB limit'
                ];
            }

            // Get file extension and validate
            $originalFileName = $imageFile['name'];
            $fileExt = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
            
            // Allowed image types
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (!in_array($fileExt, $allowedTypes)) {
                return [
                    'success' => false,
                    'message' => 'Image type not allowed. Allowed types: ' . implode(', ', $allowedTypes)
                ];
            }

            // Create announcements directory
            $announcementsDir = __DIR__ . '/../../../ImageUploads/announcements/' . date('Y') . '/';
            if (!is_dir($announcementsDir)) {
                if (!mkdir($announcementsDir, 0777, true)) {
                    return [
                        'success' => false,
                        'message' => 'Failed to create upload directory'
                    ];
                }
            }

            // Generate unique filename
            $time = time();
            $randomString = bin2hex(random_bytes(5));
            $uniqueFileName = 'announcement-' . $time . '-' . $randomString . '.' . $fileExt;
            $filePath = $announcementsDir . $uniqueFileName;

            // Move uploaded file
            if (!move_uploaded_file($imageFile['tmp_name'], $filePath)) {
                return [
                    'success' => false,
                    'message' => 'Failed to upload image'
                ];
            }

            // Return relative path for database storage
            $relativePath = 'ImageUploads/announcements/' . date('Y') . '/' . $uniqueFileName;

            return [
                'success' => true,
                'message' => 'Image uploaded successfully',
                'filepath' => $relativePath
            ];
        }
        catch(Exception $e) {
            return [
                'success' => false,
                'message' => 'Error uploading image: ' . $e->getMessage()
            ];
        }
    }
}

