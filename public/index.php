<?php

declare(strict_types=1);

// Cuando se usa el servidor embebido de PHP (php -S ... public/index.php) como router,
// dejar que sirva directamente los archivos estáticos/páginas PHP existentes.
if (PHP_SAPI === 'cli-server') {
    $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $file = __DIR__ . $path;
    if ($path !== '/' && $path !== '/index.php' && is_file($file)) {
        return false;
    }
}

error_reporting(E_ALL);
ini_set('display_errors', '0');

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/config/database.php';

spl_autoload_register(function (string $class) {
    foreach (['core', 'middleware', 'controllers', 'models', 'reports'] as $dir) {
        $file = __DIR__ . "/../src/$dir/$class.php";
        if (is_file($file)) {
            require_once $file;
            return;
        }
    }
});

set_exception_handler(function (Throwable $e) {
    error_log($e->getMessage());
    Response::error('Error interno del servidor', 500);
});

Auth::startSession();

$router = new Router();

$router->get('/', function () {
    Response::json(['message' => 'API Sistema Hospitalario v1']);
});

$router->post('/auth/login', [AuthController::class, 'login']);
$router->post('/auth/logout', [AuthController::class, 'logout']);
$router->get('/auth/me', [AuthController::class, 'me']);
$router->post('/auth/password-reset/request', [AuthController::class, 'requestPasswordReset']);
$router->post('/auth/password-reset/confirm', [AuthController::class, 'resetPassword']);

$router->get('/patients', [PatientController::class, 'index']);
$router->get('/patients/{id}', [PatientController::class, 'show']);
$router->get('/patients/{id}/medical-record', [PatientController::class, 'medicalRecord']);
$router->post('/patients', [PatientController::class, 'store']);
$router->put('/patients/{id}', [PatientController::class, 'update']);
$router->delete('/patients/{id}', [PatientController::class, 'destroy']);

$router->get('/staff/doctors', [StaffController::class, 'doctors']);
$router->post('/staff/doctors', [StaffController::class, 'createDoctor']);
$router->put('/staff/doctors/{id}', [StaffController::class, 'updateDoctor']);
$router->delete('/staff/doctors/{id}', [StaffController::class, 'deleteDoctor']);
$router->get('/staff/nurses', [StaffController::class, 'nurses']);
$router->post('/staff/nurses', [StaffController::class, 'createNurse']);
$router->put('/staff/nurses/{id}', [StaffController::class, 'updateNurse']);
$router->delete('/staff/nurses/{id}', [StaffController::class, 'deleteNurse']);

$router->get('/departments', [DepartmentController::class, 'index']);
$router->post('/departments', [DepartmentController::class, 'store']);
$router->put('/departments/{id}', [DepartmentController::class, 'update']);
$router->delete('/departments/{id}', [DepartmentController::class, 'destroy']);

$router->get('/specialties', [SpecialtyController::class, 'index']);
$router->post('/specialties', [SpecialtyController::class, 'store']);
$router->put('/specialties/{id}', [SpecialtyController::class, 'update']);
$router->delete('/specialties/{id}', [SpecialtyController::class, 'destroy']);

$router->get('/appointment-types', [AppointmentTypeController::class, 'index']);
$router->post('/appointment-types', [AppointmentTypeController::class, 'store']);
$router->put('/appointment-types/{id}', [AppointmentTypeController::class, 'update']);
$router->delete('/appointment-types/{id}', [AppointmentTypeController::class, 'destroy']);

$router->get('/availability', [AvailabilityController::class, 'index']);
$router->post('/availability', [AvailabilityController::class, 'store']);
$router->put('/availability/{id}', [AvailabilityController::class, 'update']);
$router->delete('/availability/{id}', [AvailabilityController::class, 'destroy']);

$router->get('/appointments', [AppointmentController::class, 'index']);
$router->post('/appointments', [AppointmentController::class, 'store']);
$router->put('/appointments/{id}', [AppointmentController::class, 'update']);
$router->delete('/appointments/{id}', [AppointmentController::class, 'destroy']);

$router->get('/consultations', [ConsultationController::class, 'index']);
$router->get('/consultations/{id}', [ConsultationController::class, 'show']);
$router->post('/consultations', [ConsultationController::class, 'store']);
$router->put('/consultations/{id}', [ConsultationController::class, 'update']);
$router->delete('/consultations/{id}', [ConsultationController::class, 'destroy']);

