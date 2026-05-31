<?php
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>MonetarySupport</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
    <style>
        :root { --bs-primary: #2c3e50; --bs-primary-rgb: 44, 62, 80; }
        .navbar-brand { font-weight: 700; letter-spacing: -0.5px; }
        .card { border: none; border-radius: 12px; transition: transform 0.2s; }
        .shadow-sm { box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important; }
        @media (max-width: 768px) {
            .container { padding-left: 15px; padding-right: 15px; }
            h3 { font-size: 1.5rem; }
            .btn { padding: 0.5rem 1rem; }
        }
        .nav-link { font-weight: 500; }
        .table-responsive { border-radius: 8px; }
    </style>
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-xl navbar-dark bg-primary sticky-top shadow-sm">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="?module=dashboard">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-wallet2 me-2" viewBox="0 0 16 16">
              <path d="M12.136.326A1.5 1.5 0 0 1 14 1.78V3h.5A1.5 1.5 0 0 1 16 4.5v9a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 13.5v-9a1.5 1.5 0 0 1 1.432-1.499L12.136.326zM5.562 3H13V1.78a.5.5 0 0 0-.621-.484L5.562 3zM1.5 4a.5.5 0 0 0-.5.5v9a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 0-.5-.5h-13z"/>
            </svg>
            MonetarySupport
        </a>
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="?module=dashboard">Dashboard</a></li>
                <li class="nav-item"><a class="nav-link" href="?module=accounts">Cuentas</a></li>
                <li class="nav-item"><a class="nav-link" href="?module=movements">Movimientos</a></li>
                <li class="nav-item"><a class="nav-link" href="?module=fixed_expenses">Gastos fijos</a></li>
                <li class="nav-item"><a class="nav-link" href="?module=financings">Financiamientos</a></li>
                <li class="nav-item"><a class="nav-link" href="?module=transport">Transporte</a></li>
                <li class="nav-item"><a class="nav-link" href="?module=work_expenses">Gastos laborales</a></li>
                <li class="nav-item"><a class="nav-link" href="?module=savings">Ahorros</a></li>
                <li class="nav-item"><a class="nav-link" href="?module=reports">Reportes</a></li>
                <li class="nav-item"><a class="nav-link" href="?module=quick">Registro rápido</a></li>
            </ul>
        </div>
    </div>
</nav>
<main class="container py-4">
