<?php
require "../api/auth.php";  // Inclui o ficheiro de autenticação, que define funções ou variáveis relacionadas com o login.
$error_msg = false;         // Variável para controlar se há mensagens de erro para mostrar.
$msg = "";                  // Mensagem de erro a ser mostrada, se necessário.

// Verifica se o método de requisição é POST e se os campos necessários estão definidos.
if ($_SERVER["REQUEST_METHOD"]=="POST" && isset($_POST["username"]) && isset($_POST["email"]) && isset($_POST["telemovel"]) && isset($_POST["nif"]) && isset($_POST["password"]) && isset($_POST["confirm_password"])) {
    if (empty($_POST["username"])) {
        $error_msg = true;
        $msg .= "Preencha o campo username.";
    }
    if (empty($_POST["email"])) {
        $error_msg = true;
        $msg .= "Preencha o campo email.";
    }
    if (empty($_POST["telemovel"])) {
        $error_msg = true;
        $msg .= "Preencha o campo telemovel.";
    }
    if (empty($_POST["nif"])) {
        $error_msg = true;
        $msg .= "Preencha o campo nif.";
    }
    if (empty($_POST["password"])) {
        $error_msg = true;
        $msg .= "Preencha o campo password.";
    }
    if (empty($_POST["confirm_password"])) {
        $error_msg = true;
        $msg .= "Preencha o campo confirmar password.";
    }
    if ($_POST["password"] != $_POST["confirm_password"]) {
        $error_msg = true;
        $msg .= "As passwords não coincidem.";
    }
    // Se não houver erros, tenta registar o utilizador.
    if (!$error_msg) {
        // Chama a função registo definida no ficheiro auth.php, passando os dados do formulário. 
        if (registo($_POST["email"], $_POST["username"], $_POST["password"], $_POST["telemovel"], $_POST["nif"])) {
            header("Location: login.php");  // Redireciona para a página de login após o registo bem-sucedido.
        } else {
            $error_msg = true;
            $msg = "O registo falhou. Verifique os seus dados.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registo</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .btn-verde-forte {
            background-color: #007f00 !important; /* Muda a cor de fundo do botão */
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

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h1 class="h4 text-center mb-4">Registo</h1>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Nome de utilizador:</label>
                                <input type="text" id="username" name="username" class="form-control input-verde-claro" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" id="email" name="email" class="form-control input-verde-claro" required>
                            </div>
                            <div class="mb-3">
                                <label for="telemovel" class="form-label">Telemóvel:</label>
                                <input type="text" id="telemovel" name="telemovel" class="form-control input-verde-claro" required>
                            </div>
                            <div class="mb-3">
                                <label for="nif" class="form-label">NIF:</label>
                                <input type="text" id="nif" name="nif" class="form-control input-verde-claro" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password:</label>
                                <input type="password" id="password" name="password" class="form-control input-verde-claro" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirm_password" class="form-label">Confirmar Password:</label>
                                <input type="password" id="confirm_password" name="confirm_password" class="form-control input-verde-claro" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-verde-forte">Registar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Inclusão do Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>