$router->get('/vital-signs', [VitalSignController::class, 'index']);
$router->post('/vital-signs', [VitalSignController::class, 'store']);

$router->get('/diagnoses', [DiagnosisController::class, 'index']);
$router->post('/diagnoses', [DiagnosisController::class, 'store']);

$router->get('/room-types', [RoomTypeController::class, 'index']);
$router->post('/room-types', [RoomTypeController::class, 'store']);
$router->put('/room-types/{id}', [RoomTypeController::class, 'update']);
$router->delete('/room-types/{id}', [RoomTypeController::class, 'destroy']);

$router->get('/rooms', [RoomController::class, 'index']);
$router->post('/rooms', [RoomController::class, 'store']);
$router->put('/rooms/{id}', [RoomController::class, 'update']);
$router->delete('/rooms/{id}', [RoomController::class, 'destroy']);

$router->get('/beds', [BedController::class, 'index']);
$router->post('/beds', [BedController::class, 'store']);
$router->put('/beds/{id}', [BedController::class, 'update']);
$router->delete('/beds/{id}', [BedController::class, 'destroy']);

$router->get('/hospitalization/admissions', [HospitalizationController::class, 'admissions']);
$router->post('/hospitalization/admissions', [HospitalizationController::class, 'createAdmission']);
$router->post('/hospitalization/discharges', [HospitalizationController::class, 'discharge']);
$router->post('/hospitalization/transfers', [HospitalizationController::class, 'transfer']);
$router->get('/hospitalization/nursing-rounds', [HospitalizationController::class, 'nursingRounds']);
$router->post('/hospitalization/nursing-rounds', [HospitalizationController::class, 'createNursingRound']);

$router->get('/lab/test-types', [LabTestTypeController::class, 'index']);
$router->post('/lab/test-types', [LabTestTypeController::class, 'store']);
$router->put('/lab/test-types/{id}', [LabTestTypeController::class, 'update']);
$router->delete('/lab/test-types/{id}', [LabTestTypeController::class, 'destroy']);

$router->get('/lab/parameters', [LabParameterController::class, 'index']);
$router->post('/lab/parameters', [LabParameterController::class, 'store']);
$router->put('/lab/parameters/{id}', [LabParameterController::class, 'update']);
$router->delete('/lab/parameters/{id}', [LabParameterController::class, 'destroy']);

$router->get('/lab/orders', [LaboratoryController::class, 'orders']);
$router->post('/lab/orders', [LaboratoryController::class, 'createOrder']);
$router->get('/lab/samples', [LaboratoryController::class, 'samples']);
$router->post('/lab/samples', [LaboratoryController::class, 'createSample']);
$router->get('/lab/results', [LaboratoryController::class, 'results']);
$router->post('/lab/results', [LaboratoryController::class, 'createResult']);

$router->get('/pharmacy/medication-categories', [MedicationCategoryController::class, 'index']);
$router->post('/pharmacy/medication-categories', [MedicationCategoryController::class, 'store']);
$router->put('/pharmacy/medication-categories/{id}', [MedicationCategoryController::class, 'update']);
$router->delete('/pharmacy/medication-categories/{id}', [MedicationCategoryController::class, 'destroy']);

$router->get('/pharmacy/medications', [MedicationController::class, 'index']);
$router->get('/pharmacy/medications/stock', [MedicationController::class, 'stock']);
$router->post('/pharmacy/medications', [MedicationController::class, 'store']);
$router->put('/pharmacy/medications/{id}', [MedicationController::class, 'update']);
$router->delete('/pharmacy/medications/{id}', [MedicationController::class, 'destroy']);

$router->get('/pharmacy/suppliers', [SupplierController::class, 'index']);
$router->post('/pharmacy/suppliers', [SupplierController::class, 'store']);
$router->put('/pharmacy/suppliers/{id}', [SupplierController::class, 'update']);
$router->delete('/pharmacy/suppliers/{id}', [SupplierController::class, 'destroy']);

$router->get('/pharmacy/lots', [LotController::class, 'index']);
$router->post('/pharmacy/lots', [LotController::class, 'store']);
$router->put('/pharmacy/lots/{id}', [LotController::class, 'update']);
$router->delete('/pharmacy/lots/{id}', [LotController::class, 'destroy']);

