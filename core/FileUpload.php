<?php
namespace Puf\Core;

class FileUpload {
    private $uploadDir;

    public function __construct($config) {
        $this->uploadDir = __DIR__ . '/../../uploads/'; // Adjust based on your structure
        if (!is_dir($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    public function handleUploads() {
        $response = ["success" => true, "files" => [], "errors" => []];

        file_put_contents(__DIR__ . '/../../debug.log', "UPLOAD STARTED\n", FILE_APPEND);

        if (empty($_FILES['images']['name'][0])) {
            file_put_contents(__DIR__ . '/../../debug.log', "No files uploaded\n", FILE_APPEND);
            return $response;
        }

        foreach ($_FILES['images']['tmp_name'] as $index => $tmpName) {
            if ($_FILES['images']['error'][$index] !== UPLOAD_ERR_OK) {
                $response['errors'][] = "Error uploading " . $_FILES['images']['name'][$index];
                continue;
            }

            $extension = strtolower(pathinfo($_FILES['images']['name'][$index], PATHINFO_EXTENSION));
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

            if (!in_array($extension, $allowedTypes)) {
                $response['errors'][] = "Invalid file type: " . $_FILES['images']['name'][$index];
                continue;
            }

            $uniqueName = uniqid('file_', true) . '.' . $extension;
            $targetPath = $this->uploadDir . $uniqueName;

            file_put_contents(__DIR__ . '/../../debug.log', "Attempting to move: $tmpName -> $targetPath\n", FILE_APPEND);

            if (move_uploaded_file($tmpName, $targetPath)) {
                file_put_contents(__DIR__ . '/../../debug.log', "FILE MOVED SUCCESSFULLY: $uniqueName\n", FILE_APPEND);
                $response['files'][] = [
                    "name" => $_FILES['images']['name'][$index],
                    "url" =>  $uniqueName,
                    "size" => $_FILES['images']['size'][$index],
                    "type" => $extension
                ];
            } else {
                file_put_contents(__DIR__ . '/../../debug.log', "MOVE FAILED: $targetPath\n", FILE_APPEND);
                $response['errors'][] = "Failed to move file: " . $_FILES['images']['name'][$index];
            }
        }

        file_put_contents(__DIR__ . '/../../debug.log', "UPLOAD FINISHED\n", FILE_APPEND);
        return $response;
    }
}
