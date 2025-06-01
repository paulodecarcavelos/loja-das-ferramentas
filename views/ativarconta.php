<?php
require "../api/auth.php";  // Inclui o ficheiro de autenticação, que define funções ou variáveis relacionadas com o login.

if(isset($_GET["email"]) && isset($_GET["token"])) {    // Verifica se os parâmetros email e token estão definidos na URL com o método GET
    ativarConta($_GET["email"], $_GET["token"]);        // Chama a função ativarConta passando os parâmetros email e token
    header("Location: login.php");                      // Redireciona para a página de login após a ativação da conta
    exit();                                             // Termina a execução do script após o redirecionamento
}

?>