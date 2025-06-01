<?php
    session_start();                    // Garante que a sessão está iniciada antes de destruí-la
    session_destroy();                  // Termina a sessão.
    header("Location: logout.php");     // Redireciona para o logout
    exit();                             // Termina a execução do script após o redirecionamento
?>