
<?php

$errors = $_SESSION['messages']['errors'] ?? [];
unset($_SESSION['messages']['errors']);
?>

<?php if (!empty($errors)): ?>
            <div class="error-message">
                <?php foreach ($errors as $error): ?>
                    <p><?= htmlspecialchars($error) ?></p>
                <?php endforeach; ?>
            </div>
 <?php endif; ?>