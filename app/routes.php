<?php

/** @var \App\Core\Application $app */

$app->router->get('/', function() {
    return "<h1>Welcome to Supermarket OS</h1><p>System Online. Framework-less Core Active.</p>";
});


// --- Auth Routes ---
$app->router->get('/login', [\App\Controllers\AuthController::class, 'login']);
$app->router->post('/login', [\App\Controllers\AuthController::class, 'authenticate']);
$app->router->get('/logout', [\App\Controllers\AuthController::class, 'logout']);

// --- Map Routes ---
$app->router->get('/map', [\App\Controllers\MapController::class, 'index']);
$app->router->get('/map/data', [\App\Controllers\MapController::class, 'getMapData']);
$app->router->post('/map/update', [\App\Controllers\MapController::class, 'saveLocation']);
$app->router->get('/map/search', [\App\Controllers\MapController::class, 'searchUnmapped']);

// --- Biometric Auth Routes ---
$app->router->get('/auth/biometric/register-options', [\App\Controllers\AuthController::class, 'biometricRegisterOptions']);
$app->router->post('/auth/biometric/register', [\App\Controllers\AuthController::class, 'biometricRegister']);
$app->router->get('/auth/biometric/login-options', [\App\Controllers\AuthController::class, 'biometricLoginOptions']);
$app->router->post('/auth/biometric/verify', [\App\Controllers\AuthController::class, 'biometricVerify']);

// --- Protected Routes ---
$app->router->get('/dashboard', [\App\Controllers\ReportsController::class, 'dashboard']);
$app->router->get('/profile', [\App\Controllers\UserController::class, 'profile']);

// --- Reports Routes ---
$app->router->get('/reports', function() {
    header('Location: /reports/daybook');
    exit;
});
$app->router->get('/reports/daybook', [\App\Controllers\ReportsController::class, 'daybook']);
$app->router->get('/reports/invoice/{id}', [\App\Controllers\ReportsController::class, 'invoice']);
$app->router->get('/reports/top-products', [\App\Controllers\ReportsController::class, 'topProducts']);
$app->router->get('/reports/employee-performance', [\App\Controllers\ReportsController::class, 'employeePerformance']);
$app->router->get('/reports/reorder', [\App\Controllers\ReportsController::class, 'reorderReport']);
$app->router->get('/reports/gst', [\App\Controllers\ReportsController::class, 'gstDashboard']);
$app->router->get('/reports/gst/hsn', [\App\Controllers\ReportsController::class, 'hsnSummary']);
$app->router->get('/reports/gst/export', [\App\Controllers\ReportsController::class, 'exportGstCsv']);
$app->router->get('/reports/price-comparison', [\App\Controllers\ReportsController::class, 'priceComparison']);

// --- User Management Routes ---
$app->router->get('/users', [\App\Controllers\UserController::class, 'index']);
$app->router->get('/users/create', [\App\Controllers\UserController::class, 'create']);
$app->router->post('/users/store', [\App\Controllers\UserController::class, 'store']);

// --- Product Management Routes ---
$app->router->get('/products', [\App\Controllers\ProductController::class, 'index']);
$app->router->get('/products/create', [\App\Controllers\ProductController::class, 'create']);
$app->router->post('/products/store', [\App\Controllers\ProductController::class, 'store']);
$app->router->get('/products/settings/{id}', [\App\Controllers\ProductController::class, 'editSettings']);
$app->router->post('/products/settings/{id}', [\App\Controllers\ProductController::class, 'updateSettings']);
$app->router->get('/products/delete/{id}', [\App\Controllers\ProductController::class, 'delete']);

// --- Inventory Routes ---
$app->router->get('/inventory', [\App\Controllers\InventoryController::class, 'index']);
$app->router->get('/inventory/inward', [\App\Controllers\InventoryController::class, 'inward']);
$app->router->post('/inventory/store', [\App\Controllers\InventoryController::class, 'store']);
$app->router->get('/inventory/transfers', [\App\Controllers\StockTransferController::class, 'index']);
$app->router->get('/inventory/transfers/create', [\App\Controllers\StockTransferController::class, 'create']);
$app->router->post('/inventory/transfers/store', [\App\Controllers\StockTransferController::class, 'store']);
$app->router->get('/inventory/transfers/receive/{id}', [\App\Controllers\StockTransferController::class, 'receive']);
$app->router->get('/inventory/transfers/fulfill/{id}', [\App\Controllers\StockTransferController::class, 'fulfillForm']);
$app->router->post('/inventory/transfers/fulfill/{id}', [\App\Controllers\StockTransferController::class, 'fulfill']);
$app->router->get('/inventory/transfers/batches/{id}', [\App\Controllers\StockTransferController::class, 'getBatches']);

