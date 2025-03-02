@extends('layouts.app')
@section('content')
    <div class="card">
        <div class="card-header bg-light d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-dark">User Plans</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover datatable">


                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Plan</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Payment Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($userPlans as $userPlan)
                            <tr>
                                <td>{{ $userPlan->id }}</td>
                                <td>{{ $userPlan->user->name }}</td>
                                <td>{{ $userPlan->plan->name }}</td>
                                <td>{{ $userPlan->start_date }}</td>
                                <td>{{ $userPlan->end_date }}</td>
                                <td>{{ $userPlan->status }}</td>
                                <td>{{ $userPlan->payment_status }}</td>
                                <td>
                                    {{-- <button class="btn btn-warning btn-sm editUserPlan" data-id="{{ $userPlan->id }}">Edit</button> --}}
                                    <form method="POST" action="{{ route('userplans.destroy', $userPlan->id) }}"
                                        style="display:inline;">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">No user plans found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {{ $userPlans->links() }}
            </div>
        </div>
    </div>
@endsection
