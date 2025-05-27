<?php
$title = 'Создать задачу';
ob_start();
?>

<form action="/tasks" method="POST">
	<input type="text" name="title" placeholder="Введите заголовок задачи" required><br>
	<textarea name="description" placeholder="Введите описание задачи" required></textarea><br>
	<button type="submit">Создать</button>
</form>

<?php
$content = ob_get_clean();
require 'layout.php';
?>