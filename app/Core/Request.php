<?php

namespace App\Core;

class Request
{
    private $mockJson = null;

    public function getPath(): string
    {
        $path = $_SERVER['REQUEST_URI'] ?? '/';
        $position = strpos($path, '?');
        
        if ($position === false) {
            return $path;
        }
        
        return substr($path, 0, $position);
    }

    public function getMethod(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    public function getBody(): array
    {
        $body = [];
        if ($this->getMethod() === 'get') {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if ($this->getMethod() === 'post') {
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        return $body;
    }

    public function getJson(): array
    {
        // 1. Return Mock Data if set (for Testing)
        if ($this->mockJson !== null) {
            return $this->mockJson;
        }

        // 2. Check php://input
        $contentType = $_SERVER['CONTENT_TYPE'] ?? '';
        if (stripos($contentType, 'application/json') !== false) {
            $input = file_get_contents('php://input');
            $json = json_decode($input, true);
            if (is_array($json)) return $json;
        }
        
        // 3. Fallback to $_POST if JSON was sent as form fields (optional, but robust)
        // For now, strict JSON is better for API.
        
        return [];
    }

    // Helper for testing to inject data
    public function setMockJson(array $data) {
        $this->mockJson = $data;
    }
}