$router->get('/pharmacy/prescriptions', [PharmacyController::class, 'prescriptions']);
$router->post('/pharmacy/prescriptions', [PharmacyController::class, 'createPrescription']);
$router->get('/pharmacy/prescription-items', [PharmacyController::class, 'prescriptionItems']);
$router->post('/pharmacy/prescription-items', [PharmacyController::class, 'createPrescriptionItem']);
$router->post('/pharmacy/dispense', [PharmacyController::class, 'dispense']);

$router->get('/inventory/warehouses', [InventoryController::class, 'warehouses']);
$router->post('/inventory/warehouses', [InventoryController::class, 'createWarehouse']);
$router->get('/inventory/items', [InventoryController::class, 'items']);
$router->post('/inventory/items', [InventoryController::class, 'createItem']);
$router->post('/inventory/movements', [InventoryController::class, 'createMovement']);

$router->get('/radiology/test-types', [RadiologyTestTypeController::class, 'index']);
$router->post('/radiology/test-types', [RadiologyTestTypeController::class, 'store']);
$router->put('/radiology/test-types/{id}', [RadiologyTestTypeController::class, 'update']);
$router->delete('/radiology/test-types/{id}', [RadiologyTestTypeController::class, 'destroy']);

$router->get('/radiology/orders', [RadiologyController::class, 'orders']);
$router->post('/radiology/orders', [RadiologyController::class, 'createOrder']);
$router->post('/radiology/studies', [RadiologyController::class, 'createStudy']);
$router->post('/radiology/reports', [RadiologyController::class, 'createReport']);

$router->get('/billing/payment-methods', [PaymentMethodController::class, 'index']);
$router->post('/billing/payment-methods', [PaymentMethodController::class, 'store']);
$router->put('/billing/payment-methods/{id}', [PaymentMethodController::class, 'update']);
$router->delete('/billing/payment-methods/{id}', [PaymentMethodController::class, 'destroy']);

$router->get('/billing/services', [BillableServiceController::class, 'index']);
$router->post('/billing/services', [BillableServiceController::class, 'store']);
$router->put('/billing/services/{id}', [BillableServiceController::class, 'update']);
$router->delete('/billing/services/{id}', [BillableServiceController::class, 'destroy']);

$router->get('/billing/invoices', [BillingController::class, 'invoices']);
$router->post('/billing/invoices', [BillingController::class, 'createInvoice']);
$router->get('/billing/invoices/{id}/items', [BillingController::class, 'invoiceItems']);
$router->post('/billing/payments', [BillingController::class, 'registerPayment']);

$router->get('/emergency/triage-levels', [TriageLevelController::class, 'index']);
$router->post('/emergency/triage-levels', [TriageLevelController::class, 'store']);
$router->put('/emergency/triage-levels/{id}', [TriageLevelController::class, 'update']);
$router->delete('/emergency/triage-levels/{id}', [TriageLevelController::class, 'destroy']);

$router->get('/emergency/cases', [EmergencyController::class, 'cases']);
$router->post('/emergency/cases', [EmergencyController::class, 'createCase']);
$router->post('/emergency/attend', [EmergencyController::class, 'attend']);

$router->get('/dashboard/summary', [DashboardController::class, 'summary']);
$router->get('/dashboard/bed-occupancy', [DashboardController::class, 'bedOccupancy']);
$router->get('/dashboard/appointments-today', [DashboardController::class, 'appointmentsToday']);
$router->get('/dashboard/low-stock', [DashboardController::class, 'lowStock']);
$router->get('/dashboard/pending-labs', [DashboardController::class, 'pendingLabs']);
$router->get('/dashboard/billing-today', [DashboardController::class, 'billingToday']);
$router->get('/dashboard/emergency-by-priority', [DashboardController::class, 'emergencyByPriority']);

$router->get('/reports/patients.pdf', [ReportController::class, 'patientsPdf']);
$router->get('/reports/invoices.xlsx', [ReportController::class, 'invoicesExcel']);
$router->get('/reports/low-stock.pdf', [ReportController::class, 'lowStockPdf']);

$router->get('/admin/users', [UserController::class, 'index']);
$router->get('/admin/roles', [UserController::class, 'roles']);
$router->post('/admin/users', [UserController::class, 'store']);
$router->put('/admin/users/{id}', [UserController::class, 'update']);
$router->delete('/admin/users/{id}', [UserController::class, 'destroy']);

$router->dispatch();