// --- Purchase Order Internal Routes ---
$app->router->get('/inventory/po', [\App\Controllers\PurchaseOrderController::class, 'index']);
$app->router->get('/inventory/po/create', [\App\Controllers\PurchaseOrderController::class, 'create']);
$app->router->post('/inventory/po/store', [\App\Controllers\PurchaseOrderController::class, 'store']);
$app->router->get('/inventory/po/status/{id}/{status}', [\App\Controllers\PurchaseOrderController::class, 'transitionStatus']);

// --- POS Routes ---
$app->router->get('/pos', [\App\Controllers\PosController::class, 'terminal']);
$app->router->get('/pos/search', [\App\Controllers\PosController::class, 'search']);
$app->router->post('/pos/checkout', [\App\Controllers\PosController::class, 'checkout']);

// --- Branch Management Routes ---
$app->router->get('/branches', [\App\Controllers\BranchController::class, 'index']);
$app->router->get('/branches/create', [\App\Controllers\BranchController::class, 'create']);
$app->router->post('/branches/store', [\App\Controllers\BranchController::class, 'store']);
$app->router->get('/branches/edit/{id}', [\App\Controllers\BranchController::class, 'edit']);
$app->router->post('/branches/update/{id}', [\App\Controllers\BranchController::class, 'update']);
$app->router->post('/branches/switch/{id}', [\App\Controllers\BranchController::class, 'switchBranch']);

// --- Central Reporting Routes (Admin Only) ---
$app->router->get('/central/dashboard', [\App\Controllers\CentralReportsController::class, 'dashboard']);
$app->router->get('/central/comparison', [\App\Controllers\CentralReportsController::class, 'comparison']);

// --- Vendor Portal Routes ---
$app->router->get('/vendor/login', [\App\Controllers\VendorPortalController::class, 'login']);
$app->router->post('/vendor/login', [\App\Controllers\VendorPortalController::class, 'authenticate']);
$app->router->get('/vendor/logout', [\App\Controllers\VendorPortalController::class, 'logout']);
$app->router->get('/vendor/dashboard', [\App\Controllers\VendorPortalController::class, 'dashboard']);
$app->router->get('/vendor/orders', [\App\Controllers\VendorPortalController::class, 'orders']);
$app->router->get('/vendor/orders/{id}', [\App\Controllers\VendorPortalController::class, 'orderDetails']);
$app->router->post('/vendor/orders/upload/{id}', [\App\Controllers\VendorPortalController::class, 'uploadInvoice']);
$app->router->get('/vendor/forecasting', [\App\Controllers\VendorPortalController::class, 'forecasting']);
$app->router->get('/vendor/quotations', [\App\Controllers\VendorPortalController::class, 'quotations']);
$app->router->post('/vendor/quotations', [\App\Controllers\VendorPortalController::class, 'quotations']);
$app->router->get('/vendor/ledger', [\App\Controllers\VendorPortalController::class, 'ledger']);
$app->router->post('/vendor/orders/backorder/{id}', [\App\Controllers\VendorPortalController::class, 'updateBackorder']);
$app->router->post('/vendor/orders/grn/{id}', [\App\Controllers\VendorPortalController::class, 'saveGRN']);
$app->router->get('/vendor/analytics', [\App\Controllers\VendorPortalController::class, 'analytics']);

// --- Vendor Admin Routes ---
$app->router->get('/admin/vendors', [\App\Controllers\VendorAdminController::class, 'index']);
$app->router->get('/admin/vendor/create', [\App\Controllers\VendorAdminController::class, 'create']);
$app->router->post('/admin/vendor/store', [\App\Controllers\VendorAdminController::class, 'store']);
$app->router->get('/admin/vendor/analytics', [\App\Controllers\VendorAdminController::class, 'analytics']);
$app->router->get('/admin/vendor/quotations', [\App\Controllers\VendorAdminController::class, 'quotations']);
$app->router->get('/admin/vendor/quotations/approve/{id}', [\App\Controllers\VendorAdminController::class, 'approveQuotation']);
$app->router->get('/admin/vendor/broadcast', [\App\Controllers\VendorAdminController::class, 'broadcast']);
$app->router->post('/admin/vendor/broadcast', [\App\Controllers\VendorAdminController::class, 'broadcast']);
$app->router->get('/admin/vendor/payments', [\App\Controllers\VendorAdminController::class, 'payments']);
$app->router->post('/admin/vendor/payments', [\App\Controllers\VendorAdminController::class, 'payments']);

