<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Scheduler;

class SchedulerController extends Controller
{
    public function run()
    {
        // Secure this endpoint? For now, open or require a secret key
        // if ($_GET['key'] !== 'SECRET_CRON_KEY') die('Unauthorized');

        $scheduler = new Scheduler();
        $logs = $scheduler->run();

        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'logs' => $logs]);
    }
}
