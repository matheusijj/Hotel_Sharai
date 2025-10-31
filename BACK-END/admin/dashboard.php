<?php
require_once __DIR__ . '/_layout_header.php';
require_login();
?>
<h2 class="mt-4 mb-4">Painel Administrativo</h2>
<div class="row g-3">
  <div class="col-md-6">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Gerenciar Quartos</h5>
        <p class="card-text">Cadastre, edite e remova quartos. Eles aparecerão na área pública de reservas.</p>
        <a href="quarto_form.php" class="btn btn-success">Cadastrar Novo</a>
        <a href="quartos.php" class="btn btn-outline-secondary">Ver Quartos</a>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card">
      <div class="card-body">
        <h5 class="card-title">Gerenciar Reservas</h5>
        <p class="card-text">Acompanhe e administre as reservas realizadas no site.</p>
        <a href="reserva_form.php" class="btn btn-primary">Nova Reserva</a>
        <a href="reservas.php" class="btn btn-outline-secondary">Ver Reservas</a>
      </div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/_layout_footer.php'; ?>
