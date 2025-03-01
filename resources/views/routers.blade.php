@extends('layouts.app')

@section('title', 'Routers')

@section('page-title', 'Routers Management')

@section('content')
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Routers</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addRouterModal">
            <i class="fas fa-plus-circle me-1"></i> Add Router
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>IP Address</th>
                        <th>Port</th>
                        <th>Username</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($routers as $index => $router)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $router->name }}</td>
                        <td>{{ $router->ip_address }}</td>
                        <td>{{ $router->port }}</td>
                        <td>{{ $router->username }}</td>
                        <td>{{ $router->desc ?? 'N/A' }}</td>
                        <td>
                            <button class="btn btn-sm btn-info edit-router-btn"
                                    data-id="{{ $router->id }}"
                                    data-name="{{ $router->name }}"
                                    data-ip="{{ $router->ip_address }}"
                                    data-port="{{ $router->port }}"
                                    data-username="{{ $router->username }}"
                                    data-desc="{{ $router->desc }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editRouterModal">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-router-btn"
                                    data-id="{{ $router->id }}"
                                    data-name="{{ $router->name }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deleteRouterModal">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No routers found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $routers->links() }}
    </div>
</div>

<!-- Add Router Modal -->
<div class="modal fade" id="addRouterModal" tabindex="-1" aria-labelledby="addRouterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('routers.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addRouterModalLabel">Add New Router</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Router Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="ip_address" class="form-label">IP Address</label>
                        <input type="text" class="form-control" id="ip_address" name="ip_address" required>
                    </div>
                    <div class="mb-3">
                        <label for="port" class="form-label">Port</label>
                        <input type="number" class="form-control" id="port" name="port" value="8728" required>
                    </div>
                    <div class="mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="desc" class="form-label">Description</label>
                        <textarea class="form-control" id="desc" name="desc" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Router</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Router Modal -->
<div class="modal fade" id="editRouterModal" tabindex="-1" aria-labelledby="editRouterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editRouterForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editRouterModalLabel">Edit Router</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Router Name</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_ip_address" class="form-label">IP Address</label>
                        <input type="text" class="form-control" id="edit_ip_address" name="ip_address" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_port" class="form-label">Port</label>
                        <input type="number" class="form-control" id="edit_port" name="port" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="edit_username" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_password" class="form-label">Password (leave blank to keep current)</label>
                        <input type="password" class="form-control" id="edit_password" name="password">
                    </div>
                    <div class="mb-3">
                        <label for="edit_desc" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_desc" name="desc" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Router</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Router Modal -->
<div class="modal fade" id="deleteRouterModal" tabindex="-1" aria-labelledby="deleteRouterModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deleteRouterForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteRouterModalLabel">Delete Router</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete router <span id="delete_router_name" class="fw-bold"></span>?</p>
                    <p class="text-danger">This action cannot be undone!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Router</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    // Edit Router Modal
    $('.edit-router-btn').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const ip = $(this).data('ip');
        const port = $(this).data('port');
        const username = $(this).data('username');
        const desc = $(this).data('desc');

        $('#edit_name').val(name);
        $('#edit_ip_address').val(ip);
        $('#edit_port').val(port);
        $('#edit_username').val(username);
        $('#edit_desc').val(desc);
        $('#edit_password').val('');

        $('#editRouterForm').attr('action', '/routers/' + id);
    });

    // Delete Router Modal
    $('.delete-router-btn').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');

        $('#delete_router_name').text(name);
        $('#deleteRouterForm').attr('action', '/routers/' + id);
    });

    // Form validation and submission handling
    $(document).ready(function() {
        // Add validation for IP address format
        $('input[name="ip_address"], input[name="edit_ip_address"]').on('blur', function() {
            const ipRegex = /^(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/;
            const ip = $(this).val();

            if (!ipRegex.test(ip) && ip !== '') {
                $(this).addClass('is-invalid');
                if (!$(this).next('.invalid-feedback').length) {
                    $(this).after('<div class="invalid-feedback">Please enter a valid IP address.</div>');
                }
            } else {
                $(this).removeClass('is-invalid');
                $(this).next('.invalid-feedback').remove();
            }
        });

        // Success notification handling
        if (sessionStorage.getItem('router_action_success')) {
            const message = sessionStorage.getItem('router_action_success');

            // Display success message
            const alertHtml = `
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;

            $('.card-header').after(alertHtml);

            // Remove from session storage
            sessionStorage.removeItem('router_action_success');

            // Auto dismiss after 5 seconds
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
        }
    });
</script>
@endsection
