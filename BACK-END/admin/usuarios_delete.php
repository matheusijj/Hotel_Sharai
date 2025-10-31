<?php
require_once __DIR__ . '/_layout_header.php';
require_login();
$userModel = new User($pdo);
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
// Impede excluir o usuário atualmente logado
$currentId = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
if ($id) {
  if ($id === $currentId) {
    header('Location: usuarios.php?error=own');
    exit;
  }
  $userModel->delete($id);
}
header('Location: usuarios.php');
exit;
