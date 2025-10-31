<?php
class ReservaController {
  private Reserva $reserva;
  private Quarto $quarto;
  public function __construct(PDO $pdo) {
    $this->reserva = new Reserva($pdo);
    $this->quarto = new Quarto($pdo);
  }

  public function verificarDisponibilidade() {
    $quartoId = (int)($_GET['quarto_id'] ?? 0);
    $entrada = $_GET['entrada'] ?? '';
    $saida = $_GET['saida'] ?? '';
    $ok = $quartoId && $entrada && $saida;
    $disp = $ok ? $this->reserva->verificarDisponibilidade($quartoId, $entrada, $saida) : false;
    header('Content-Type: application/json');
    echo json_encode(['disponivel' => $disp, 'mensagem' => $disp ? 'Disponível' : 'Indisponível']);
  }

  public function criarPublica() {
    $body = file_get_contents('php://input');
    $data = json_decode($body, true);
    if (!$data) { $data = $_POST; }

    $quartoId = (int)($data['quarto_id'] ?? 0);
    $entrada = $data['data_entrada'] ?? '';
    $saida = $data['data_saida'] ?? '';
    $nome = $data['nome_completo'] ?? '';
    // E-mail do hóspede salvo em variável
    $emailDestino = $data['email'] ?? '';
    $cpf = $data['cpf'] ?? '';
    $telefone = $data['telefone'] ?? '';
    // Campos opcionais para email
    $emailAssunto = trim((string)($data['email_assunto'] ?? 'Confirmação de Reserva'));
    $emailMensagem = (string)($data['email_mensagem'] ?? '');

    header('Content-Type: application/json');

    if (!$quartoId || !$entrada || !$saida || !$nome || !$emailDestino || !$cpf || !$telefone) {
      echo json_encode(['sucesso' => false, 'mensagem' => 'Dados insuficientes']);
      return;
    }

    if (!$this->quarto->buscarPorId($quartoId)) {
      echo json_encode(['sucesso' => false, 'mensagem' => 'Quarto inválido']);
      return;
    }

    if (!$this->reserva->verificarDisponibilidade($quartoId, $entrada, $saida)) {
      echo json_encode(['sucesso' => false, 'mensagem' => 'Quarto indisponível para o período']);
      return;
    }

    $id = $this->reserva->criar($data);

    // Enviar e-mail de confirmação (se configurado)
    $enviado = false; $erroEmail = null;
    try {
      $config = require __DIR__ . '/../config.php';
      require_once __DIR__ . '/../services/Mailer.php';
      $mailer = new \App\Services\Mailer($config);
      if ($mailer->isConfigured()) {
        $html = '<p>Olá ' . htmlspecialchars($nome) . ',</p>' .
                '<p>Sua reserva foi confirmada.</p>' .
                '<p><strong>Quarto:</strong> ' . (int)$quartoId . '<br>' .
                '<strong>Entrada:</strong> ' . htmlspecialchars($entrada) . '<br>' .
                '<strong>Saída:</strong> ' . htmlspecialchars($saida) . '</p>';
        if ($emailMensagem !== '') {
          $html .= '<hr><p>' . nl2br(htmlspecialchars($emailMensagem)) . '</p>';
        }
        $res = $mailer->send($emailDestino, $emailAssunto, $html);
        $enviado = $res['sent'];
        $erroEmail = $res['error'] ?? null;
      }
    } catch (\Throwable $e) {
      $erroEmail = $e->getMessage();
    }

    echo json_encode([
      'sucesso' => true,
      'mensagem' => 'Reserva criada com sucesso',
      'id' => $id,
      'email' => [ 'enviado' => $enviado, 'erro' => $erroEmail ]
    ]);
  }
}

