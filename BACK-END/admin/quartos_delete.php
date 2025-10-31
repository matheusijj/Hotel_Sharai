<?php
require_once __DIR__ . '/_layout_header.php';
require_login();
$quartoModel = new Quarto($pdo);
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id) { $quartoModel->delete($id); }
header('Location: quartos.php');
exit;
