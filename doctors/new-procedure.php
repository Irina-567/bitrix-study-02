<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');

use Bitrix\Main\Loader;
use Models\Lists\MedicalProceduresPropertyValuesTable;

Loader::includeModule('iblock');

//$iblockId = MedicalProceduresPropertyValuesTable::IBLOCK_ID;

$procedure = null;
$name = '';
$description = '';

// Save on POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);

    $newId = MedicalProceduresPropertyValuesTable::add([
        'NAME' => $name,
        'DESC' => $description,
    ]);

    if ($newId) {
        LocalRedirect("/doctors/index.php");
    } else {
        echo "<p style='color:red;'>Failed to create medical procedure.</p>";
    }

}
?>

<link rel="stylesheet" href="/local/assets/css/doctors.css">

<div class="doctors-page">
    <h1 class="align-center">Create Medical Procedure</h1>

    <form method="post">
        <label>
            Name of new medical procedure:
            <input type="text" name="name" value="<?= htmlspecialchars($name) ?>" required>
        </label>

        <label>
            Description:
            <input type="text" name="description" value="<?= htmlspecialchars($description) ?>" required>
        </label>

        <br><br>
        <div class="button-group">

            <button type="submit" class="btn btn-primary">Create</button>
            <a href="/doctors/index.php" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<?php require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php'); ?>
