<?php
require_once __DIR__ . '/_layout_header.php';
require_login();
$reservaModel = new Reserva($pdo);
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id) { $reservaModel->delete($id); }
header('Location: reservas.php');
exit;