// --- Employee Portal Routes ---
$app->router->get('/employee/login', [\App\Controllers\EmployeePortalController::class, 'login']);
$app->router->post('/employee/login', [\App\Controllers\EmployeePortalController::class, 'authenticate']);
$app->router->get('/employee/logout', [\App\Controllers\EmployeePortalController::class, 'logout']);
$app->router->get('/employee/dashboard', [\App\Controllers\EmployeePortalController::class, 'dashboard']);
$app->router->get('/employee/clock-in', [\App\Controllers\EmployeePortalController::class, 'clockIn']);
$app->router->post('/employee/clock-in', [\App\Controllers\EmployeePortalController::class, 'clockIn']);
$app->router->get('/employee/clock-out', [\App\Controllers\EmployeePortalController::class, 'clockOut']);
$app->router->post('/employee/clock-out', [\App\Controllers\EmployeePortalController::class, 'clockOut']);
$app->router->get('/employee/roster', [\App\Controllers\EmployeePortalController::class, 'roster']);
$app->router->get('/employee/shifts/claim/{id}', [\App\Controllers\EmployeePortalController::class, 'claimShift']);
$app->router->get('/employee/messages', [\App\Controllers\EmployeePortalController::class, 'messages']);
$app->router->get('/employee/leaves', [\App\Controllers\EmployeePortalController::class, 'leaves']);
$app->router->post('/employee/leaves/request', [\App\Controllers\EmployeePortalController::class, 'requestLeave']);

// --- Admin Employee Management Routes ---
$app->router->get('/admin/employee/roster', [\App\Controllers\EmployeeAdminController::class, 'roster']);
$app->router->post('/admin/employee/roster/save', [\App\Controllers\EmployeeAdminController::class, 'saveShift']);
$app->router->get('/admin/employee/roster/delete/{id}', [\App\Controllers\EmployeeAdminController::class, 'deleteShift']);
$app->router->get('/admin/employee/leaves', [\App\Controllers\EmployeeAdminController::class, 'leaves']);
$app->router->post('/admin/employee/leaves/update/{id}', [\App\Controllers\EmployeeAdminController::class, 'updateLeaveStatus']);
$app->router->get('/admin/employee/timesheets', [\App\Controllers\EmployeeAdminController::class, 'timesheets']);
$app->router->get('/admin/employee/messages', [\App\Controllers\EmployeeAdminController::class, 'messages']);
$app->router->post('/admin/employee/messages', [\App\Controllers\EmployeeAdminController::class, 'messages']);

$app->router->get('/api/notifications/unread', [\App\Controllers\NotificationController::class, 'getUnread']);
$app->router->post('/api/notifications/read/{id}', [\App\Controllers\NotificationController::class, 'markAsRead']);
$app->router->get('/notifications/clear', [\App\Controllers\NotificationController::class, 'markAllRead']);
$app->router->get('/notifications/summary', [\App\Controllers\NotificationController::class, 'dailySummary']);

// --- System & Maintenance Routes ---
$app->router->get('/health', function() {
    return json_encode(['status' => 'ok', 'timestamp' => time()]);
});

// Scheduler Route (Phase 25)
$app->router->get('/system/cron', [\App\Controllers\SchedulerController::class, 'run']);

// Tax Routes (Phase 25)
$app->router->get('/admin/taxes', [\App\Controllers\TaxController::class, 'index']);
$app->router->get('/admin/taxes/create', [\App\Controllers\TaxController::class, 'create']);
$app->router->post('/admin/taxes/store', [\App\Controllers\TaxController::class, 'store']);
$app->router->get('/admin/taxes/edit/{id}', [\App\Controllers\TaxController::class, 'edit']);
$app->router->post('/admin/taxes/update/{id}', [\App\Controllers\TaxController::class, 'update']);

// Overtime Routes (Phase 26)
$app->router->get('/admin/employee/overtime', [\App\Controllers\OvertimeController::class, 'index']);
$app->router->get('/admin/employee/overtime/approve/{id}', [\App\Controllers\OvertimeController::class, 'approve']);
$app->router->get('/admin/employee/overtime/reject/{id}', [\App\Controllers\OvertimeController::class, 'reject']);
$app->router->get('/admin/employee/overtime/report', [\App\Controllers\OvertimeController::class, 'report']);

// Open Shifts Routes (Phase 26)
$app->router->get('/admin/employee/open-shifts', [\App\Controllers\OpenShiftsController::class, 'index']);
$app->router->get('/admin/employee/open-shifts/create', [\App\Controllers\OpenShiftsController::class, 'create']);
$app->router->post('/admin/employee/open-shifts/store', [\App\Controllers\OpenShiftsController::class, 'store']);
$app->router->get('/employee/open-shifts', [\App\Controllers\OpenShiftsController::class, 'browse']);
$app->router->get('/employee/open-shifts/claim/{id}', [\App\Controllers\OpenShiftsController::class, 'claim']);
