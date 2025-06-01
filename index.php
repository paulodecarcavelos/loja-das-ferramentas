<?php
// Inclui o ficheiro de autenticação, que define funções ou variáveis relacionadas com o login.
require 'api/auth.php';

// Inicia a sessão para aceder às variáveis de sessão.
session_start();

// Verifica se a variável de sessão "user" não está definida (ou seja, o utilizador não está autenticado).
if(!isset($_SESSION["user"])){
    // Se não estiver autenticado, redireciona para a página de login e termina a execução do script.
    header("Location: views/login.php");
    exit();
}

// Inclui o ficheiro de ligação à base de dados.
require 'api/db.php';

// Ternário para capturar o parâmetro de pesquisa da URL (GET).
// Se existir, aplica real_escape_string para evitar injeções SQL.
$search = isset($_GET['search']) ? $con->real_escape_string($_GET['search']) : '';

// Define a query base que vai buscar os produtos com os campos necessários.
$sql = "SELECT id, nome, descricao, preco, imagem FROM produtos";

// Se o campo de pesquisa não estiver vazio, adiciona cláusulas WHERE à query
// para procurar no nome ou descrição (utilizando LIKE com wildcards).
if ($search !== '') {
    $sql .= " WHERE nome LIKE '%$search%' OR descricao LIKE '%$search%'";
}

// Executa a query.
$result = $con->query($sql);

// Inicializa o array onde serão guardados os produtos.
$produtos = [];

// Se a query tiver resultados,
if ($result && $result->num_rows > 0) {
    // percorre cada linha do resultado e adiciona os dados ao array $produtos como arrays associativos.
    while($row = $result->fetch_assoc()) {
        $produtos[] = $row;
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homepage</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa; /* Cor de fundo clara */
        }
        .bg-verde-forte {
            background-color: #007f00 !important; /* Verde forte */
        }
        .btn-verde-forte {
            background-color: #007f00 !important; /* Verde forte para botões */
            color: white !important;
        }
        .card {
            border: none; /* Remove bordas dos cartões */
        }
        .card-img-top {
            height: 180px; /* Altura fixa para as imagens dos produtos */
            object-fit: cover; /* Cobre o espaço sem distorcer a imagem */
        }
        .pagination .page-item.active .page-link {
            background-color: #007f00; /* Cor de fundo verde forte para a página ativa */
            border-color: #007f00; /* Borda verde forte para a página ativa */
        }
        .pagination .page-link {
            color: #007f00; /* Cor do texto dos links de paginação */
        }
        .pagination .page-link:hover {
            background-color: #005f00; /* Cor de fundo verde escuro ao passar o mouse */
            color: white; /* Cor do texto ao passar o mouse */
        }
        .bg-verde-forte {
            background-color: #007f00 !important; /* Verde forte para o rodapé */
        }
        /* footer p {
            margin: 0; /* Remove margem do parágrafo no rodapé 
        }*/

        .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between; /* Distribui o espaço entre os elementos */
        }
        .card-text {
            flex-grow: 1; /* Permite que a descrição ocupe o espaço restante */
        }
        .mt-auto {
            margin-top: auto; /* Empurra o conteúdo para o final do cartão */
        }
        .form-control-sm {
            width: 70px; /* Largura fixa para o campo de quantidade */
        }
        .form-control {
            background-color: #e6f9e6; /* Verde claro para os campos de entrada */
            border: 1px solid #b2d8b2; /* Bordas em tom verde claro */
            color: #000; /* Cor do texto */
        }
        .form-control:focus {
            background-color: #d4f5d4 !important; /* Tom ligeiramente mais escuro ao focar */
            border-color: #66cc66;
            box-shadow: 0 0 0 0.2rem rgba(0, 128, 0, 0.25);
            outline: none;
        }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-verde-forte mb-4">
    <div class="container">
        <a class="navbar-brand" href="#">Loja das Ferramentas</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <?php if(isAdmin()){ ?>
                    <li class="nav-item">
                        <a class="nav-link" href="views/areaadmin.php">Área de administração</a>
                    </li>
                <?php } ?>
                <li class="nav-item">
                    <a class="nav-link" href="views/logout.php">Logout</a>
                </li>
                   <li class="nav-item">
                    <a class="nav-link" href="views/cart.php">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16" style="margin-right: 4px;">
                            <path d="M0 1.5A.5.5 0 0 1 .5 1h1a.5.5 0 0 1 .485.379L2.89 5H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 14H4a.5.5 0 0 1-.491-.408L1.01 2H.5a.5.5 0 0 1-.5-.5zm3.14 4l1.25 6.5h7.22l1.25-6.5H3.14zM5.5 16a1 1 0 1 0 0-2 1 1 0 0 0 0 2zm7 0a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                        </svg>
                    </a>
                </li>

            </ul>
        </div>
    </div>
