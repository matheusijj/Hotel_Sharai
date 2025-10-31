<?php
require_once __DIR__ . '/_layout_header.php';
require_login();
$quartoModel = new Quarto($pdo);
$reservaModel = new Reserva($pdo);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editing = $id > 0;
$erro = '';
$r = [
  'quarto_id' => '', 'data_entrada' => '', 'data_saida' => '', 'nome_completo' => '', 'email' => '', 'cpf' => '', 'telefone' => ''
];

if ($editing) {
  $row = $reservaModel->getById($id);
  if ($row) $r = $row; else $erro = 'Reserva não encontrada';
}

// Carregar quartos ativos
$quartos = $quartoModel->listAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $dados = [
    'quarto_id' => (int)($_POST['quarto_id'] ?? 0),
    'data_entrada' => $_POST['data_entrada'] ?? '',
    'data_saida' => $_POST['data_saida'] ?? '',
    'nome_completo' => trim($_POST['nome_completo'] ?? ''),
    'email' => trim($_POST['email'] ?? ''),
    'cpf' => trim($_POST['cpf'] ?? ''),
    'telefone' => trim($_POST['telefone'] ?? ''),
  ];
  $r = array_merge($r, $dados);

  if (!$dados['quarto_id'] || !$dados['data_entrada'] || !$dados['data_saida'] || !$dados['nome_completo'] || !$dados['email']) {
    $erro = 'Preencha os campos obrigatórios';
  } elseif ($reservaModel->existeConflitoDatas($dados['quarto_id'], $dados['data_entrada'], $dados['data_saida'], $editing ? $id : null)) {
    $erro = 'Já existe reserva para este quarto no período informado';
  } else {
    if ($editing) {
      $reservaModel->update($id, $dados);
    } else {
      $newId = $reservaModel->criar($dados);
    }
    header('Location: reservas.php');
    exit;
  }
}
?>
<h2 class="my-4"><?php echo $editing ? 'Editar Reserva' : 'Nova Reserva'; ?></h2>
<div class="card">
  <div class="card-body">
    <?php if ($erro): ?><div class="alert alert-danger"><?php echo htmlspecialchars($erro); ?></div><?php endif; ?>
    <form method="post">
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label">Quarto *</label>
          <select name="quarto_id" class="form-select" required>
            <option value="">Selecione</option>
            <?php foreach ($quartos as $q): ?>
              <option value="<?php echo (int)$q['id']; ?>" <?php echo ((int)$r['quarto_id'] === (int)$q['id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($q['numero'] . ' - ' . $q['tipo']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-3">
          <label class="form-label">Entrada *</label>
          <input type="date" name="data_entrada" class="form-control" value="<?php echo htmlspecialchars($r['data_entrada']); ?>" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Saída *</label>
          <input type="date" name="data_saida" class="form-control" value="<?php echo htmlspecialchars($r['data_saida']); ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">Nome Completo *</label>
          <input type="text" name="nome_completo" class="form-control" value="<?php echo htmlspecialchars($r['nome_completo']); ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">E-mail *</label>
          <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($r['email']); ?>" required>
        </div>
        <div class="col-md-6">
          <label class="form-label">CPF</label>
          <input type="text" name="cpf" class="form-control" value="<?php echo htmlspecialchars($r['cpf']); ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Telefone</label>
          <input type="text" name="telefone" class="form-control" value="<?php echo htmlspecialchars($r['telefone']); ?>">
        </div>
      </div>
      <div class="mt-3">
        <a href="reservas.php" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary"><?php echo $editing ? 'Salvar' : 'Cadastrar'; ?></button>
      </div>
    </form>
  </div>
</div>
<?php require_once __DIR__ . '/_layout_footer.php'; ?>
