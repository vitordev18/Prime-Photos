<?php 
include 'templates/header.php'; 
?>

<section id="inicio" class="main-section">
  <h1 class="main-description main-title">Não vendemos apenas fotos.</h1>
  <p class="main-subtext">
    Na Prime Photos, cada imagem é uma memória viva. Transforme momentos
    especiais em recordações tangíveis e eternize suas histórias com
    qualidade e emoção.
  </p>
  <a href="#produtos" class="main-cta">Ver produto</a>
</section>

<section id="produtos" class="products-section">
  <div class="section-container">
    <h2 class="section-title">Produto</h2>
    <ul class="products-grid">
      <li class="product-card">
        <article>
          <figure class="product-image">
            <div class="image-placeholder">
              <img
                src="/assets/Elementos/Polaroid (vermelha).svg"
                alt="Foto Polaroid"
              />
            </div>
          </figure>
          <h3 class="product-title">Foto Polaroid</h3>
          <p class="product-description">
            Impressões de alta qualidade em papel fotográfico premium.
          </p>
          <p class="product-price">R$ 8,00</p>
          <p class="product-stock in-stock">Em estoque</p>
          <form method="POST" action="/back-end/adicionar_carrinho.php">
            <input type="hidden" name="id_produto" value="2"> 
            <button class="purchase-button" aria-label="Adicionar Fotos Polaroid ao carrinho">
              Adicionar ao carrinho
            </button>
          </form>
        </article>
      </li>
    </ul>
  </div>
</section>

<?php include 'templates/footer.php'; ?>