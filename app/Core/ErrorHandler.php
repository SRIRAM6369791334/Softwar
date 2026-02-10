<?php

namespace App\Core;

/**
 * Centralized Error Handler
 * Manages error logging, generic error views, and production safety
 */
class ErrorHandler
{
    private static $instance = null;
    private $isProduction = false;
    private $logFile;

    private function __construct()
    {
        $this->isProduction = defined('APP_ENV') && APP_ENV === 'production';
        $this->logFile = defined('APP_ROOT') ? APP_ROOT . '/storage/logs/error.log' : __DIR__ . '/../../storage/logs/error.log';
        
        // Ensure log directory exists
        $logDir = dirname($this->logFile);
        if (!is_dir($logDir)) {
            mkdir($logDir, 0755, true);
        }
    }

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Register self as error/exception handler
     */
    public function register(): void
    {
        error_reporting(E_ALL);
        set_error_handler([$this, 'handleError']);
        set_exception_handler([$this, 'handleException']);
        register_shutdown_function([$this, 'handleShutdown']);
    }

    /**
     * Handle PHP Errors
     */
    public function handleError($level, $message, $file, $line)
    {
        if (!(error_reporting() & $level)) {
            return false;
        }

        $this->logError("Error ($level): $message in $file on line $line");
        $this->renderError(500, $message, $file, $line);
        return true;
    }

    /**
     * Handle Uncaught Exceptions
     */
    public function handleException(\Throwable $exception)
    {
        $message = $exception->getMessage();
        $file = $exception->getFile();
        $line = $exception->getLine();
        $trace = $exception->getTraceAsString();

        $this->logError("Exception: $message in $file on line $line\nStack Trace:\n$trace");
        $this->renderError(500, $message, $file, $line, $trace);
    }

    /**
     * Handle Fatal Errors on Shutdown
     */
    public function handleShutdown()
    {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            $this->handleError($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }

    /**
     * Log Error to File
     */
    private function logError(string $message): void
    {
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[$timestamp] $message\n" . str_repeat('-', 80) . "\n";
        file_put_contents($this->logFile, $logEntry, FILE_APPEND);
    }

    /**
     * Render Error View
     */
    private function renderError(int $code, string $message, string $file, int $line, string $trace = '')
    {
        // Don't render twice if we can avoid it
        if (headers_sent()) {
            echo "\n<br><b>Critical Error:</b> $message";
            return;
        }

        http_response_code($code);

        // If AJAX request, return JSON
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'message' => $this->isProduction ? 'An internal server error occurred' : $message,
                'debug' => $this->isProduction ? null : [
                    'file' => $file,
                    'line' => $line,
                    'trace' => explode("\n", $trace)
                ]
            ]);
            exit;
        }

        // Generic Error for Production
        if ($this->isProduction) {
            $this->showProductionError($code);
        } else {
            $this->showDebugError($code, $message, $file, $line, $trace);
        }
        exit;
    }

    private function showProductionError(int $code)
    {
        include __DIR__ . '/../../views/errors/generic.php';
    }

    private function showDebugError(int $code, string $message, string $file, int $line, string $trace)
    {
        ?>
        <div style="background: #f8d7da; color: #721c24; padding: 20px; border: 1px solid #f5c6cb; border-radius: 5px; font-family: sans-serif; margin: 20px;">
            <h2 style="margin-top: 0;">⚠️ Internal Server Error (<?php echo $code; ?>)</h2>
            <p><strong>Message:</strong> <?php echo htmlspecialchars($message); ?></p>
            <p><strong>File:</strong> <?php echo htmlspecialchars($file); ?> (Line: <?php echo $line; ?>)</p>
            <pre style="background: #eee; padding: 10px; overflow: auto; max-height: 400px;"><?php echo htmlspecialchars($trace); ?></pre>
        </div>
        <?php
    }
}
