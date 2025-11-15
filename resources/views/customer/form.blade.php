@extends('customer.layout')
@section('css')
<style>
    .required label:after {
        content: " *";
        color: red;
        font-weight: bold;
    }

    .form-label {
        margin-bottom: 0px !important;
    }
</style>
@endsection
@section('content')
<div class="fade modal" tabindex="-1" id="alert_modal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Alert</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Ok</button>
                </form>
            </div>
        </div>
    </div>
</div>
<div class="container">
    <div class="col-md-8 offset-md-2">
        <h1>{{ isset($customer) ? 'Edit' : 'New' }} Customer</h1>
        <hr />
        <form method="POST" enctype="multipart/form-data" action="{{ url('/submit') }}">
            @csrf
            @method(isset($customer) ? 'PUT' : 'POST')
            <input type="hidden" value="{{ isset($customer) ? $customer->id : 0 }}" name="id" />
            <div class="row">
                <div class="col-md-6">
                    <div class="required  mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input id="name" name="name" type="text" value="{{ old('name', isset($customer) ? $customer->name : '') }}"
                            class="form-control @error('name') is-invalid @enderror" />
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="required  mb-3">
                        <label for="gender" class="form-label">Gender</label>
                        <select id="gender" name="gender" class="form-select @error('gender') is-invalid @enderror">
                            <option value="2" @if (old('gender', isset($customer) ? $customer->gender : '') == 2) selected @endif>Male</option>
                            <option value="1" @if (old('gender', isset($customer) ? $customer->gender : '') == 1) selected @endif>Female</option>
                        </select>
                        @error('gender')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="required  mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input id="email" name="email" type="text"
                            value="{{ old('email', isset($customer) ? $customer->email : '') }}"
                            class="form-control @error('email') is-invalid @enderror">
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3 text-center">
                        <label class="form-label">Photo</label>
                        <div>
                            <input type="hidden" style="display: none" value="0" name="is_deleted_image"
                                id="is_deleted_image">
                            <img id="img_preview" style="width: 180px;height: 180px;cursor: pointer;"
                                class="img img-thumbnail" onerror="this.onerror=null;this.src='./default.png'"
                                src="{{ isset($customer) && $customer->image ? url('./storage/' . $customer->image) : url('./default.png') }}"
                                onclick="changeProfile()" />
                            <p>
                                <a href="javascript:changeProfile()" style="text-decoration: none;">
                                    Change</a>
                                <a href="javascript:removeProfile()"
                                    style="color: red;text-decoration: none;">Remove</a>
                            </p>
                            <input type="file" id="image" name="image" style="display: none;"
                                accept=".jpg,.jpeg,.bmp,.gif,.png,.webp" />
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4 text-center">
                <a href="{{ url('/') }}" class="btn btn-danger mx-2">
                    <i class="bi bi-x-lg"></i> Cancel
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-floppy" style="padding-right: 3px;"></i> Save
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
@section('js')
<script>
    function changeProfile() {
        document.getElementById('image').click();
    }

    function validateFile(inputElement, maxFileSize = 1, validFileExtension = [".jpg", ".jpeg", ".bmp", ".gif", ".png",
        ".webp"
    ]) {
        let image = null;
        const files = inputElement.files;

        if (files && files.length > 0) {
            const alertModal = document.getElementById("alert_modal");
            const modal = new bootstrap.Modal(alertModal);
            const fileSizeMB = ((files[0].size / 1024) / 1024).toFixed(4);
            const fileName = files[0].name;

            if (fileSizeMB <= maxFileSize) {
                let isValidExtension = false;
                let fileExtension = '';
                for (let ext of
                        validFileExtension) {
                    if (fileName.toLowerCase().endsWith(ext.toLowerCase())) {
                        isValidExtension = true;
                        fileExtension = ext;
                        break;
                    }
                }
                const baseName = fileName.substring(0, fileName.length -
                    fileExtension.length).trim();
                const cleanedBaseName = baseName.replace(/[^a-zA-Z0-9-_\s]/g, '');
                if (cleanedBaseName !== baseName) {
                    alertModal.querySelector('.modal-body p').textContent =
                        "Invalid filename. Filename only allows alphanumeric characters.";
                    modal.show();
                    inputElement.value = '';
                } else if (isValidExtension) {
                    image = files[0];
                } else {
                    alertModal.querySelector('.modal-body p').textContent = fileName +
                        " is invalid. Allowed extensions are: " + validFileExtension.join(", ");
                    modal.show();
                    inputElement.value = '';
                }

            } else {
                const maxSizeMsg = (maxFileSize < 1) ?
                    "Maximum file size is " + (maxFileSize * 1000).toString() + " KB." :
                    "Maximum file size is " +
                    maxFileSize.toString() + "MB.";
                alertModal.querySelector('.modal-body p').textContent = maxSizeMsg;
                modal.show();
                inputElement.value = '';
            }
        }
        return image;
    }

    function removeProfile() {
        document.getElementById('img_preview').setAttribute('src', '{{ url("./default.png") }}');
        document.getElementById('is_deleted_image').value = 1;
    }

    document.getElementById('image').addEventListener('change', function() {
        if (this.value !== '') {
            const
                selectedFile = validateFile(this, 1, [".jpg", ".jpeg", ".bmp", ".gif", ".png",
                    ".webp"
                ]);
            if (selectedFile) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('img_preview').setAttribute('src', e.target.result);
                }
                reader.readAsDataURL(selectedFile);
            }
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            document.getElementById('name').focus();
        }, 10); // Small delay to ensure element is rendered
    });
</script>
@endsection