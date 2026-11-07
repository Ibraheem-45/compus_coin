@extends('admin.master')

@section('content')

<div class="container-fluid py-4">

<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
    <div>
        <h1 class="h3 mb-1">Student Accounts</h1>
        <p class="text-muted mb-0">View and manage registered student accounts.</p>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="card border-0 shadow-sm">
    <div class="card-body p-4">

        <form method="GET" action="{{ route('admin.users') }}" class="row g-3 mb-4">
            <div class="col-12 col-md-9">
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Search by name, email or university"
                    value="{{ request('search') }}"
                >
            </div>

            <div class="col-12 col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    Search
                </button>
            </div>
        </form>

        @if($students->count())
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>University</th>
                            <th>Academic Year</th>
                            <th>Verified</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach($students as $student)
                            <tr>
                                <td class="fw-semibold">
                                    {{ $student->name }}
                                </td>

                                <td>
                                    {{ $student->email }}
                                </td>

                                <td>
                                    {{ $student->university ?: 'Not provided' }}
                                </td>

                                <td>
                                    {{ $student->academic_year ?: 'Not provided' }}
                                </td>

                                <td>
                                    @if($student->email_verified_at)
                                        <span class="badge bg-success-subtle text-success">
                                            Verified
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning">
                                            Not Verified
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if($student->status === 'active')
                                        <span class="badge bg-success-subtle text-success">
                                            Active
                                        </span>
                                    @else
                                        <span class="badge bg-danger-subtle text-danger">
                                            Disabled
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    {{ $student->created_at?->format('d M Y') }}
                                </td>

                                <td>
                                    <div class="d-flex flex-wrap gap-2">

                                        <a
                                            href="{{ route('admin.users.show', $student) }}"
                                            class="btn btn-sm btn-outline-primary"
                                        >
                                            View
                                        </a>

                                        @if($student->status === 'active')
                                            <form
                                                method="POST"
                                                action="{{ route('admin.users.disable', $student) }}"
                                            >
                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-outline-danger"
                                                >
                                                    Disable
                                                </button>
                                            </form>
                                        @else
                                            <form
                                                method="POST"
                                                action="{{ route('admin.users.enable', $student) }}"
                                            >
                                                @csrf
                                                @method('PATCH')

                                                <button
                                                    type="submit"
                                                    class="btn btn-sm btn-outline-success"
                                                >
                                                    Enable
                                                </button>
                                            </form>
                                        @endif

                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $students->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="bi bi-people fs-1 text-muted"></i>

                <h5 class="mt-3">No students found</h5>

                <p class="text-muted mb-0">
                    No student accounts match your search.
                </p>
            </div>
        @endif

    </div>
</div>

</div>
@endsection
