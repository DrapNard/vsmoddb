<?php
namespace App\Services;

use App\Core\Service;

class FileService extends Service
{
    private $uploadDir;
    private $allowedTypes;

    public function __construct()
    {
        parent::__construct();
        $this->uploadDir = dirname(dirname(__DIR__)) . '/public/uploads';
        $this->allowedTypes = [
            'application/zip',
            'application/x-zip-compressed',
            'application/json',
            'text/plain',
            'image/jpeg',
            'image/png',
            'image/gif'
        ];
    }

    /**
     * Upload a file
     *
     * @param array $file The uploaded file data
     * @param string $subDir Optional subdirectory within uploads
     * @return array|false Returns file info on success, false on failure
     */
    public function upload($file, $subDir = '')
    {
        try {
            if (!isset($file['tmp_name']) || !isset($file['type']) || !in_array($file['type'], $this->allowedTypes)) {
                return false;
            }

            // Create target directory if it doesn't exist
            $targetDir = $this->uploadDir;
            if ($subDir) {
                $targetDir .= '/' . trim($subDir, '/');
            }
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '.' . $extension;
            $targetPath = $targetDir . '/' . $filename;

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                return false;
            }

            return [
                'filename' => $filename,
                'original_name' => $file['name'],
                'mime_type' => $file['type'],
                'size' => $file['size'],
                'path' => str_replace($this->uploadDir, '', $targetPath)
            ];
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Delete a file
     *
     * @param string $path Relative path from upload directory
     * @return bool
     */
    public function delete($path)
    {
        try {
            $fullPath = $this->uploadDir . '/' . ltrim($path, '/');
            if (file_exists($fullPath) && is_file($fullPath)) {
                return unlink($fullPath);
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get file info
     *
     * @param string $path Relative path from upload directory
     * @return array|false
     */
    public function getInfo($path)
    {
        try {
            $fullPath = $this->uploadDir . '/' . ltrim($path, '/');
            if (!file_exists($fullPath) || !is_file($fullPath)) {
                return false;
            }

            return [
                'filename' => basename($fullPath),
                'mime_type' => mime_content_type($fullPath),
                'size' => filesize($fullPath),
                'path' => $path
            ];
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Validate file type
     *
     * @param string $mimeType
     * @return bool
     */
    public function isAllowedType($mimeType)
    {
        return in_array($mimeType, $this->allowedTypes);
    }

    /**
     * Add allowed file type
     *
     * @param string $mimeType
     */
    public function addAllowedType($mimeType)
    {
        if (!in_array($mimeType, $this->allowedTypes)) {
            $this->allowedTypes[] = $mimeType;
        }
    }

    /**
     * Get file contents
     *
     * @param string $path Relative path from upload directory
     * @return string|false
     */
    public function getContents($path)
    {
        try {
            $fullPath = $this->uploadDir . '/' . ltrim($path, '/');
            if (!file_exists($fullPath) || !is_file($fullPath)) {
                return false;
            }

            return file_get_contents($fullPath);
        } catch (\Exception $e) {
            return false;
        }
    }
}