<?php
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MonetarySupport • Pro Finance</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { 
            --bs-primary: #0f172a; 
            --bs-primary-rgb: 15, 23, 42;
            --sidebar-width: 260px;
            --bg-main: #f8fafc;
        }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-main); color: #1e293b; overflow-x: hidden; }
        
        /* Sidebar Styles */
        #sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
            background: #fff;
            border-right: 1px solid #e2e8f0;
            transition: all 0.3s;
        }
        .sidebar-header { padding: 1.5rem; border-bottom: 1px solid #f1f5f9; }
        .sidebar-nav { padding: 1rem; }
        .nav-custom-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: #64748b;
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 0.25rem;
            transition: all 0.2s;
            font-weight: 500;
        }
        .nav-custom-link:hover, .nav-custom-link.active {
            background-color: #f1f5f9;
            color: #0f172a;
        }
        .nav-custom-link i { font-size: 1.25rem; margin-right: 0.75rem; }
        
        /* Main Content */
        #main-wrapper { margin-left: var(--sidebar-width); min-height: 100vh; transition: all 0.3s; }
        .main-header {
            background: rgba(255, 255, 255, 0.8);
            backdrop-filter: blur(8px);
            padding: 1rem 2rem;
            position: sticky;
            top: 0;
            z-index: 999;
            border-bottom: 1px solid #e2e8f0;
        }
        
        /* Responsive Sidebar */
        @media (max-width: 1200px) {
            #sidebar { left: calc(-1 * var(--sidebar-width)); }
            #sidebar.active { left: 0; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
            #main-wrapper { margin-left: 0; }
            .mobile-toggle { display: block !important; }
        }
        
        /* UI Components */
        .card { border: 1px solid #e2e8f0; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.02); }
        .btn-primary { background-color: #0f172a; border-color: #0f172a; }
        .btn-primary:hover { background-color: #1e293b; border-color: #1e293b; }
        
        /* Utility */
        .text-xs { font-size: 0.75rem; }
        .fw-semibold { font-weight: 600; }
    </style>
</head>
<body>

<div id="sidebar">
    <div class="sidebar-header d-flex align-items-center justify-content-between">
        <a href="?module=dashboard" class="text-decoration-none d-flex align-items-center">
            <div class="bg-dark rounded-3 p-2 me-2">
                <i class="bi bi-intersect text-white"></i>
            </div>
            <span class="fs-5 fw-bold text-dark">Monetary</span>
        </a>
    </div>
    <div class="sidebar-nav">
        <small class="text-uppercase text-muted fw-bold text-xs mb-3 d-block ps-2" style="letter-spacing: 1px;">General</small>
        <a href="?module=dashboard" class="nav-custom-link <?= ($_GET['module'] ?? 'dashboard') === 'dashboard' ? 'active' : '' ?>">
            <i class="bi bi-grid-1x2-fill"></i> Dashboard
        </a>
        <a href="?module=movements" class="nav-custom-link <?= ($_GET['module'] ?? '') === 'movements' ? 'active' : '' ?>">
            <i class="bi bi-arrow-left-right"></i> Movimientos
        </a>
        <a href="?module=accounts" class="nav-custom-link <?= ($_GET['module'] ?? '') === 'accounts' ? 'active' : '' ?>">
            <i class="bi bi-wallet2"></i> Cuentas
        </a>
        
        <small class="text-uppercase text-muted fw-bold text-xs mt-4 mb-3 d-block ps-2" style="letter-spacing: 1px;">Planificación</small>
        <a href="?module=fixed_expenses" class="nav-custom-link <?= ($_GET['module'] ?? '') === 'fixed_expenses' ? 'active' : '' ?>">
            <i class="bi bi-clock-history"></i> Gastos fijos
        </a>
        <a href="?module=financings" class="nav-custom-link <?= ($_GET['module'] ?? '') === 'financings' ? 'active' : '' ?>">
            <i class="bi bi-credit-card"></i> Créditos
        </a>
        <a href="?module=savings" class="nav-custom-link <?= ($_GET['module'] ?? '') === 'savings' ? 'active' : '' ?>">
            <i class="bi bi-piggy-bank"></i> Ahorros
        </a>

        <small class="text-uppercase text-muted fw-bold text-xs mt-4 mb-3 d-block ps-2" style="letter-spacing: 1px;">Laboral & Otros</small>
        <a href="?module=work_expenses" class="nav-custom-link <?= ($_GET['module'] ?? '') === 'work_expenses' ? 'active' : '' ?>">
            <i class="bi bi-briefcase"></i> Gastos laborales
        </a>
        <a href="?module=transport" class="nav-custom-link <?= ($_GET['module'] ?? '') === 'transport' ? 'active' : '' ?>">
            <i class="bi bi-bus-front"></i> Transporte
        </a>
        <a href="?module=reports" class="nav-custom-link <?= ($_GET['module'] ?? '') === 'reports' ? 'active' : '' ?>">
            <i class="bi bi-bar-chart-line"></i> Reportes
        </a>
    </div>
</div>

<div id="main-wrapper">
    <header class="main-header d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <button class="btn btn-light mobile-toggle d-none me-3" onclick="document.getElementById('sidebar').classList.toggle('active')">
                <i class="bi bi-list"></i>
            </button>
            <h5 class="mb-0 fw-bold d-none d-md-block">
                <?php
                    $modules = [
                        'dashboard' => 'Dashboard',
                        'accounts' => 'Mis Cuentas',
                        'movements' => 'Historial de Transacciones',
                        'fixed_expenses' => 'Gestión de Gastos Fijos',
                        'financings' => 'Financiamientos',
                        'savings' => 'Estrategias de Ahorro',
                        'work_expenses' => 'Gastos Laborales',
                        'transport' => 'Transporte',
                        'reports' => 'Reportes & Análisis',
                        'quick' => 'Registro Rápido'
                    ];
                    echo $modules[$_GET['module'] ?? 'dashboard'] ?? 'MonetarySupport';
                ?>
            </h5>
        </div>
        <div class="d-flex align-items-center gap-3">
            <a href="?module=quick" class="btn btn-sm btn-dark d-none d-sm-flex align-items-center rounded-pill px-3">
                <i class="bi bi-lightning-fill text-warning me-1"></i> Quick Action
            </a>
            <div class="dropdown">
                <div class="avatar bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 38px; height: 38px; cursor: pointer;" data-bs-toggle="dropdown">
                    C
                </div>
            </div>
        </div>
    </header>
    <main class="p-4 p-md-5">
