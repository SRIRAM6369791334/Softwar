<?php

namespace App\Core;

class FileHandler
{
    private const ALLOWED_MIMES = [
        'image/jpeg' => ['ff', 'd8', 'ff'],
        'image/png'  => ['89', '50', '4e', '47'],
        'image/gif'  => ['47', '49', '46', '38'],
        'application/pdf' => ['25', '50', '44', '46'],
    ];

    /**
     * Securely handle file upload
     * 
     * @param array $fileData $_FILES['key']
     * @param string $destinationDir
     * @return string Filename on success
     * @throws \Exception
     */
    public static function upload(array $fileData, string $destinationDir): string
    {
        if ($fileData['error'] !== UPLOAD_ERR_OK) {
            throw new \Exception("File upload failed with error code: " . $fileData['error']);
        }

        // 1. Basic Validation
        $tmpPath = $fileData['tmp_name'];
        $fileName = $fileData['name'];
        $fileSize = $fileData['size'];

        if ($fileSize > 5 * 1024 * 1024) { // 5MB Limit
            throw new \Exception("File is too large (Max 5MB)");
        }

        // 2. MIME Validation (Magic Numbers)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $tmpPath);
        finfo_close($finfo);

        if (!isset(self::ALLOWED_MIMES[$mime])) {
            throw new \Exception("Invalid file type: $mime");
        }

        // 3. Header Check (Deep Validation)
        $handle = fopen($tmpPath, 'rb');
        $header = fread($handle, 4);
        fclose($handle);

        $hex = bin2hex($header);
        $isValidHeader = false;
        foreach (self::ALLOWED_MIMES[$mime] as $magic) {
            if (strpos($hex, $magic) === 0) {
                $isValidHeader = true;
                break;
            }
        }

        if (!$isValidHeader) {
            throw new \Exception("File header mismatch. File might be malicious.");
        }

        // 4. Filename Sanitization
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        $newFileName = bin2hex(random_bytes(16)) . '.' . $ext;

        if (!is_dir($destinationDir)) {
            mkdir($destinationDir, 0755, true);
        }

        $destPath = $destinationDir . DIRECTORY_SET_SEPARATOR . $newFileName;
        
        // Final Security: Prevent path traversal
        $destPath = realpath($destinationDir) . DIRECTORY_SEPARATOR . $newFileName;

        if (!move_uploaded_file($tmpPath, $destPath)) {
            throw new \Exception("Failed to save file.");
        }

        return $newFileName;
    }
}
