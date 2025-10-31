<?php
require_once __DIR__ . '/_layout_header.php';
require_login();
$created = !empty($_GET['created']);
$deleted = !empty($_GET['deleted']);
$soft = !empty($_GET['soft']);
$error = !empty($_GET['error']);
// Fallback: se acessado diretamente sem controller, carregar lista aqui
if (!isset($lista)) {
  require_once __DIR__ . '/../models/Quarto.php';
  $quartoModel = new Quarto($pdo);
  $lista = $quartoModel->listAll();
}
?>
<h2 class="my-4">Gerenciar Quartos</h2>
<div class="d-flex justify-content-end mb-3">
  <a href="quarto_form.php" class="btn btn-success">Novo Quarto</a>
</div>
<div class="card">
  <div class="card-body">
    <?php if ($deleted): ?>
      <div class="alert alert-success">Quarto excluído com sucesso.</div>
    <?php endif; ?>
    <?php if ($soft): ?>
      <div class="alert alert-warning">Quarto possui reservas vinculadas. Ele foi marcado como Inativo.</div>
    <?php endif; ?>
    <?php if ($error): ?>
      <div class="alert alert-danger">Não foi possível excluir o quarto. Tente novamente mais tarde.</div>
    <?php endif; ?>
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>ID</th>
            <th>Número</th>
            <th>Tipo</th>
            <th>Preço</th>
            <th>Status</th>
            <th style="width:160px">Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($lista as $q): ?>
          <tr>
            <td><?php echo (int)$q['id']; ?></td>
            <td><?php echo htmlspecialchars($q['numero']); ?></td>
            <td><?php echo htmlspecialchars($q['tipo']); ?></td>
            <td><?php echo 'R$ ' . number_format((float)$q['preco_noite'], 0, '', '.'); ?></td>
            <td><?php echo ((int)$q['status'] === 1) ? 'Ativo' : 'Inativo'; ?></td>
            <td>
              <a href="quarto_form.php?id=<?php echo (int)$q['id']; ?>" class="btn btn-sm btn-primary">Editar</a>
              <a href="index.php?c=quartos&a=delete&id=<?php echo (int)$q['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Excluir este quarto?');">Excluir</a>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- Modal de sucesso ao cadastrar -->
<div class="modal fade" id="modalSucessoQuarto" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Quarto cadastrado com sucesso!</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        O quarto foi cadastrado com sucesso.
      </div>
      <div class="modal-footer">
        <a href="index.php?c=quartos&a=list" class="btn btn-primary">Ver quartos</a>
        <a href="quarto_form.php" class="btn btn-success">Cadastrar novo</a>
      </div>
    </div>
  </div>
</div>
<script>
  (function(){
    const params = new URLSearchParams(window.location.search);
    if (params.get('created') === '1') {
      const el = document.getElementById('modalSucessoQuarto');
      if (el) bootstrap.Modal.getOrCreateInstance(el).show();
    }
  })();
</script>
<?php require_once __DIR__ . '/_layout_footer.php'; ?>
