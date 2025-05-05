<?php
require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');

use Models\Lists\DoctorsPropertyValuesTable;
use Models\Lists\MedicalProceduresPropertyValuesTable;
use Models\Lists\SpecialisationsPropertyValuesTable;
use Bitrix\Main\Loader;

Loader::includeModule('iblock');

$doctorId = (int)$_GET['id'];
if (!$doctorId) {
    echo "<p>Doctor not found.</p>";
    require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
    return;
}


$doctors = \Bitrix\Iblock\Elements\ElementDoctorsTable::getList([
    'select' => [
        'ID',
        'NAME',
        'FIRST_NAME',
        'LAST_NAME',
        'SPECIALISATION_ID.ELEMENT.NAME',
        'MEDICAL_PROCEDURE_IDS.ELEMENT.NAME'
    ],
    'filter' => [
        'ID' => $doctorId,
        'ACTIVE' => 'Y'
    ],
])
->fetchCollection();


foreach ($doctors as $doctor) {
    $procedures = $doctor->getMedicalProcedureIds()->getAll();
?>

<link rel="stylesheet" href="/local/assets/css/doctors.css">
<div class="doctors-page">
    <div class="button-group">
        <p>
            <a href="/doctors/index.php" class="btn btn-secondary">Back to doctors list</a>
        </p>
    </div>

    <h1>Doctor: <?= htmlspecialchars($doctor->getFirstName()->getValue() . ' ' . $doctor->getLastName()->getValue()) ?></h1>
    <p><strong>Specialisation:</strong> <?= htmlspecialchars($doctor->get('SPECIALISATION_ID')->getElement()->getName()) ?></p>

    <h3>Medical Procedures</h3>
    <ul>
        <?php foreach ($procedures as $procedure): ?>
            <li><?= htmlspecialchars($procedure->getElement()->getName()) ?></li>
        <?php endforeach; ?>
        <?php if (empty($procedures)): ?>
            <li>No procedures assigned.</li>
        <?php endif; ?>
    </ul>

    <div class="button-group">
        <p>
            <a href="/doctors/edit-doctor.php?id=<?= $doctor['ID'] ?>" class="btn">Edit</a>
        </p>
    </div>
</div>
    <?php
}
    ?>

<?php require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php'); ?>