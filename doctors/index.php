<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

use Models\Lists\DoctorsPropertyValuesTable as DoctorsTable;


$doctors = DoctorsTable::query()
    ->setSelect([
        '*',
        'ID' => 'ELEMENT.ID',
        'NAME' => 'ELEMENT.NAME',
        'SPECIALISATION_NAME' => 'SPECIALISATION.ELEMENT.NAME',
    ])
    ->setOrder(['NAME'=>'desc'])
    ->registerRuntimeField(
        null,
        new \Bitrix\Main\Entity\ReferenceField(
            'SPECIALISATION',
            \Models\Lists\SpecialisationsPropertyValuesTable::getEntity(),
            ['=this.SPECIALISATION_ID' => 'ref.IBLOCK_ELEMENT_ID']
        )
    )
    ->fetchAll();
?>

<link rel="stylesheet" href="/local/assets/css/doctors.css">
<div class="doctors-page">
    <div class="doctors-header">
        <h1>Doctors List</h1>
        <div class="button-group">
            <a href="/doctors/edit-doctor.php" class="btn">Create Doctor</a>
            <a href="/doctors/new-procedure.php" class="btn btn-secondary">Create Medical Procedure</a>
        </div>
    </div>

    <div class="doctors-list">
        <?php foreach ($doctors as $doctor): ?>
            <div class="doctor-card" onclick="location.href='/doctors/doctor-details.php?id=<?= $doctor['ID'] ?>'">
                <div class="doctor-name"><?= htmlspecialchars($doctor['FIRST_NAME'] . ' ' . $doctor['LAST_NAME']) ?></div>
                <div class="doctor-specialisation"><?= htmlspecialchars($doctor['SPECIALISATION_NAME']) ?></div>
            </div>
        <?php endforeach; ?>
    </div>
</div>



<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>