<?php
// Inclui o ficheiro de autenticação, que define funções ou variáveis relacionadas com o login.
require '../api/auth.php';
// Inicia a sessão para aceder às variáveis de sessão.
session_start();
// Verifica se a variável de sessão "user" não está definida (ou seja, o utilizador não está autenticado).
if(!isset($_SESSION["user"])){
    // Se não estiver autenticado, redireciona para a página de login e termina a execução do script.
    header("Location: views/login.php");
    exit();
}

require '../api/db.php';    // Inclui o ficheiro de ligação à base de dados.

// Prepara a query para evitar injeções SQL: vai buscar os produtos no carrinho do utilizador atual, juntando com a tabela de produtos.
$sql = $con->prepare("SELECT p.id, p.nome, p.descricao, p.preco, p.imagem, c.quantidade FROM produtos p JOIN Carrinho c ON p.id=c.produtoId WHERE c.userId=?");
// Liga o ID do utilizador atual à query preparada para ir buscar os produtos ao carrinho.
$sql->bind_param("i", $_SESSION["user"]["id"]);
$sql->execute();    // Executa a query preparada.
$result = $sql->get_result();   // Obtém o resultado da execução da query.
echo $result->num_rows; // Debug: mostra o número de linhas devolvidas pela query.

// Define a variável do ID do cliente PayPal. Este ID é necessário para integrar o PayPal na página de checkout.
// O ID do cliente é usado para autenticar as transações com a API do PayPal.
$PAYPAL_CLIENT_ID = "";

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrinho de compras</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .cart-item {
            border: 1px solid #e3e3e3;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            background: #fff;
            box-shadow: 0 2px 8px rgba(0,0,0,0.03);
        }
        .cart-item img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
        }
        .cart-actions {
            display: flex;
            gap: 10px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Carrinho de Compras</h2>
        <?php if ($result->num_rows === 0): ?>
            <div class="alert alert-info">O seu carrinho está vazio.</div>
        <?php endif; ?>
        <div class="row">
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="col-md-12 cart-item d-flex align-items-center">
                    <?php 
                        $image = base64_encode($row['imagem']);
                        $src = 'data:image/jpeg;base64,' . $image;
                    ?>
                    <div class="me-4">
                        <img src="<?php echo $src ?>" alt="Imagem">
                    </div>
                    <div class="flex-grow-1">
                        <h5><?php echo htmlspecialchars($row['nome']); ?></h5>
                        <p class="mb-1 text-muted"><?php echo htmlspecialchars($row['descricao']); ?></p>
                        <div class="fw-bold mb-2"><?php echo number_format($row['preco'], 2, ',', '.'); ?> €</div>
                        <div class="cart-actions">
                            <form action="../api/update_cart.php" method="post" class="d-flex align-items-center gap-2">
                                <input type="hidden" name="produtoId" value="<?php echo $row['id']; ?>">
                                <input type="number" name="quantidade" value="<?php echo $row['quantidade']; ?>" min="1" class="form-control form-control-sm" style="width: 70px;">
                                <button type="submit" class="btn btn-primary btn-sm">Atualizar</button>
                            </form>
                            <form action="../api/delete_cart.php" method="post">
                                <input type="hidden" name="produtoId" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm">Remover</button>
                            </form>
                        </div>
                    </div>
                    <div class="ms-auto text-center">
                        <span class="badge bg-secondary fs-6">Subtotal: <?php echo $row["quantidade"]*$row['preco']; ?> €</span>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <?php
        // Faz o reset do ponteiro do resultado para calcular o total.
        $result->data_seek(0);
        $total=0;
        // Percorre novamente os resultados para calcular o total.
        while($row=$result->fetch_assoc()) {
            // Calcula o total multiplicando a quantidade pelo preço de cada produto.  
            $total+=$row["quantidade"]*$row["preco"];
        }
        ?>
        <?php if ($total>0): ?>
            <div class="d-flex justify-content-end mt-4">
                <h4>Total do Pedido: <span class="badge bg-success"><?php echo number_format($total, 2, ',', '.'); ?> €</span></h4>
            </div>
        <?php endif; ?>
    </div>
    <div class="d-flex justify-content-center">

        <div id="paypal-button-container" class="w-50"></div>
    </div>

    <!-- Botão de navegação para voltar ás compras-->
    <div class="container mt-4 d-flex justify-content-between">
        <a href="../index.php" class="btn btn-outline-secondary">← Continuar a Comprar</a>
    </div>

    <!-- Script do PayPal -->
    <script src=<?php echo "https://www.paypal.com/sdk/js?client-id=$PAYPAL_CLIENT_ID&currency=EUR" ?>></script>

    <script>
        paypal.Buttons({
            createOrder: function(data, actions) {
                return actions.order.create({
                    purchase_units: [{
                        amount: {
                            value: '<?php echo $total; ?>'
                        }
                    }]
                });
            },
            onApprove: function(data, actions) {
                return actions.order.capture().then(function(details) {
                    window.location.href = "/UFCD_PHP/24198_Loja/views/finish.php";
                });
            },
            onError: function(err) {
                console.error('Erro no pagamento:', err);
                alert('Ocorreu um erro durante o pagamento. Tente novamente.');
            }
        }).render('#paypal-button-container');
    </script>

    <!-- Inclusão do Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
