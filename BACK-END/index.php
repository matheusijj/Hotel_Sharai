<?php
require __DIR__ . '/db.php';
require __DIR__ . '/models/Quarto.php';
require __DIR__ . '/models/Reserva.php';
require __DIR__ . '/controllers/QuartoController.php';
require __DIR__ . '/controllers/ReservaController.php';

$controller = $_GET['controller'] ?? '';
$action = $_GET['action'] ?? '';

try {
  if ($controller === 'quarto' && $action === 'listarPublico') {
    (new QuartoController($pdo))->listarPublico();
    exit;
  }
  if ($controller === 'reserva' && $action === 'verificarDisponibilidade') {
    (new ReservaController($pdo))->verificarDisponibilidade();
    exit;
  }
  if ($controller === 'reserva' && $action === 'criarPublica') {
    (new ReservaController($pdo))->criarPublica();
    exit;
  }
  header('Content-Type: application/json');
  http_response_code(404);
  echo json_encode(['sucesso' => false, 'mensagem' => 'Rota não encontrada']);
} catch (Throwable $e) {
  header('Content-Type: application/json');
  http_response_code(500);
  echo json_encode(['sucesso' => false, 'mensagem' => 'Erro interno']);
}

