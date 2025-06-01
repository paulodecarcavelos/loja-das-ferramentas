<?php

require '../api/auth.php';

session_start();

if(!isset($_SESSION["user"])){
    header("Location: views/login.php");
    exit();
}

require '../api/db.php';


$sql = $con->prepare("DELETE FROM Carrinho WHERE userId = ?");  // Prepara a declaração SQL para evitar injeções SQL
$userId = $_SESSION["user"]["id"];                              // Obtém o ID do utilizador da sessão
$sql->bind_param("i", $userId);                                 // Liga o parâmetro userId à declaração SQL
$sql->execute();                                                // Executa a declaração SQL
if ($sql->affected_rows>0) {                                    // Verifica se alguma linha foi afetada, ou seja, se o carrinho foi limpo
    // Carrinho limpo com sucesso
    echo "Carrinho limpo com sucesso.";
    unset($_SESSION["carrinho"]);                               // Limpa a variável de sessão do carrinho
    header("Location: /24198_Loja/views/finish.php");           // Redireciona para a página de finalização
    exit();                                                     // Termina a execução do script após o redirecionamento                                   
    
} else {
    // Erro ao limpar o carrinho
    echo "Erro ao limpar o carrinho.";
}
$sql->close();  // Fecha a declaração SQL

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Agradecimentos</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column justify-content-center align-items-center vh-100">
    <div class="card shadow p-4" style="max-width: 400px;">
        <h1 class="mb-4 text-center">Agradecemos a sua encomenda, volte sempre.</h1>
        <form action="/24198_Loja/index.php" class="text-center">
            <button type="submit" class="btn btn-primary">Voltar</button>
        </form>
    </div>
</body>
</html>
