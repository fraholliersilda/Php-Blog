<?php


function setErrors($errors) {
    $_SESSION['messages']['errors'] = $errors;
}


function getErrors() {
    if (isset($_SESSION['messages']['errors'])) {
        $errors = $_SESSION['messages']['errors'];
        unset($_SESSION['messages']['errors']); 
        return $errors;
    }
    return [];
}

function displayErrors() {
    $errors = getErrors();
    if (!empty($errors)) {
        echo '<div class="error-messages">';
        foreach ($errors as $error) {
            echo '<p>' . htmlspecialchars($error) . '</p>';
        }
        echo '</div>';
    }
}
