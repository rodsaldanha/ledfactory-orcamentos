<?php
/**
 * Página de Login
 */
$csrf = Security::generateCSRF();
$error = '';
$expired = isset($_GET['expired']) ? true : false;
if (isset($_SESSION['login_error'])) {
    $error = $_SESSION['login_error'];
    unset($_SESSION['login_error']);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
    <meta name="robots" content="noindex, nofollow">
</head>
<body>
<div class="login-wrapper">
    <div class="login-box">
        <div class="login-logo">
            <img src="assets/img/logo.png" alt="LED Factory">
        </div>
        <div class="login-title">Acesso ao Sistema</div>

        <?php if ($expired): ?>
        <div class="alert alert-error">Sessão expirada. Faça login novamente.</div>
        <?php endif; ?>

        <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="?p=do_login" autocomplete="off">
            <input type="hidden" name="csrf_token" value="<?php echo $csrf; ?>">

            <div class="form-group">
                <label class="form-label">Usuário</label>
                <input type="text" name="username" class="form-input" placeholder="Digite seu usuário" required autofocus>
            </div>

            <div class="form-group">
                <label class="form-label">Senha</label>
                <input type="password" name="password" class="form-input" placeholder="Digite sua senha" required>
            </div>

            <button type="submit" class="btn btn-primary btn-full">Entrar</button>
        </form>
    </div>
</div>
</body>
</html>
