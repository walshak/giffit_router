<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to Naijalink Network Manager</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <style>
        :root {
            --primary-color: #0d6efd;
            --success-color: #198754;
            --info-color: #0dcaf0;
            --warning-color: #ffc107;
        }

        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        .hero-section {
            padding: 5rem 0;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        }

        .feature-card {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }

        .stats-card {
            border-radius: 0.5rem;
        }

        .cta-section {
            border-radius: 0.5rem;
        }

        .navbar {
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        /* Dark mode styles */
        @media (prefers-color-scheme: dark) {
            body.dark-mode {
                background-color: #000;
                color: rgba(255, 255, 255, 0.5);
            }

            body.dark-mode .hero-section {
                background: linear-gradient(135deg, #111 0%, #222 100%);
            }

            body.dark-mode .text-dark {
                color: #fff !important;
            }

            body.dark-mode .text-muted {
                color: #adb5bd !important;
            }

            body.dark-mode .card {
                background-color: #111 !important;
                border-color: #333 !important;
            }

            body.dark-mode .bg-light {
                background-color: #222 !important;
            }

            body.dark-mode .navbar {
                background-color: #111 !important;
            }

            body.dark-mode .btn-outline-primary {
                color: #4c9fff;
                border-color: #4c9fff;
            }

            body.dark-mode .btn-outline-primary:hover {
                background-color: rgba(13, 110, 253, 0.2);
                color: #4c9fff;
            }

            body.dark-mode .btn-outline-success {
                color: #3dd680;
                border-color: #3dd680;
            }

            body.dark-mode .btn-outline-success:hover {
                background-color: rgba(25, 135, 84, 0.2);
                color: #3dd680;
            }

            body.dark-mode .btn-outline-info {
                color: #54deff;
                border-color: #54deff;
            }

            body.dark-mode .btn-outline-info:hover {
                background-color: rgba(13, 202, 240, 0.2);
                color: #54deff;
            }

            body.dark-mode .btn-primary {
                background-color: #0d6efd;
                border-color: #0d6efd;
            }
        }
    </style>
</head>

<body class="dark:bg-black dark:text-white/50 dark-mode">
    <!-- Minimal Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Naijalink Network Manager</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('register') }}">Register</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section text-center">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold text-dark">Welcome to Naijalink Network Manager</h1>
                    <p class="lead text-muted mb-5">Your comprehensive solution for managing network infrastructure</p>
                    <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg px-5 py-3 mb-5">
                        <i class="fas fa-tachometer-alt me-2"></i> Go to Dashboard
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <div class="container py-5">
        <!-- Features Section -->
        <div class="row mb-5">
            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm feature-card">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-network-wired fa-3x text-primary"></i>
                        </div>
                        <h3 class="h4 card-title text-dark">Router Management</h3>
                        <p class="card-text">Configure and monitor all your network routers from a single dashboard.</p>
                        <a href="{{ route('routers') }}" class="btn btn-outline-primary mt-2">Manage Routers</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm feature-card">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-project-diagram fa-3x text-success"></i>
                        </div>
                        <h3 class="h4 card-title text-dark">Subscription Plans</h3>
                        <p class="card-text">Create, modify and manage service plans for your network users.</p>
                        <a href="{{ route('plans') }}" class="btn btn-outline-success mt-2">Manage Plans</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card h-100 border-0 shadow-sm feature-card">
                    <div class="card-body text-center p-4">
                        <div class="mb-3">
                            <i class="fas fa-users fa-3x text-info"></i>
                        </div>
                        <h3 class="h4 card-title text-dark">User Management</h3>
                        <p class="card-text">Efficiently manage user accounts and monitor active subscriptions.</p>
                        <a href="{{ route('users') }}" class="btn btn-outline-info mt-2">Manage Users</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="card border-0 shadow-sm mb-5 cta-section">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="text-dark">Ready to get started?</h2>
                        <p class="mb-md-0">Access your dashboard to view network statistics and perform quick actions.</p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <a href="{{ route('dashboard') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-tachometer-alt me-2"></i> Go to Dashboard
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Overview -->
        <div class="row mb-4">
            <div class="col-12">
                <h3 class="h4 mb-4 text-center text-dark">Network at a Glance</h3>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card text-center border-0 bg-light p-3 h-100 stats-card">
                    <div class="d-inline-block mb-2">
                        <i class="fas fa-network-wired fa-2x text-primary"></i>
                    </div>
                    <h4 class="h5 text-dark">{{ $routerCount ?? '0' }}</h4>
                    <p class="text-muted mb-0">Routers</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card text-center border-0 bg-light p-3 h-100 stats-card">
                    <div class="d-inline-block mb-2">
                        <i class="fas fa-project-diagram fa-2x text-success"></i>
                    </div>
                    <h4 class="h5 text-dark">{{ $planCount ?? '0' }}</h4>
                    <p class="text-muted mb-0">Plans</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card text-center border-0 bg-light p-3 h-100 stats-card">
                    <div class="d-inline-block mb-2">
                        <i class="fas fa-users fa-2x text-info"></i>
                    </div>
                    <h4 class="h5 text-dark">{{ $userCount ?? '0' }}</h4>
                    <p class="text-muted mb-0">Users</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card text-center border-0 bg-light p-3 h-100 stats-card">
                    <div class="d-inline-block mb-2">
                        <i class="fas fa-user-check fa-2x text-warning"></i>
                    </div>
                    <h4 class="h5 text-dark">{{ $activeSubscriptions ?? '0' }}</h4>
                    <p class="text-muted mb-0">Active Subscriptions</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="bg-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0">© 2025 Naijalink Network Manager. All rights reserved.</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <a href="#" class="text-decoration-none text-muted me-3">Privacy Policy</a>
                    <a href="#" class="text-decoration-none text-muted me-3">Terms of Service</a>
                    <a href="#" class="text-decoration-none text-muted">Contact</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        // Check for dark mode preference
        const isDarkMode = window.matchMedia('(prefers-color-scheme: dark)').matches;

        // Apply dark mode class to body if needed
        if (isDarkMode) {
            document.body.classList.add('dark-mode');
        }

        // Enable all tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });
    </script>
</body>
</html>
