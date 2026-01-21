<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Productos Más Vendidos</title>

    <!-- Tu hoja de estilos general -->
    <link rel="stylesheet" href="StylesGeneralesPublic.css">

    <!-- Estilos SOLO para esta página -->
    <style>
        body {
            background-color: #bccbd2;
        }

        /* Tarjeta principal limpia */
        .contenedor-detalle {
            max-width: 1050px;
            margin: 30px auto 70px;
            padding: 40px 45px;
            background: #ffffff; /* SOLO BLANCO */
            border-radius: 20px;
            box-shadow: 0 12px 30px rgba(0,0,0,.12);
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 35px;
            align-items: start;
        }

        /* Encabezado */
        .encabezado-detalle {
            grid-column: 1 / -1;
            margin-bottom: 18px;
        }

        .badge-mas-vendido {
            display: inline-block;
            padding: 6px 16px;
            border-radius: 50px;
            background: #ff8c42;
            color: #fff;
            font-weight: 700;
            font-size: 11px;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }

        .encabezado-detalle h1 {
            font-size: 30px;
            margin: 0;
        }

        .tagline {
            font-size: 14px;
            margin: 4px 0 0;
            color: #6b7280;
        }

        /* IMAGEN SIN FONDO — SOLO LA IMAGEN BLANCA ORIGINAL */
        .producto-img {
            padding: 0;
            background: transparent !important;
            border-radius: 0 !important;
            box-shadow: none !important;
        }

        .producto-img img {
            width: 100%;
            max-height: 450px;
            object-fit: contain;
            display: block;
            margin: 0 auto;
            border-radius: 12px;
            box-shadow: none !important; /* SIN SOMBRA EXTRA */
        }

        /* Información del producto */
        .producto-info h2 {
            font-size: 26px;
            margin-bottom: 6px;
        }

        .producto-codigo {
            color: #6b7280;
            margin-bottom: 10px;
        }

        .producto-precio {
            display: inline-block;
            background: #10b981;
            color: #fff;
            padding: 8px 18px;
            border-radius: 999px;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .producto-descripcion {
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .producto-caracteristicas {
            margin-left: 20px;
        }

        .producto-caracteristicas li {
            margin-bottom: 4px;
        }

        /* Botón volver */
        .btn-volver {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 22px;
            background: #1f6feb;
            color: #fff;
            border-radius: 999px;
            font-weight: 700;
            text-decoration: none;
            box-shadow: 0 5px 15px rgba(31,111,235,.35);
        }

        .btn-volver:hover {
            opacity: 0.95;
            transform: translateY(-2px);
        }

        /* RESPONSIVE CELULAR */
        @media (max-width: 900px) {
            .contenedor-detalle {
                grid-template-columns: 1fr;
                margin: 20px 15px 60px;
                padding: 20px 20px;
            }

            .producto-img img {
                max-height: 380px;
            }
        }
    </style>
</head>

<body>
    <header>
        <section class="logo">
            <img src="ACASALogoAcerosA.png" alt="Logo de Aceros Alonso">
            <h2>Aceros Alonso</h2>
        </section>

        
    </header>

    <main>
        <section class="contenedor-detalle">

            <!-- ENCABEZADO -->
            <div class="encabezado-detalle">
                <span class="badge-mas-vendido">Producto más vendido</span>
                <h1>Productos Más Vendidos</h1>
                <p class="tagline">Destacado por su rendimiento y preferido por maestros de obra.</p>
            </div>

            <!-- IMAGEN PRINCIPAL SIN FONDO EXTRA -->
            <div class="producto-img">
                <img src="mas_venta.jpeg" alt="Producto más vendido">
            </div>

            <!-- INFORMACIÓN -->
            <div class="producto-info">
                <h2>Cemento SUPERSTRONG 50 kg</h2>
                <p class="producto-codigo">Código: MV-001</p>

                <span class="producto-precio">$260.00 MXN</span>

                <p class="producto-descripcion">
                    Cemento de alta resistencia ideal para construcción en general.
                    Utilizado por su rendimiento y durabilidad, siendo uno de los productos más vendidos.
                </p>

                <ul class="producto-caracteristicas">
                    <li>Presentación: costal de 50 kg.</li>
                    <li>Uso: estructuras, losas y muros.</li>
                    <li>Color: gris estándar.</li>
                    <li>Almacenar en lugar seco y ventilado.</li>
                </ul>

                <a href="paginaPrincipal.php" class="btn-volver">Volver al Inicio</a>
            </div>

        </section>
    </main>

</body>
</html>
