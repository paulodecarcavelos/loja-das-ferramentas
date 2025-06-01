<?php
     // Inicia a sessão PHP. É necessário para aceder ás variáveis da sessão, como a que indica se o utilizador está autenticado.
    session_start();

    // Inclui o ficheiro 'auth.php' contém funções de autenticação, como a verificação se o utilizador é administrador.
    require '../api/auth.php';

    // Verifica se o utilizador atual "não" é um administrador.
    if( !isAdmin() ){
        // Se o utilizador não for administrador, redireciona-o para a página inicial do site.
        header("Location: ../index.php");

        // Termina imediatamente a execução do script após o redirecionamento.
        exit();
    }
?>
<!-- Configurações da página, objetos e estilo (com Bootstrap) -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área de administração</title>
    <!-- Importa o ficheiro CSS do framework Bootstrap (versão 5.3.0) a partir da CDN do jsDelivr.
        Isto permite usar estilos e componentes já prontos do Bootstrap, como botões, tabelas, formulários, etc.,
        evitando ter de fazer o download do ficheiro CSS para o servidor local. -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
    .bg-verde-forte {
        background-color: #007f00 !important; /* Côr para fundo verde escuro e forte */
    }
</style>
</head>

<!-- Início do corpo (body) do documento HTML e aplica a classe "bg-light" do Bootstrap,
que atribui um fundo com uma cor clara (geralmente cinzento muito claro). -->
<body class="bg-light">

    <!-- 
        Cria uma barra de navegação (navbar) responsiva com Bootstrap.
        As classes definem o comportamento e estilo da navbar:
        - 'navbar': classe base da barra de navegação.
        - 'navbar-expand-lg': torna a navbar expansível a partir de larguras grandes (breakpoint lg).
        - 'navbar-dark': usa texto e ícones claros, ideal para fundos escuros.
        - 'bg-primary': aplica a cor de fundo principal do Bootstrap (por defeito é azul).
        - 'mb-4': aplica margem inferior (spacing) para separar visualmente a navbar do conteúdo abaixo.
    -->
    <!-- <nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4"> fundo azul -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-verde-forte mb-4"> <!-- fundo verde personalizado com CSS no 'head' -->
        <div class="container-fluid">
            <!-- A marca da navbar, que é um link para a página inicial ou título da aplicação. -->
            <a class="navbar-brand" href="#">Administração</a>
            <!-- Botão que aparece em ecrãs pequenos para expandir/colapsar o menu -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>
    <!-- Container centraliza e aplica padding horizontal à navbar -->
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0 fw-bold">Área de administração</h1>
            <!-- Botão que aparece em ecrãs pequenos para expandir/colapsar o menu -->
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#insertProductModal">
                <i class="bi bi-plus-circle"></i> Inserir Novo Produto
            </button>
        </div>

        <?php
        // Inclui o ficheiro de ligação à base de dados, apenas uma vez, para evitar inclusões duplicadas.
        require_once '../api/db.php';
        // Prepara uma query SQL com a seleção dos campos da tabela 'produtos', ordenados por 'id' de forma descendente (mais recentes primeiro).
        $stmt = $con->prepare("SELECT id, nome, preco, descricao, imagem FROM produtos ORDER BY id DESC");
        // Executa a query preparada.
        $stmt->execute();
        // Obtém o resultado da query executada na forma de result set.
        $result = $stmt->get_result();
        // Converte o result set num array associativo, onde cada elemento é um produto com os nomes dos campos como chaves.
        $produtos = $result->fetch_all(MYSQLI_ASSOC);
        // Fecha o statement preparado para libertar recursos.
        $stmt->close();
        // Fecha a ligação à base de dados. É boa prática fechar a ligação à BD quando já não é necessária.
        $con->close();
        ?>

        <!-- Cria um cartão (card), com uma ligeira sombra, do Bootstrap para mostrar a lista de produtos -->
        <div class="card shadow-sm">
            <!-- Cabeçalho do cartão com fundo verde forte e texto branco, onde é colocado o título "Produtos" -->
            <div class="card-header bg-verde-forte text-white">
                <h2 class="h5 mb-0">Produtos</h2>
            </div>
            <!-- Corpo do cartão, onde é colocada a tabela com os produtos -->
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <!-- Cabeçalho da tabela com fundo claro, onde são definidos os nomes das colunas -->
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Nome</th>
                                <th>Preço</th>
                                <th>Descrição</th>
                                <th>Imagem</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Loop através do array de produtos para criar uma linha da tabela para cada produto -->
                            <?php foreach ($produtos as $produto): ?>
                                <tr>
                                    <td><?= htmlspecialchars($produto['id']) ?></td>
                                    <td><?= htmlspecialchars($produto['nome']) ?></td>
                                    <td><span class="badge bg-success"><?= number_format($produto['preco'], 2, ',', '.') ?> €</span></td>
                                    <td><?= htmlspecialchars($produto['descricao']) ?></td>
                                    <td>
                                        <?php if (!empty($produto['imagem'])): ?>
                                            <img src="data:image/jpeg;base64,<?= base64_encode($produto['imagem']) ?>" alt="<?= htmlspecialchars($produto['nome']) ?>" class="img-thumbnail" style="width: 80px; height: auto;">
                                        <?php else: ?>
                                            <span class="text-muted">Sem imagem</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <!-- Botões de ação para eliminar e editar o produto -->
                                        <button class="btn btn-danger btn-sm me-1" title="Eliminar"
                                            onclick="if(confirm('Tem a certeza que deseja eliminar este produto?')) { fetch('../api/admin/delete_product.php?id=<?= $produto['id'] ?>').then(r => r.json()).then(result => { if(result.status === 'success'){ location.reload(); } else { alert(result.message || 'Erro ao eliminar produto.'); } }); }">
                                            <!-- Ícone de lixeira para indicar eliminação -->
                                            <i class="bi bi-trash"></i>
                                        </button>
                                        <!-- Botão para abrir o modal de edição do produto -->
                                        <button class="btn btn-warning btn-sm" title="Editar"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editProductModal"
                                            data-id="<?= htmlspecialchars($produto['id']) ?>"
                                            data-nome="<?= htmlspecialchars($produto['nome']) ?>"
                                            data-preco="<?= htmlspecialchars($produto['preco']) ?>"
                                            data-descricao="<?= htmlspecialchars($produto['descricao']) ?>"
                                        >
                                            <!-- Ícone de lápis para indicar edição -->
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <!-- Botão para sair da área de administração -->
    <a href="login.php" class="btn btn-outline-danger" style="position: absolute; top: 80px; right: 35px;">Sair</a>

    <!-- Modal para edição-->
    <div class="modal fade" id="editProductModal" tabindex="-1" aria-labelledby="editProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="editProductForm" method="post" enctype="multipart/form-data" action="../api/admin/edit_product.php">
                    <div class="modal-header bg-warning">
                        <h5 class="modal-title" id="editProductModalLabel">Editar Produto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id" id="editProductId">
                        <div class="mb-3">
                            <label for="editProductName" class="form-label">Nome do Produto</label>
                            <input type="text" class="form-control" id="editProductName" name="nome" required>
                        </div>
                        <div class="mb-3">
                            <label for="editProductPrice" class="form-label">Preço</label>
                            <input type="number" step="0.01" class="form-control" id="editProductPrice" name="preco" required>
                        </div>
                        <div class="mb-3">
                            <label for="editProductDescription" class="form-label">Descrição</label>
                            <textarea class="form-control" id="editProductDescription" name="descricao" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="editProductImage" class="form-label">Imagem (deixe em branco para não alterar)</label>
                            <input type="file" class="form-control" id="editProductImage" name="imagem">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-warning">Salvar Alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal para inserção -->
    <div class="modal fade" id="insertProductModal" tabindex="-1" aria-labelledby="insertProductModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="../api/admin/insert_product.php" enctype="multipart/form-data">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="insertProductModalLabel">Inserir Novo Produto</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="productName" class="form-label">Nome do Produto</label>
                            <input type="text" class="form-control" id="productName" name="nome" required>
                        </div>
                        <div class="mb-3">
                            <label for="productPrice" class="form-label">Preço</label>
                            <input type="number" step="0.01" class="form-control" id="productPrice" name="preco" required>
                        </div>
                        <div class="mb-3">
                            <label for="productDescription" class="form-label">Descrição</label>
                            <textarea class="form-control" id="productDescription" name="descricao" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="productImage" class="form-label">Imagem</label>
                            <input type="file" class="form-control" id="productImage" name="imagem" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success">Inserir Produto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Toast for feedback -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 1055">
        <!-- Elemento Toast para mostrar mensagens de feedback -->
        <div id="feedbackToast" class="toast align-items-center text-bg-verde-forte border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <!--Insere um ícone do Bootstrap Icons com as seguintes características:
                - 'bi bi-check-circle-fill': "check" (visto) dentro de um círculo preenchido.
                - 'me-2': adiciona margem à direita (margin-end), útil para afastar o ícone do conteúdo seguinte.
                - 'style="font-size: 1.5rem;"': define o tamanho do ícone manualmente para 1.5 rem (unidade relativa ao tamanho da fonte base).
                -->
                <i class="bi bi-check-circle-fill me-2" style="font-size: 1.5rem;"></i>
            <div class="d-flex">
                <div class="toast-body" id="toastMessage"></div>
                <!-- Botão para fechar o toast -->
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <!-- Bootstrap Icons CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <script>
    // Para preencher o modal de edição com os dados do produto
    document.addEventListener('DOMContentLoaded', function() {
        // Seleciona o modal de edição e adiciona um evento para quando for mostrado
        var editModal = document.getElementById('editProductModal');
        editModal.addEventListener('show.bs.modal', function (event) {
            // Obtém o botão que acionou o modal (o botão de editar)
            // 'event.relatedTarget' contém o botão que disparou o evento
            var button = event.relatedTarget;
            document.getElementById('editProductId').value = button.getAttribute('data-id');
            document.getElementById('editProductName').value = button.getAttribute('data-nome');
            document.getElementById('editProductPrice').value = button.getAttribute('data-preco');
            document.getElementById('editProductDescription').value = button.getAttribute('data-descricao');
            document.getElementById('editProductImage').value = '';
        });

        // Submeter edição via AJAX       
        document.getElementById('editProductForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData
                });
                const result = await response.json();

                let message = result.message || 'Produto atualizado com sucesso!';
                let toastEl = document.getElementById('feedbackToast');
                let toastMsg = document.getElementById('toastMessage');
                toastMsg.textContent = message;

                toastEl.classList.remove('text-bg-verde-forte', 'text-bg-danger', 'text-bg-success');
                if (result.status === 'success') {
                    toastEl.classList.add('text-bg-success');
                    var modal = bootstrap.Modal.getInstance(document.getElementById('editProductModal'));
                    modal.hide();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastEl.classList.add('text-bg-danger');
                }

                var toast = new bootstrap.Toast(toastEl);
                toast.show();

            } catch (error) {
                let toastEl = document.getElementById('feedbackToast');
                let toastMsg = document.getElementById('toastMessage');
                toastMsg.textContent = 'Erro ao atualizar produto.';
                toastEl.classList.remove('text-bg-verde-forte', 'text-bg-success');
                toastEl.classList.add('text-bg-danger');
                var toast = new bootstrap.Toast(toastEl);
                toast.show();
            }
        });

        // Submeter inserção via AJAX
        document.querySelector('#insertProductModal form').addEventListener('submit', async function(e) {
            e.preventDefault();

            const form = e.target;
            const formData = new FormData(form);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                let message = result.message || 'Produto inserido com sucesso!';
                let toastEl = document.getElementById('feedbackToast');
                let toastMsg = document.getElementById('toastMessage');
                toastMsg.textContent = message;

                toastEl.classList.remove('text-bg-verde-forte', 'text-bg-danger', 'text-bg-success');
                if (result.status === 'success') {
                    toastEl.classList.add('text-bg-success');
                    form.reset();
                    var modal = bootstrap.Modal.getInstance(document.getElementById('insertProductModal'));
                    modal.hide();
                    setTimeout(() => location.reload(), 1000);
                } else {
                    toastEl.classList.add('text-bg-danger');
                }

                var toast = new bootstrap.Toast(toastEl);
                toast.show();

            } catch (error) {
                let toastEl = document.getElementById('feedbackToast');
                let toastMsg = document.getElementById('toastMessage');
                toastMsg.textContent = 'Erro ao inserir produto.';
                toastEl.classList.remove('text-bg-verde-forte', 'text-bg-success');
                toastEl.classList.add('text-bg-danger');
                var toast = new bootstrap.Toast(toastEl);
                toast.show();
            }
        });
    });
    </script>
    <!-- Importa o JavaScript do Bootstrap a partir da CDN do jsDelivr. -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>