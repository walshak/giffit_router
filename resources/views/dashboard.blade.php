@extends('layouts.app')

@section('title', 'Dashboard')

@section('page-title', 'Dashboard')

@section('content')
<div class="row">
    <!-- Routers Card -->
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Routers</h5>
                        <h2>{{ $routerCount }}</h2>
                    </div>
                    <i class="fas fa-network-wired fa-3x"></i>
                </div>
                <a href="{{ route('routers') }}" class="text-white">View Details <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>

    <!-- Plans Card -->
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Plans</h5>
                        <h2>{{ $planCount }}</h2>
                    </div>
                    <i class="fas fa-project-diagram fa-3x"></i>
                </div>
                <a href="{{ route('plans') }}" class="text-white">View Details <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>

    <!-- Users Card -->
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Users</h5>
                        <h2>{{ $userCount }}</h2>
                    </div>
                    <i class="fas fa-users fa-3x"></i>
                </div>
                <a href="{{ route('users') }}" class="text-white">View Details <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>

    <!-- Active Subscriptions Card -->
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="card-title">Active Subscriptions</h5>
                        <h2>{{ $activeSubscriptions }}</h2>
                    </div>
                    <i class="fas fa-user-check fa-3x"></i>
                </div>
                <a href="{{ route('users') }}" class="text-white">View Details <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light text-dark">
                <h5 class="mb-0">Network Overview</h5>
            </div>
            <div class="card-body text-dark">
                <p>Welcome to the Network Management System. This dashboard provides you with a quick overview of your network resources.</p>
                <p>Use the sidebar to navigate to different sections of the application:</p>
                <ul>
                    <li><strong>Routers:</strong> Manage your network routers</li>
                    <li><strong>Plans:</strong> Create and manage subscription plans</li>
                    <li><strong>Users:</strong> Manage users and their subscriptions</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('routers') }}" class="btn btn-outline-primary">
                        <i class="fas fa-plus-circle me-2"></i> Add New Router
                    </a>
                    <a href="{{ route('plans') }}" class="btn btn-outline-success">
                        <i class="fas fa-plus-circle me-2"></i> Create New Plan
                    </a>
                    <a href="{{ route('users') }}" class="btn btn-outline-info">
                        <i class="fas fa-plus-circle me-2"></i> Add New User
                    </a>
                    <a href="{{ route('users') }}" class="btn btn-outline-warning">
                        <i class="fas fa-user-plus me-2"></i> Subscribe User to Plan
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
