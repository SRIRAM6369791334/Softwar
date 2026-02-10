<?php

namespace App\Core;

class Controller
{
    public function view(string $view, array $params = [], string $layout = 'main')
    {
        // Extract variables to be available in view
        extract($params);

        // Buffer the view content
        ob_start();
        require APP_ROOT . "/views/pages/$view.php";
        $content = ob_get_clean();

        // Render within the layout
        if ($layout) {
            require APP_ROOT . "/views/layouts/$layout.php";
        } else {
            echo $content;
        }
    }

    public function json(array $data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function redirect(string $url)
    {
        header("Location: $url");
        exit;
    }

    protected function requireRole(int|array $roles)
    {
        if (!Auth::check()) {
            $this->redirect('/login');
        }

        if (!Auth::hasRole($roles)) {
            http_response_code(403);
            echo "<h1>403 Unuathorized</h1><p>You do not have permission to access this page.</p>";
            echo "<a href='/dashboard'>Return to Dashboard</a>";
            exit;
        }
    }
}
