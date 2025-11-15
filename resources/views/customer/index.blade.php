@extends('customer.layout')
@section('content')
    <div class="fade modal" tabindex="-1" id="confirm-delete">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure want to delete?</p>
                </div>
                <div class="modal-footer">
                    <form id="frm_delete" action="{{ url('/delete') }}" method="post"
                        style="padding-bottom: 0px;margin-bottom: 0px;">
                        @method('DELETE')
                        @csrf
                        <input type="hidden" name="delete_id" id="delete_id" />
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" data-bs-dismiss="modal"
                            id="delete_btn">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="container">
        <div class="pagetitle">
            <a type="button" class="btn btn-primary" style="float: right" href="{{ url('/create') }}">
                <i class="bi bi-plus-circle"></i> Add New
            </a>
            <h2>Customers List</h2>
            <hr />
        </div>
        <form method="get" id="search_form">
            <div class="row pb-3">
                <div class="col-md-10">
                    <div class="row justify-content-start">
                        <div class="col-lg-3 col-sm-6">
                            <label class="form-label mb-0">Name</label>
                            <input type="text" class="form-control" id="search" value="{{ request('search') }}"
                                placeholder="Search..." />
                        </div>
                        <div class="col-lg-3 col-sm-6">
                            <label class="form-label mb-0">Gender</label>
                            <select class="form-select" id="gender">
                                <option value="0" {{ request('gender') == 0 ? 'selected' : '' }}>ALL</option>
                                <option value="2" {{ request('gender') == 2 ? 'selected' : '' }}>Male</option>
                                <option value="1" {{ request('gender') == 1 ? 'selected' : '' }}>Female</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="col-md-2 align-self-end">
                    <button type="submit" class="btn btn-secondary pt-1" style="float: right">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
            </div>
        </form>

        <table class="table table-striped">
            <thead class="table-dark">
                <tr>
                    <th width="60px" class="text-center">No</th>
                    <th width="150px" class="text-center">Photo</th>
                    <th style="cursor: pointer" onclick="loadData('name')">
                        Name
                        <i
                            class="text-secondary {{ request('field') == 'name'
                                ? (request('order') == 'desc'
                                    ? 'bi bi-sort-alpha-down-alt'
                                    : 'bi bi-sort-alpha-down')
                                : 'bi bi-arrow-down-up' }}"></i>
                    </th>
                    <th style="cursor: pointer" onclick="loadData('gender')">
                        Gender
                        <i
                            class="text-secondary {{ request('field') == 'gender'
                                ? (request('order') == 'desc'
                                    ? 'bi bi-sort-alpha-down-alt'
                                    : 'bi bi-sort-alpha-down')
                                : 'bi bi-arrow-down-up' }}"></i>
                    </th>
                    <th style="cursor: pointer" onclick="loadData('email')">
                        Email
                        <i
                            class="text-secondary {{ request('field') == 'email'
                                ? (request('order') == 'desc'
                                    ? 'bi bi-sort-alpha-down-alt'
                                    : 'bi bi-sort-alpha-down')
                                : 'bi bi-arrow-down-up' }}"></i>
                    </th>
                    <th style="cursor: pointer" onclick="loadData('created_at')">
                        Created at
                        <i
                            class="text-secondary {{ request('field') == 'created_at'
                                ? (request('order') == 'desc'
                                    ? 'bi bi-sort-alpha-down-alt'
                                    : 'bi bi-sort-alpha-down')
                                : 'bi bi-arrow-down-up' }}"></i>
                    </th>
                    <th width="150px" style="vertical-align: middle">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($customers as $index => $value)
                    <tr>
                        <th style="vertical-align: middle;text-align: center">
                            {{ $customers->perPage() * ($customers->currentPage() - 1) + ($index + 1) }}
                        </th>
                        <td class="text-center py-2"><img style="width: 60px;height: 60px;"
                                src="{{ url($value->image ? './storage/' . $value->image : './default.png') }}" />
                        </td>
                        <td style="vertical-align: middle">{{ $value->name }}</td>
                        <td style="vertical-align: middle">{{ $value->gender == 2 ? 'Male' : 'Female' }}</td>
                        <td style="vertical-align: middle">{{ $value->email }}</td>
                        <td style="vertical-align: middle">{{ date('d/m/Y H:i:s', strtotime($value->created_at)) }}</td>
                        <td style="vertical-align: middle;text-align: center;">
                            <i class="bi bi-trash3-fill text-danger" role="button" data-record-id="{{ $value->id }}"
                                title="Delete" data-bs-toggle="modal" data-bs-target="#confirm-delete"></i>
                            <a title="Edit" href="{{ url('/edit/' . $value->id) }}">
                                <i class="bi bi-pencil-square text-success px-3" role="button"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <nav>
            <ul class="pagination d-flex justify-content-end">
                {{ $customers->links() }}
            </ul>
        </nav>
    </div>
@endsection
@section('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const deleteModal = document.getElementById('confirm-delete');
            if (deleteModal) {
                deleteModal.addEventListener('show.bs.modal', event => {
                    const triggerElement = event.relatedTarget;
                    const recordId = triggerElement.getAttribute('data-record-id');
                    document.querySelector('input#delete_id').value = recordId;
                });
            }
        });

        document.getElementById('search_form').addEventListener('submit', function(event) {
            event.preventDefault();
            loadData();
        });

        function loadData(sortBy = null) {
            const urlParams = new URLSearchParams(window.location.search);
            let field = urlParams.get('field') || 'created_at';
            let order = urlParams.get('order') || 'asc';

            // Apply sorting logic
            if (sortBy) {
                if (field === sortBy) {
                    order = (order === 'asc') ? 'desc' : 'asc';
                } else {
                    field = sortBy;
                    order = 'asc';
                }
            }

            // Build and submit form
            const form = document.createElement('form');
            form.method = 'GET';
            form.action = '/';

            const params = {
                search: document.getElementById('search')?.value || '',
                gender: document.getElementById('gender')?.value || '',
                field: field,
                order: order
            };

            for (const key in params) {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = params[key];
                form.appendChild(input);
            }

            document.body.appendChild(form);
            form.submit();
        }
    </script>
@endsection