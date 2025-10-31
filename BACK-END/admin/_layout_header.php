<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../db.php';
require_once __DIR__ . '/../services/Auth.php';
require_once __DIR__ . '/../models/Quarto.php';
require_once __DIR__ . '/../models/Reserva.php';

function is_logged_in(): bool { return Auth::isLoggedIn(); }
function require_login() { if (!Auth::isLoggedIn()) { header('Location: login.php'); exit; } }
$currentUser = Auth::currentUser();
?><!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin - Hotel</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Text:ital@0;1&family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&family=Roboto:wght@100;400;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://essentialnutrition-upload-files.s3.us-east-1.amazonaws.com/pot-2025/style.css">
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body class="admin-body">
  <div class="topbar">
    <div class="container container-narrow d-flex justify-content-between align-items-center">
      <div class="brand">
      
        <span class="brand-text d-none d-sm-inline">SHARAI</span>
      </div>
      <div>
        <?php if (is_logged_in()): ?>
          <a href="dashboard.php">Dashboard</a>
          <a href="index.php?c=quartos&a=list">Gerenciar Quartos</a>
          <a href="index.php?c=reservas&a=list">Gerenciar Reservas</a>
          <a href="index.php?c=users&a=list">Usuários</a>
          <a href="logout.php" class="ms-2">Sair</a>
        <?php else: ?>
          <a href="login.php">Login</a>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <div class="container container-narrow">
