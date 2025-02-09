<?php

function setSuccessMessages($messages) {
    $_SESSION['messages']['success'] = $messages;
}

function getSuccessMessages() {
    if (isset($_SESSION['messages']['success'])) {
        $messages = $_SESSION['messages']['success'];
        unset($_SESSION['messages']['success']); 
        return $messages;
    }
    return [];
}

function displaySuccessMessages() {
    $messages = getSuccessMessages();
    if (!empty($messages)) {
        echo '<div class="alert alert-success" role="alert" style="text-align: center;">';
        foreach ($messages as $message) {
            echo '<p>' . htmlspecialchars($message) . '</p>';
        }
        echo '</div>';
    }
}
