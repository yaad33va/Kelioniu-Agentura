<!DOCTYPE html>
<html lang="lt">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        body {
            background-color: #f8f9fa;
        }

        .pastel-card {
            border: none;
            border-radius: 16px;
            padding: 30px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            font-size: 1.1rem;
        }

        .pastel-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }

        .pastel-bg-1 { background-color: #f8d7da; }
        .pastel-bg-2 { background-color: #d1ecf1; }
        .pastel-bg-3 { background-color: #d4edda; }
        .pastel-bg-4 { background-color: #fff3cd; }
        .pastel-bg-5 { background-color: #e2e3e5; }
        .pastel-bg-6 { background-color: #e8d4f0; }

        a.card-wrapper {
            text-decoration: none;
            color: inherit;
        }

        .card-title {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .card-link {
            font-weight: 500;
            color: #333;
            text-decoration: underline;
        }

        .top-bar-wrapper {
            position: relative;
            width: 100vw; /* Ensure it spans the full viewport width */
            left: 50%;
            transform: translateX(-50%);
            background-color: #ffffff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            padding: 20px 40px;
            margin-bottom: 2rem;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .top-bar h2 {
            font-weight: 700;
            margin: 0;
        }

        .table-nav {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .table-nav a {
            padding: 6px 12px;
            border-radius: 6px;
            background-color: #f1f1f1;
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: background-color 0.2s ease;
        }

        .table-nav a:hover {
            background-color: #ddd;
        }

        html {
            scroll-behavior: smooth;
        }

        .icon {
            font-size: 1.5rem;
            margin-right: 10px;
        }

        /* Custom table styling */
        .custom-table th, .custom-table td {
            vertical-align: middle; /* Vertically center-aligned */
            text-align: center; /* Horizontally center-aligned */
        }
    </style>
</head>

<body>

@section('body')
@show
</body>
</html>
