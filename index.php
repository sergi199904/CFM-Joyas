<?php
require_once 'includes/db.php';
$query = "SELECT * FROM productos ORDER BY id DESC";
$result = mysqli_query($conn, $query);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>CFM Joyas</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Font Awesome -->
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"
        integrity="sha512-…"
        crossorigin="anonymous"
        referrerpolicy="no-referrer" />
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
 <!-- Tu CSS personalizado -->
 <link href="css/style.css" rel="stylesheet">

</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg custom-nav">
    <div class="container">
      <a class="navbar-brand" href="#">
        <img src="img/logooficial.jpg" alt="Logo" class="logo"> CFM Joyas
      </a>
      <button class="navbar-toggler custom-toggler" type="button"
              data-bs-toggle="collapse" data-bs-target="#navbarNav"
              aria-controls="navbarNav" aria-expanded="false"
              aria-label="Toggle navigation">
        <i class="fas fa-bars"></i>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="#historia">Historia</a></li>
          <li class="nav-item"><a class="nav-link" href="#productos">Productos</a></li>
          <li class="nav-item"><a class="nav-link" href="#contacto">Contacto</a></li>
          <li class="nav-item"><a class="nav-link" href="#ubicacion">Ubicación</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Header -->
  <header class="text-center py-5">
    <h1 class="display-4">CFM Joyas</h1>
    <p class="lead">"Venta de Joyas & Accesorios"</p>
  </header>

  <!-- Carrusel -->
  <div id="mainCarousel" class="carousel slide" data-bs-ride="carousel">
    <div class="carousel-inner">
      <div class="carousel-item active">
        <img src="img/coleccion.jpg" class="d-block w-100" alt="Nueva Colección">
      </div>
      <div class="carousel-item">
        <img src="img/precios.jpg" class="d-block w-100" alt="Precios y Ofertas">
      </div>
      <div class="carousel-item">
        <img src="img/oportunidad.jpg" class="d-block w-100" alt="Oportunidad">
      </div>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
      <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
      <span class="carousel-control-next-icon"></span>
    </button>
  </div>

  <!-- Sección Historia -->
  <section id="historia" class="py-5 text-center">
    <div class="container">
      <h2 class="mb-4">Una parte de nosotros</h2>
      <p class="mx-auto" style="max-width:700px;">
       -- --
      </p>

    </div>
  </section>

  <section id="productos" class="py-5 bg-light">
  <div class="container">
    <h2 class="text-center mb-4">Nuestros productos</h2>
    <div class="row g-4">
      <?php $i = 0; while ($row = mysqli_fetch_assoc($result)) { $i++; ?>
      <div class="col-sm-6 col-md-4">
        <a 
          href="<?= htmlspecialchars($row['instagram']) ?>" 
          target="_blank" rel="noopener noreferrer"
          class="product-link"
        >
          <div class="card product-card">
            <img 
              src="<?= htmlspecialchars($row['imagen']) ?>" 
              class="card-img-top" 
              alt="<?= htmlspecialchars($row['nombre']) ?>"
            >
            <div class="card-img-overlay d-flex align-items-end p-0">
              <div class="w-100 bg-dark bg-opacity-50 text-white py-2 px-3 overlay-text">
                <?= htmlspecialchars($row['nombre']) ?>
              </div>
            </div>
          </div>
        </a>
      </div>
      <?php } ?>
    </div>
  </div>
</section>

  <!-- Sección Contacto -->
  <section id="contacto" class="py-5">
  <div class="container">
    <h2 class="text-center mb-4">Contáctanos</h2>
    <div class="row justify-content-center">
      <div class="col-md-6">
        <form action="send_email.php" method="POST" novalidate>
          <div class="mb-3">
            <input type="text" name="name" class="form-control" placeholder="Nombre" required>
          </div>
          <div class="mb-3">
            <input type="email" name="email" class="form-control" placeholder="Correo Electrónico" required>
          </div>
          <div class="mb-3">
            <textarea name="message" class="form-control" rows="4" placeholder="Tu mensaje" required></textarea>
          </div>
          <div class="text-center">
            <button type="submit" class="btn btn-dark">Enviar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</section>


  <!-- Sección Ubicación -->
  <section id="ubicacion" class="py-5 bg-light">
    <div class="container text-center">
      <h2 class="mb-4">Ubicación</h2>
      <iframe src="https://maps.google.com/maps?q=Zapallar,+Chile&output=embed"
              width="100%" height="300" style="border:0;"></iframe>
    </div>
  </section>

  <!-- Redes Sociales -->
  <div class="social-media text-center py-4">
    <h4>¡Síguenos en nuestras redes sociales!</h4>
    <div class="social-icons d-flex justify-content-center gap-4 mt-3">
      <a href="https://www.facebook.com/profile.php?id=100075879374011"
         target="_blank" class="social-icon">
        <img src="https://upload.wikimedia.org/wikipedia/commons/5/51/Facebook_f_logo_%282019%29.svg"
             alt="Facebook" width="40" height="40">
      </a>
      <a href="https://www.instagram.com/cfmjoyas/"
         target="_blank" class="social-icon">
        <img src="https://upload.wikimedia.org/wikipedia/commons/9/95/Instagram_logo_2022.svg"
             alt="Instagram" width="40" height="40">
      </a>
      <a href="https://wa.me/+56998435160"
         target="_blank" class="social-icon">
        <img src="https://cdn.jsdelivr.net/npm/simple-icons@v3/icons/whatsapp.svg"
             alt="WhatsApp" width="40" height="40">
      </a>
    </div>
  </div>

  <!-- Footer -->
  <footer class="py-4 bg-dark text-center text-white">
    <p class="mt-3">&copy; 2025 CFM Joyas. Todos los derechos reservados.</p>
  </footer>

  <!-- Bootstrap 5 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
  document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.product-link').forEach((el, i) => {
      el.style.animationDelay = (i * 0.1) + 's';
      el.classList.add('visible');
    });
  });
</script>
</body>
</html>
