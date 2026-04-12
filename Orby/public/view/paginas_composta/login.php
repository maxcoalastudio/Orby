<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt_BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Orby - Login</title>
    
    <link rel="stylesheet" href="/public/css/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Orby</h1>
                <p>Entre ou crie sua conta</p>
            </div>
            
            <?php if (isset($_SESSION['sucesso'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['sucesso']; unset($_SESSION['sucesso']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['erro'])): ?>
                <div class="alert alert-error"><?php echo $_SESSION['erro']; unset($_SESSION['erro']); ?></div>
            <?php endif; ?>
            
            <div class="login-tabs">
                <button class="login-tab active" data-tab="login">Entrar</button>
                <button class="login-tab" data-tab="register">Criar Conta</button>
            </div>
            
            <!-- Formulário de Login -->
            <form action="valida_login" method="post" class="login-form active" id="login-form">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Senha</label>
                    <input type="password" name="senha" class="form-input" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100">Entrar</button>
            </form>
            
            <!-- Formulário de Cadastro -->
            <form action="valida_login" method="post" class="login-form" id="register-form">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Usuário</label>
                    <input type="text" name="usuario" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Senha</label>
                    <input type="password" name="senha" class="form-input" required>
                </div>
                <button type="submit" name="cadastrar" class="btn btn-primary w-100">Criar Conta</button>
            </form>
        </div>
    </div>
    
    <script>
        // Toggle entre login e cadastro
        const tabs = document.querySelectorAll('.login-tab');
        const forms = document.querySelectorAll('.login-form');
        
        tabs.forEach(tab => {
            tab.addEventListener('click', () => {
                const target = tab.dataset.tab;
                
                tabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                
                forms.forEach(form => form.classList.remove('active'));
                document.getElementById(`${target}-form`).classList.add('active');
            });
        });
    </script>
</body>
</html>
