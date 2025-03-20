@extends('layouts.app')

@section('title', 'Plans')

@section('page-title', 'Plans Management')

@section('content')
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <h5 class="mb-0">All Plans</h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addPlanModal">
            <i class="fas fa-plus-circle me-1"></i> Add Plan
        </button>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover datatable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Download Speed</th>
                        <th>Upload Speed</th>
                        <th>Time Limit(days)</th>
                        <th>Price</th>
                        <th>Description</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($plans as $index => $plan)
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td>{{ $plan->name }}</td>
                        <td>{{ $plan->download_speed }} Mbps</td>
                        <td>{{ $plan->upload_speed }} Mbps</td>
                        <td>{{ $plan->time_limit }} days</td>
                        <td>{{ $plan->price }}</td>
                        <td>{{ $plan->desc ?? 'N/A' }}</td>
                        <td>
                            <button class="btn btn-sm btn-info edit-plan-btn"
                                    data-id="{{ $plan->id }}"
                                    data-name="{{ $plan->name }}"
                                    data-download="{{ $plan->download_speed }}"
                                    data-upload="{{ $plan->upload_speed }}"
                                    data-time="{{ $plan->time_limit }}"
                                    data-price="{{ $plan->price }}"
                                    data-desc="{{ $plan->desc }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editPlanModal">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-sm btn-danger delete-plan-btn"
                                    data-id="{{ $plan->id }}"
                                    data-name="{{ $plan->name }}"
                                    data-bs-toggle="modal"
                                    data-bs-target="#deletePlanModal">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No plans found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $plans->links() }}
    </div>
</div>

<!-- Add Plan Modal -->
<div class="modal fade" id="addPlanModal" tabindex="-1" aria-labelledby="addPlanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('plans.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addPlanModalLabel">Add New Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="name" class="form-label">Plan Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="download_speed" class="form-label">Download Speed (Mbps)</label>
                        <input type="number" class="form-control" id="download_speed" name="download_speed" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="upload_speed" class="form-label">Upload Speed (Mbps)</label>
                        <input type="number" class="form-control" id="upload_speed" name="upload_speed" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="time_limit" class="form-label">Time Limit (days)</label>
                        <input type="number" class="form-control" id="time_limit" name="time_limit" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" step="any" class="form-control" id="price" name="price" min="0.00001" required>
                    </div>
                    <div class="mb-3">
                        <label for="desc" class="form-label">Description</label>
                        <textarea class="form-control" id="desc" name="desc" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Plan Modal -->
<div class="modal fade" id="editPlanModal" tabindex="-1" aria-labelledby="editPlanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editPlanForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editPlanModalLabel">Edit Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Plan Name</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_download_speed" class="form-label">Download Speed (Mbps)</label>
                        <input type="number" class="form-control" id="edit_download_speed" name="download_speed" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_upload_speed" class="form-label">Upload Speed (Mbps)</label>
                        <input type="number" class="form-control" id="edit_upload_speed" name="upload_speed" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_time_limit" class="form-label">Time Limit (days)</label>
                        <input type="number" class="form-control" id="edit_time_limit" name="time_limit" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_price" class="form-label">Price</label>
                        <input type="number" step="any" class="form-control" id="edit_price" name="price" min="0.00001" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_desc" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_desc" name="desc" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Plan Modal -->
<div class="modal fade" id="deletePlanModal" tabindex="-1" aria-labelledby="deletePlanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="deletePlanForm" method="POST">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title" id="deletePlanModalLabel">Delete Plan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete plan <span id="delete_plan_name" class="fw-bold"></span>?</p>
                    <p class="text-danger">This action cannot be undone!</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-danger">Delete Plan</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Edit Plan Modal
        $('.edit-plan-btn').on('click', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');
            const download = $(this).data('download');
            const upload = $(this).data('upload');
            const time = $(this).data('time');
            const price = $(this).data('price');
            const desc = $(this).data('desc');

            $('#edit_name').val(name);
            $('#edit_download_speed').val(download);
            $('#edit_upload_speed').val(upload);
            $('#edit_time_limit').val(time);
            $('#edit_price').val(price);
            $('#edit_desc').val(desc);

            $('#editPlanForm').attr('action', '/plans/' + id);
        });

        // Delete Plan Modal
        $('.delete-plan-btn').on('click', function() {
            const id = $(this).data('id');
            const name = $(this).data('name');

            $('#delete_plan_name').text(name);
            $('#deletePlanForm').attr('action', '/plans/' + id);
        });

        // Form validation and error handling
        $('form').on('submit', function(e) {
            const form = $(this);

            // Basic client-side validation
            const requiredInputs = form.find('input[required]');
            let hasError = false;

            requiredInputs.each(function() {
                if ($(this).val().trim() === '') {
                    $(this).addClass('is-invalid');
                    if (!$(this).next('.invalid-feedback').length) {
                        $(this).after('<div class="invalid-feedback">This field is required.</div>');
                    }
                    hasError = true;
                } else {
                    $(this).removeClass('is-invalid');
                    $(this).next('.invalid-feedback').remove();
                }
            });

            if (hasError) {
                e.preventDefault();
            }
        });
    });
</script>
@endsection
