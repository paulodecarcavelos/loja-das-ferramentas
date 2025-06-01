<?php

session_start();    // Inicia a sessão PHP para armazenar dados do utilizador autenticado

require "../api/auth.php";  // Importa o ficheiro que contém a função login() e verifica as credenciais

$error_msg = false; // Variável para controlar se há mensagem de erro
$msg = "";  // Mensagem de erro a ser mostrada no caso de haver erro
// Verifica se o formulário foi submetido (método POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recolhe os dados introduzidos pelo utilizador
    $username = $_POST["username"];
    $password = $_POST["password"];
    // Verifica se ambos os campos estão preenchidos
    if (empty($username) || empty($password)) {
        $error_msg = true;
        $msg = "Preencha todos os campos";
    } else {
        // Tenta fazer o login com as credenciais fornecidas
        // A função login() deve retornar true se as credenciais forem válidas
        if (login($username, $password)) {
            header("Location: ../index.php");   // Redireciona para a página principal após o login bem-sucedido
        } else {
            $error_msg = true;
            $msg = "O login falhou. Verifique o seu username e password.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    .btn-verde-forte {
        background-color: #007f00 !important;   /* Muda a cor de fundo do botão */
        color: white !important;
    }

    .input-verde-claro {
        background-color: #e6f9e6 !important; /* Força a côr de fundo por cima do Bootstrap */
        border: 1px solid #b2d8b2;            /* Bordas em tom verde claro */
        color: #000;                            /* Côr do texto */
    }

    .input-verde-claro:focus {
        background-color: #d4f5d4 !important; /* Ao focar fica com o tom ligeiramente mais escuro */
        border-color: #66cc66;
        box-shadow: 0 0 0 0.2rem rgba(0, 128, 0, 0.25); /* Ligeira sombra verde clara */
        outline: none;
    }
</style>
</head>

<body class="bg-light d-flex align-items-center justify-content-center" style="height: 100vh;">
    <!-- Mostra uma mensagem de erro se houver algum problema com o login -->
    <?php
    if ($error_msg) {
        echo "<div class='position-fixed top-0 end-0 p-3' style='z-index: 1050;'>
                  <div class='alert alert-danger alert-dismissible fade show' role='alert'>
                      $msg
                      <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
                  </div>
              </div>";
    }
    ?>
    <!-- Formulário de login -->
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h1 class="h4 text-center mb-4">Login</h1>
                        <!-- Formulário usa o método POST para enviar os dados -->
                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username:</label>
                                <input type="text" id="username" name="username" class="form-control input-verde-claro" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password:</label>
                                <input type="password" id="password" name="password" class="form-control input-verde-claro" required>
                            </div>
                            <div class="d-grid">
                                <input type="submit" value="Entrar" class="btn btn-verde-forte">
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Bootstrap JS (opcional, para componentes interativos) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>