</nav>

<!-- Uso de Paginação para mostrar os produtos listados -->
<?php
$por_pagina = 5; // Define a quantidade de produtos por página
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_atual - 1) * $por_pagina;

$sql_total = "SELECT COUNT(*) AS total FROM produtos";
$total_result = $con->query($sql_total);
$total_produtos = $total_result->fetch_assoc()['total'];
$total_paginas = ceil($total_produtos / $por_pagina);

// Ordenação ascendente dos produtos pelo seu campo 'nome'
$sql = "SELECT id, nome, descricao, preco, imagem FROM produtos";
if ($search !== '') {
    $sql .= " WHERE nome LIKE '%$search%' OR descricao LIKE '%$search%'";
}
$sql .= " ORDER BY nome ASC LIMIT $por_pagina OFFSET $offset";

$result = $con->query($sql);
//$sql = "SELECT id, nome, descricao, preco, imagem FROM produtos LIMIT $por_pagina OFFSET $offset";
?>

<div class="container">

    <form class="row mb-4" method="get" action="">
        <div class="col-md-10">
            <input type="text" class="form-control" name="search" placeholder="Encontre aqui o que precisa..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-verde-forte w-100">Pesquisar</button>
        </div>
    </form>

    <div class="row g-4">
    <?php foreach ($produtos as $produto): ?>
        <div class="col-sm-6 col-md-4 col-lg-3">
            <div class="card h-100 shadow-sm">
                <?php
                // Display product image directly from DB (assuming 'imagem' is BLOB or base64 string)
                if (!empty($produto['imagem'])) {
                    // If it's binary data (BLOB), encode as base64
                    $imgData = base64_encode($produto['imagem']);
                    $src = 'data:image/jpeg;base64,' . $imgData;
                } else {
                    // Placeholder image if none exists
                    $src = 'https://via.placeholder.com/300x180?text=Sem+Imagem';
                }
                ?>
                <img src="<?php echo $src; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($produto['nome']); ?>" style="height: 180px; object-fit: cover;">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title"><?php echo htmlspecialchars($produto['nome']); ?></h5>
                    <p class="card-text"><?php echo htmlspecialchars($produto['descricao']); ?></p>
                    <div class="mt-auto">
                        <strong class="text-success">€<?php echo number_format($produto['preco'], 2, ',', '.'); ?></strong>
                        <form method="post" action="api/add_to_cart.php" class="mt-3 d-flex align-items-center gap-2">
                            <input type="hidden" name="produto_id" value="<?php echo $produto['id']; ?>">
                            <input type="number" name="quantidade" value="1" min="1" class="form-control form-control-sm" style="width: 70px;">
                            <button type="submit" class="btn btn-outline-primary btn-sm">Adicionar ao carrinho</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>

</div>

<nav aria-label="Paginação">
    <ul class="pagination justify-content-center mt-4">
        <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
            <li class="page-item <?= ($pagina_atual == $i) ? 'active' : '' ?>">
                <a class="page-link" href="?pagina=<?= $i ?>&search=<?= urlencode($search) ?>"><?= $i ?></a>
            </li>
        <?php endfor; ?>
    </ul>
</nav>

<!-- Rodapé -->
<footer class="bg-verde-forte text-white text-center py-3 mt-5">
    <p>&copy; <?= date("Y") ?> Loja das Ferramentas | Desenvolvido por Paulo Frutuoso</p>
</footer>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>