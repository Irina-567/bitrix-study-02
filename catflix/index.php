<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

//$APPLICATION->SetTitle('Query');

use Bitrix\Main\Type;
use Bitrix\Main\Entity\Query;

use Models\CatflixStreamsTable as Streams;


function formatDuration(int $seconds): string {
    $minutes = floor($seconds / 60);
    $remainingSeconds = $seconds % 60;
    return sprintf('%02d:%02d', $minutes, $remainingSeconds);
}

// get Streams collection
$q = new Query(Streams::getEntity());
$q->setSelect(array('id', 'title', 'short_description','stream_length', 'user_id', 'USER', 'CATS', 'REVIEWS'));
$result = $q->exec();
$collection = $result->fetchCollection();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Catflix Streams</title>
    <style>
        .stream-block {
            padding: 10px;
            border: 1px solid #ddd;
            margin-bottom: 20px;
            border-radius: 5px;
            background-color: #fdfdfd;
        }

        .toggle-link {
            color: #007bff;
            text-decoration: underline;
            cursor: pointer;
        }

        .reviews-block {
            font-size: 0.9em;
            color: #333;
            margin-top: 8px;
            margin-left: 20px;
            display: none;
        }

        .rating {
            font-weight: bold;
        }
    </style>
</head>
<body>

<h1>Catflix Streams</h1>

<?php

foreach ($collection as $index => $record) {
    $streamTitle = $record->getTitle();
    $streamLength = $record->getStreamLength();
    $streamLengthText = $streamLength ? "(".formatDuration($streamLength).")" : "";

    // Get user
    $user = $record->getUser();
    $username = $user ? $user->getUsername() : 'Unknown';

    // Get cats
    $cats = $record->getCats();
    $catNames = [];

    foreach ($cats as $cat) {
        $catNames[] = $cat->getCatName();
    }

    // Get reviews
    $reviews = $record->getReviews();
    $ratings = [];
    $reviewHtml = '';

    foreach ($reviews as $review) {
        $rating = $review->getRating();
        $ratings[] = $rating;
        $commentRaw = $review->getComment();
        $comment = $commentRaw;

        if (is_string($commentRaw) && str_starts_with($commentRaw, 'a:')) {
            $data = @unserialize($commentRaw);
            if (is_array($data) && isset($data['TEXT'])) {
                $comment = $data['TEXT'];
            }
        }
        $reviewHtml .= "<div>🗨️ <em>{$comment}</em>  <strong>Rating: {$rating}</strong></div>";
    }

    $avg = count($ratings) ? round(array_sum($ratings) / count($ratings), 2) : null;
    $avgDisplay = $avg ? "{$avg} ★" : "No ratings yet";

    $uniqueId = "reviews_block_{$index}";

    echo "<div class='stream-block'>";
    echo "<strong>🎬 Stream:</strong> {$streamTitle} <strong>{$streamLengthText}</strong><br><br>";
    echo "<strong>Description:</strong> {$record->getShortDescription()}<br>";
    echo "<strong>👤 Published by:</strong> {$username}<br>";
    echo "<strong>🐾 Featuring Cats:</strong> " . implode(', ', $catNames) . "<br>";

    if ($avg) {
        echo "<span class='rating'>⭐ Average Rating:</span> <a href='#' class='toggle-link' data-target='{$uniqueId}'>{$avgDisplay}</a><br>";
        echo "<div id='{$uniqueId}' class='reviews-block'>{$reviewHtml}</div>";
    } else {
        echo "<em>No reviews yet</em><br>";
    }

    echo "</div>";

}

?>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.toggle-link').forEach(function (link) {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.dataset.target;
            const target = document.getElementById(targetId);
            if (target) {
                const isVisible = target.style.display === 'block';
                target.style.display = isVisible ? 'none' : 'block';
            }
        });
    });
});



</script>

</body>
</html>

<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>