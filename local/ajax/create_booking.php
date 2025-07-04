<?php

define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);
define("BX_NO_ACCELERATOR_RESET", true);
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

header('Content-Type: application/json');

if (!CModule::IncludeModule('iblock')) {
    echo json_encode(['success' => false, 'error' => 'Was not able to connect iblock']);
    exit;
}

//
$BOOKINGS_IBLOCK_ID = 24;

//get and validate booking data
$patientName = trim($_POST['patient_name'] ?? '');
$bookingTime = trim($_POST['booking_time'] ?? '');
$procedureId = (int)($_POST['procedure_id'] ?? 0);
$doctorId = (int)($_POST['doctor_id'] ?? 0);

if (!$patientName || !$bookingTime || !$procedureId || !$doctorId) {
    echo json_encode(['success' => false, 'error' => 'Please, check if all fields are filled']);
    exit;
}


$date = date_create_from_format('d.m.Y H:i:s', $bookingTime);
if (!$date) {
    $date = date_create_from_format('d.m.Y H:i', $bookingTime);
}
if (!$date) {
    echo json_encode(['success' => false, 'error' => 'Something is wrong with selected time!']);
    exit;
}

$formattedTime = $date->format('d.m.Y H:i:s');

$requestedTime = \DateTime::createFromFormat('d.m.Y H:i:s', $formattedTime);
if (!$requestedTime) {
    $requestedTime = \DateTime::createFromFormat('d.m.Y H:i', $formattedTime);
}

if (!$requestedTime) {
    echo json_encode(['success' => false, 'error' => 'Something is wrong with selected time!']);
    exit;
}

//check for conflicts
$conflictFound = false;

$existingRes = CIBlockElement::GetList([], [
    'IBLOCK_ID' => $BOOKINGS_IBLOCK_ID,
    'PROPERTY_DOCTOR_ID' => $doctorId,
], false, false, ['ID', 'PROPERTY_BOOKING_TIME']);

while ($item = $existingRes->Fetch()) {
    $existingTimeStr = $item['PROPERTY_BOOKING_TIME_VALUE'];
    if (!$existingTimeStr) {
        continue;
    }

    //get existing booking time
    $existingTime = DateTime::createFromFormat('d.m.Y H:i:s', $existingTimeStr);
    if (!$existingTime) {
        $existingTime = DateTime::createFromFormat('d.m.Y H:i', $existingTimeStr);
    }

    if (!$existingTime) {
        continue;
    }

    //compare booking time
    $diffInSeconds = abs($requestedTime->getTimestamp() - $existingTime->getTimestamp());

    if ($diffInSeconds < 1800) {
        $conflictFound = true;
        break;
    }
}

if ($conflictFound) {
    echo json_encode(['success' => false, 'error' => 'Doctor already has this time booked. Select another time, please']);
    exit;
}

//create booking
$el = new CIBlockElement;

$arFields = [
    'IBLOCK_ID' => $BOOKINGS_IBLOCK_ID,
    'NAME' => "Бронирование: {$patientName}",
    'ACTIVE' => 'Y',
    'PROPERTY_VALUES' => [
        'PATIENT_NAME' => $patientName,
        'BOOKING_TIME' => $formattedTime,
        'PROCEDURE_ID' => $procedureId,
        'DOCTOR_ID' => $doctorId
    ]
];

$bookingId = $el->Add($arFields);

if ($bookingId) {
    echo json_encode(['success' => true, 'booking_id' => $bookingId]);
} else {
    echo json_encode(['success' => false, 'error' => $el->LAST_ERROR]);
}