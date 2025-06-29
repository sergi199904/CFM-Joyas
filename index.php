<?php
require_once 'includes/db.php';

// Filtro por categoría
$categoria_filtro = isset($_GET['categoria']) ? limpiar_input($_GET['categoria']) : '';

// Consulta de productos con filtro
$query = "SELECT * FROM productos WHERE 1=1";
$params = [];
$types = '';

if ($categoria_filtro && $categoria_filtro !== 'todas') {
    $query .= " AND categoria = ?";
    $params[] = $categoria_filtro;
    $types .= 's';
}

$query .= " ORDER BY fecha DESC";

$stmt = $conn->prepare($query);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

// Obtener categorías para el filtro
$categories = $conn->query("SELECT nombre, COUNT(*) as total FROM productos GROUP BY categoria ORDER BY nombre");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>CFM Joyas - Venta de Joyas & Accesorios</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="CFM Joyas - Especialistas en joyas, cerámicas y accesorios únicos. Encuentra las mejores piezas con precios accesibles.">
  
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- CSS personalizado -->
  <link href="css/style.css" rel="stylesheet">
  
  <style>
    .admin-btn {
      position: fixed;
      bottom: 20px;
      right: 20px;
      z-index: 1000;
      background: linear-gradient(45deg, #007bff, #0056b3);
      border: none;
      border-radius: 50px;
      padding: 15px 20px;
      box-shadow: 0 4px 15px rgba(0,123,255,0.3);
      transition: all 0.3s ease;
    }
    
    .admin-btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 6px 20px rgba(0,123,255,0.4);
    }
    
    .product-price {
      position: absolute;
      top: 10px;
      right: 10px;
      background: linear-gradient(45deg, #28a745, #20c997);
      color: white;
      padding: 5px 10px;
      border-radius: 15px;
      font-weight: bold;
      font-size: 0.9rem;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
    }
    
    .category-filter {
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color: white;
      border-radius: 15px;
      padding: 20px;
      margin-bottom: 30px;
    }
    
    .filter-btn {
      background: rgba(255,255,255,0.2);
      border: 1px solid rgba(255,255,255,0.3);
      color: white;
      border-radius: 20px;
      padding: 8px 16px;
      margin: 5px;
      transition: all 0.3s ease;
    }
    
    .filter-btn:hover, .filter-btn.active {
      background: rgba(255,255,255,0.9);
      color: #333;
      transform: translateY(-2px);
    }
    
    .product-card {
      position: relative;
      height: 350px;
      overflow: hidden;
      border-radius: 15px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    
    .product-card .card-img-top {
      height: 250px;
      object-fit: cover;
      transition: transform 0.3s ease;
    }
    
    .product-card:hover .card-img-top {
      transform: scale(1.05);
    }
    
    .product-info {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background: linear-gradient(transparent, rgba(0,0,0,0.8));
      color: white;
      padding: 20px;
      transform: translateY(100%);
      transition: transform 0.3s ease;
    }
    
    .product-card:hover .product-info {
      transform: translateY(0);
    }
    
    /* Mejora para navegación de secciones */
    section {
      scroll-margin-top: 90px; /* Espacio para navbar fija */
    }
    
    /* Asegurar que los títulos no se corten */
    #historia, #productos, #contacto, #ubicacion {
      padding-top: 100px !important;
      margin-top: -20px;
    }
  </style>
</head>
<body>
  <!-- Botón de administración -->
  <a href="admin/login.php" class="btn admin-btn text-white" title="Panel de Administración">
    <i class="fas fa-cog"></i> Admin
  </a>

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
    <h1 class="header-title">CFM Joyas</h1>
    <p class="lead">"Venta de Joyas & Accesorios"</p>
    <p class="text-muted">
      <i class="fas fa-gem"></i> Joyas únicas 
      <i class="fas fa-palette ms-3"></i> Cerámicas artesanales 
      <i class="fas fa-star ms-3"></i> Accesorios especiales
    </p>
  </header>

 <!-- Carrusel Mejorado - Reemplazar en index.php -->
<div id="mainCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="4000">
  <div class="carousel-indicators">
    <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="0" class="active"></button>
    <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="1"></button>
    <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="2"></button>
    <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="3"></button>
    <button type="button" data-bs-target="#mainCarousel" data-bs-slide-to="4"></button>
  </div>
  
  <div class="carousel-inner">
    <div class="carousel-item active">
      <div class="carousel-image-container">
        <img src="img\Carrusel\Image 8.jpg" class="d-block w-100 carousel-img" alt="Collares con Piedras Naturales">
        <div class="carousel-overlay"></div>
      </div>
      </div>
    </div>
    
    <div class="carousel-item">
      <div class="carousel-image-container">
        <img src="img\Carrusel\Image 2.jpg" class="d-block w-100 carousel-img" alt="Pulseras Artesanales">
        <div class="carousel-overlay"></div>
      </div>
    </div>
    
    <div class="carousel-item">
      <div class="carousel-image-container">
        <img src="img\Carrusel\Image 1.jpg" class="d-block w-100 carousel-img" alt="Colección Premium">
        <div class="carousel-overlay"></div>
      </div>
    </div>
    
    <div class="carousel-item">
      <div class="carousel-image-container">
        <img src="img\Carrusel\image 11.jpg" class="d-block w-100 carousel-img" alt="Cerámicas Artesanales">
        <div class="carousel-overlay"></div>
      </div>
    </div>
    
    <div class="carousel-item">
      <div class="carousel-image-container">
        <img src="img\Carrusel\image 10.jpg" class="d-block w-100 carousel-img" alt="Ambiente de Tienda">
        <div class="carousel-overlay"></div>
      </div>
      </div>
    </div>
  </div>
  
  <!-- Controles mejorados -->
  <button class="carousel-control-prev" type="button" data-bs-target="#mainCarousel" data-bs-slide="prev">
    <div class="carousel-control-icon">
      <i class="fas fa-chevron-left"></i>
    </div>
    <span class="visually-hidden">Anterior</span>
  </button>
  
  <button class="carousel-control-next" type="button" data-bs-target="#mainCarousel" data-bs-slide="next">
    <div class="carousel-control-icon">
      <i class="fas fa-chevron-right"></i>
    </div>
    <span class="visually-hidden">Siguiente</span>
  </button>
</div>
  <!-- Sección Historia -->
  <section id="historia" class="py-5 text-center">
    <div class="container">
      <h2 class="mb-4">Una parte de nosotros</h2>
      <p class="mx-auto" style="max-width:700px;">
        En CFM Joyas creamos piezas únicas que reflejan la belleza y elegancia. 
        Desde joyas artesanales hasta cerámicas especiales, cada producto cuenta una historia 
        y representa la pasión por el arte y la calidad.
      </p>
    </div>
  </section>

  <!-- Filtro de Categorías ACTUALIZADO -->
  <section class="py-3" id="filtros">
    <div class="container">
      <div class="category-filter text-center">
        <h5 class="mb-3"><i class="fas fa-filter"></i> Filtrar por categoría</h5>
        <div>
          <!-- Filtro "Todas" -->
          <a href="index.php#productos" class="btn filter-btn <?= empty($categoria_filtro) ? 'active' : '' ?>">
            <i class="fas fa-th"></i> Todas
          </a>
          
          <!-- Categorías principales ORIGINALES -->
          <a href="index.php?categoria=joyas#productos" 
             class="btn filter-btn <?= $categoria_filtro === 'joyas' ? 'active' : '' ?>">
            <i class="fas fa-gem"></i> Joyas
          </a>
          
          <a href="index.php?categoria=ceramicas#productos" 
             class="btn filter-btn <?= $categoria_filtro === 'ceramicas' ? 'active' : '' ?>">
            <i class="fas fa-palette"></i> Cerámicas
          </a>
          
          <a href="index.php?categoria=otros#productos" 
             class="btn filter-btn <?= $categoria_filtro === 'otros' ? 'active' : '' ?>">
            <i class="fas fa-star"></i> Otros
          </a>
          
          <!-- NUEVAS categorías específicas -->
          <a href="index.php?categoria=collares#productos" 
             class="btn filter-btn <?= $categoria_filtro === 'collares' ? 'active' : '' ?>">
            <i class="fas fa-circle-notch"></i> Collares
          </a>
          
          <a href="index.php?categoria=pulseras#productos" 
             class="btn filter-btn <?= $categoria_filtro === 'pulseras' ? 'active' : '' ?>">
            <i class="fas fa-link"></i> Pulseras
          </a>
          
          <a href="index.php?categoria=aretes#productos" 
             class="btn filter-btn <?= $categoria_filtro === 'aretes' ? 'active' : '' ?>">
            <i class="fas fa-earring"></i> Aretes
          </a>
          
          <a href="index.php?categoria=anillos#productos" 
             class="btn filter-btn <?= $categoria_filtro === 'anillos' ? 'active' : '' ?>">
            <i class="fas fa-ring"></i> Anillos
          </a>
        </div>
      </div>
    </div>
  </section>

  <!-- Sección Productos -->
  <section id="productos" class="py-5 bg-light">
    <div class="container">
      <h2 class="text-center mb-4">
        Nuestros productos
        <?php if ($categoria_filtro): ?>
          <small class="text-muted">- <?= ucfirst($categoria_filtro) ?></small>
        <?php endif; ?>
      </h2>
      
      <?php if ($result->num_rows === 0): ?>
        <div class="text-center py-5">
          <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
          <h4 class="text-muted">No hay productos en esta categoría</h4>
          <a href="index.php#productos" class="btn btn-primary mt-3">Ver todos los productos</a>
        </div>
      <?php else: ?>
        <div class="row g-4">
          <?php $i = 0; while ($row = $result->fetch_assoc()) { $i++; ?>
            <div class="col-sm-6 col-md-4 col-lg-3">
              <a href="<?= htmlspecialchars($row['instagram']) ?>" 
                 target="_blank" rel="noopener noreferrer"
                 class="product-link text-decoration-none">
                <div class="card product-card border-0">
                  <!-- Precio -->
                  <div class="product-price">
                    $<?= number_format($row['precio'], 0, ',', '.') ?>
                  </div>
                  
                  <!-- Categoría -->
                  <div class="category-badge">
                    <?= ucfirst(htmlspecialchars($row['categoria'])) ?>
                  </div>
                  
                  <!-- Imagen -->
                  <img src="<?= htmlspecialchars($row['imagen']) ?>" 
                       class="card-img-top" 
                       alt="<?= htmlspecialchars($row['nombre']) ?>">
                  
                  <!-- Información del producto -->
                  <div class="product-info">
                    <h6 class="mb-2"><?= htmlspecialchars($row['nombre']) ?></h6>
                    <p class="mb-2">
                      <i class="fas fa-tag"></i> $<?= number_format($row['precio'], 0, ',', '.') ?> CLP
                    </p>
                    <p class="mb-0">
                      <i class="fab fa-instagram"></i> Ver en Instagram
                    </p>
                  </div>
                </div>
              </a>
            </div>
          <?php } ?>
        </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- Sección Contacto -->
  <section id="contacto" class="py-5">
    <div class="container">
      <h2 class="text-center mb-4">Contáctanos</h2>
      <div class="row justify-content-center">
        <div class="col-md-6">
          <div class="card shadow">
            <div class="card-body">
              <form action="send_email.php" method="POST" novalidate>
                <div class="mb-3">
                  <label class="form-label">Nombre</label>
                  <input type="text" name="name" class="form-control" placeholder="Tu nombre" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Correo Electrónico</label>
                  <input type="email" name="email" class="form-control" placeholder="tu@email.com" required>
                </div>
                <div class="mb-3">
                  <label class="form-label">Mensaje</label>
                  <textarea name="message" class="form-control" rows="4" 
                            placeholder="Cuéntanos sobre tu consulta..." required></textarea>
                </div>
                <div class="text-center">
                  <button type="submit" class="btn btn-dark btn-lg">
                    <i class="fas fa-paper-plane"></i> Enviar Mensaje
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

<!-- Sección Ubicación -->
<section id="ubicacion" class="py-5 bg-light">
  <div class="container text-center">
    <h2 class="mb-4">Ubicación</h2>
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <iframe src="https://maps.google.com/maps?q=-32.5541667,-71.4577222&output=embed"
                width="100%" height="300" style="border:0; border-radius:10px;" class="shadow"></iframe>
        <p class="mt-3 text-muted">
          <i class="fas fa-map-marker-alt"></i> Zapallar, Región de Valparaíso, Chile
        </p>
        <p class="text-muted small">
          <i class="fas fa-compass"></i> 32°33'15.0"S 71°27'27.8"W
        </p>
      </div>
    </div>
  </div>
</section>

  <!-- Redes Sociales -->
  <div class="social-media text-center py-4">
    <h4>¡Síguenos en nuestras redes sociales!</h4>
    <div class="social-icons d-flex justify-content-center gap-4 mt-3">
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
    <div class="container">
      <div class="row">
        <div class="col-md-4">
          <h5><i class="fas fa-gem"></i> CFM Joyas</h5>
          <p class="small">Especialistas en joyas y accesorios únicos</p>
        </div>
        <div class="col-md-4">
          <h6>Categorías</h6>
          <p class="small">
            <i class="fas fa-gem"></i> Joyas<br>
            <i class="fas fa-palette"></i> Cerámicas<br>
            <i class="fas fa-star"></i> Otros Accesorios
          </p>
        </div>
        <div class="col-md-4">
          <h6>Contacto</h6>
          <p class="small">
            <i class="fab fa-whatsapp"></i> +56 9 9843 5160<br>
            <i class="fas fa-envelope"></i> cfmjoyas@gmail.com
          </p>
        </div>
      </div>
      <hr class="my-3">
      <p class="mb-0">&copy; 2025 CFM Joyas. Todos los derechos reservados.</p>
    </div>
  </footer>

  <!-- Bootstrap 5 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      // Animación de entrada para productos
      document.querySelectorAll('.product-link').forEach((el, i) => {
        el.style.animationDelay = (i * 0.1) + 's';
        el.classList.add('visible');
      });
      
      // SCROLL AUTOMÁTICO A PRODUCTOS SI HAY FILTRO APLICADO
      <?php if ($categoria_filtro): ?>
        // Si hay una categoría seleccionada, hacer scroll a productos
        setTimeout(() => {
          document.getElementById('productos').scrollIntoView({
            behavior: 'smooth',
            block: 'start'
          });
        }, 100);
      <?php endif; ?>
      
      // Smooth scroll para navegación con offset para navbar
      document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
          e.preventDefault();
          const targetId = this.getAttribute('href');
          const target = document.querySelector(targetId);
          if (target) {
            // Calcular posición con offset para navbar (90px)
            const targetPosition = target.offsetTop - 90;
            window.scrollTo({
              top: targetPosition,
              behavior: 'smooth'
            });
          }
        });
      });
      
      // Efecto hover para el botón de admin
      const adminBtn = document.querySelector('.admin-btn');
      adminBtn.addEventListener('mouseenter', () => {
        adminBtn.innerHTML = '<i class="fas fa-lock"></i> Acceso Seguro';
      });
      adminBtn.addEventListener('mouseleave', () => {
        adminBtn.innerHTML = '<i class="fas fa-cog"></i> Admin';
      });
    });
  </script>
</body>
</html>