<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');

use Bitrix\Main\Loader;
use Models\Lists\DoctorsPropertyValuesTable;
use Models\Lists\SpecialisationsPropertyValuesTable;
use Models\Lists\MedicalProceduresPropertyValuesTable;
use Bitrix\Iblock\Elements\ElementDoctorsTable;

Loader::includeModule('iblock');

$iblockId = DoctorsPropertyValuesTable::IBLOCK_ID;

$doctorId = (int)($_GET['id'] ?? 0);
$isEdit = $doctorId > 0;

$doctor = null;
$firstName = '';
$lastName = '';
$specialisationId = 0;
$selectedProcedures = [];

// Load doctor if edit mode
if ($isEdit) {
    $doctor = ElementDoctorsTable::getByPrimary($doctorId, [
        'select' => [
            '*',
            'FIRST_NAME',
            'LAST_NAME',
            'SPECIALISATION_ID.ELEMENT.ID',
            'MEDICAL_PROCEDURE_IDS.ELEMENT.ID'
        ]
    ])->fetchObject();

    if (!$doctor) {
        echo "<p>Doctor not found.</p>";
        require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
        return;
    }

    // Fill values
    $firstName = $doctor->getFirstName()?->getValue() ?? '';
    $lastName = $doctor->getLastName()?->getValue() ?? '';
    $specialisationId = $doctor->getSpecialisationId()?->getElement()?->getId() ?? 0;

    $procCollection = $doctor->getMedicalProcedureIds()?->getAll() ?? [];
    foreach ($procCollection as $prItem) {
        $selectedProcedures[] = $prItem->getElement()->getId();
    }
}

// Load select options
$specialisations = SpecialisationsPropertyValuesTable::query()
    ->setSelect(['ID' => 'ELEMENT.ID', 'NAME' => 'ELEMENT.NAME'])
    ->fetchAll();

$procedures = MedicalProceduresPropertyValuesTable::query()
    ->setSelect(['ID' => 'ELEMENT.ID', 'NAME' => 'ELEMENT.NAME'])
    ->fetchAll();

// Save on POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name']);
    $lastName = trim($_POST['last_name']);
    $specialisationId = (int)$_POST['specialisation'];
    $procedureIds = array_map('intval', $_POST['procedures'] ?? []);

    if ($isEdit) {
        $doctor->set('FIRST_NAME', $firstName);
        $doctor->set('LAST_NAME', $lastName);
        $doctor->set('SPECIALISATION_ID', $specialisationId);

        $result = $doctor->save();

        if ($result->isSuccess()) {
            \CIBlockElement::SetPropertyValuesEx(
                $doctor->getId(),
                $iblockId,
                ['MEDICAL_PROCEDURE_IDS' => $procedureIds]
            );

            LocalRedirect("/doctors/doctor-details.php?id={$doctorId}");
        } else {
            echo "<p style='color:red;'>Error saving doctor: " . implode(', ', $result->getErrorMessages()) . "</p>";
        }

    } else {
        $newId = DoctorsPropertyValuesTable::add([
            'NAME' => $firstName . ' ' . $lastName,
            'FIRST_NAME' => $firstName,
            'LAST_NAME' => $lastName,
            'SPECIALISATION_ID' => $specialisationId,
//            'MEDICAL_PROCEDURE_IDS' => $procedureIds,
        ]);

        if ($newId) {
            \CIBlockElement::SetPropertyValuesEx(
                $newId,
                $iblockId,
                ['MEDICAL_PROCEDURE_IDS' => $procedureIds]
            );

            LocalRedirect("/doctors/doctor-details.php?id={$newId}");
        } else {
            echo "<p style='color:red;'>Failed to create doctor.</p>";
        }
    }
}
?>

<link rel="stylesheet" href="/local/assets/css/doctors.css">

<div class="doctors-page">
    <h1 class="align-center"><?= $isEdit ? 'Edit Doctor' : 'Create Doctor' ?></h1>

    <form method="post">
        <label>
            First Name:
            <input type="text" name="first_name" value="<?= htmlspecialchars($firstName) ?>" required>
        </label>

        <label>
            Last Name:
            <input type="text" name="last_name" value="<?= htmlspecialchars($lastName) ?>" required>
        </label>

        <label>
            Specialisation:
            <select name="specialisation" required>
                <option value="">— Select —</option>
                <?php foreach ($specialisations as $spec): ?>
                    <option value="<?= $spec['ID'] ?>" <?= $spec['ID'] == $specialisationId ? 'selected' : '' ?>>
                        <?= htmlspecialchars($spec['NAME']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <label>
            Medical Procedures:
            <select name="procedures[]" multiple size="6">
                <?php foreach ($procedures as $proc): ?>
                    <option value="<?= $proc['ID'] ?>" <?= in_array($proc['ID'], $selectedProcedures) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($proc['NAME']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>

        <br><br>
        <div class="button-group">

            <button type="submit" class="btn btn-primary"><?= $isEdit ? 'Save Changes' : 'Create Doctor' ?></button>
            <?php if ($isEdit): ?>
                <a href="/doctors/doctor-details.php?id=<?= $doctorId ?>" class="btn btn-secondary">Cancel</a>
            <?php else: ?>
                <a href="/doctors/index.php" class="btn btn-secondary">Back to List</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<?php require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php'); ?>
