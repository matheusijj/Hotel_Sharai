<?php
require_once __DIR__ . '/_layout_header.php';
require_once __DIR__ . '/../models/Quarto.php';
require_login();
$quartoModel = new Quarto($pdo);

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$editing = $id > 0;
$erro = '';
$quarto = [
  'numero' => '', 'tipo' => '', 'preco_noite' => '', 'descricao' => '', 'status' => 1,
];

if ($editing) {
  $q = $quartoModel->buscarPorId($id);
  if ($q) $quarto = $q; else $erro = 'Quarto não encontrado';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $dados = [
    'numero' => trim($_POST['numero'] ?? ''),
    'tipo' => trim($_POST['tipo'] ?? ''),
    'preco_noite' => $_POST['preco_noite'] !== '' ? (float)$_POST['preco_noite'] : null,
    'descricao' => trim($_POST['descricao'] ?? ''),
    'status' => isset($_POST['status']) ? 1 : 0,
  ];
  if (!$dados['numero'] || !$dados['tipo']) {
    $erro = 'Número e Tipo são obrigatórios';
    $quarto = array_merge($quarto, $dados);
  } else {
    if ($editing) {
      $quartoModel->update($id, $dados);
      header('Location: quartos.php');
      exit;
    } else {
      $newId = $quartoModel->create($dados);
      header('Location: quartos.php?created=1');
      exit;
    }
  }
}
?>
<h2 class="my-4"><?php echo $editing ? 'Editar Quarto' : 'Cadastrar Novo Quarto'; ?></h2>
<div class="card">
  <div class="card-body">
    <?php if ($erro): ?><div class="alert alert-danger"><?php echo htmlspecialchars($erro); ?></div><?php endif; ?>
    <form method="post">
      <div class="mb-3">
        <label class="form-label">Número do Quarto *</label>
        <input type="text" name="numero" class="form-control" value="<?php echo htmlspecialchars($quarto['numero']); ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Tipo *</label>
        <input type="text" name="tipo" class="form-control" value="<?php echo htmlspecialchars($quarto['tipo']); ?>" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Preço por Noite</label>
        <input type="number" step="0.01" name="preco_noite" class="form-control" value="<?php echo htmlspecialchars((string)($quarto['preco_noite'] ?? '')); ?>">
      </div>
      <div class="mb-3">
        <label class="form-label">Descrição</label>
        <textarea name="descricao" class="form-control" rows="4"><?php echo htmlspecialchars($quarto['descricao'] ?? ''); ?></textarea>
      </div>
      <div class="form-check form-switch mb-3">
        <input class="form-check-input" type="checkbox" id="status" name="status" <?php echo ((int)($quarto['status'] ?? 1) === 1) ? 'checked' : ''; ?>>
        <label class="form-check-label" for="status">Quarto Ativo</label>
      </div>
      <div>
        <a href="quartos.php" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary"><?php echo $editing ? 'Salvar' : 'Cadastrar'; ?></button>
      </div>
    </form>
  </div>
</div>
<?php require_once __DIR__ . '/_layout_footer.php'; ?>
