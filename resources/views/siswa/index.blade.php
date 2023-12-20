@extends('base-layout')
@section('title-page')
    @include('utils.title-page', ['judul' => 'Daftar Siswa'])
@endsection
@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data Siswa</h3>
                </div>
                <div class="card-body">
                    <button class="btn btn-primary mb-3" data-toggle="modal" data-target="#addSiswaModal">Tambah Siswa</button>
                    <table class="table table-bordered" id="students-table">
                        <thead>
                            <tr>
                                <th>Nama Siswa</th>
                                <th>Kelas</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                           
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Siswa Modal -->
<div class="modal fade" id="addSiswaModal" tabindex="-1" role="dialog" aria-labelledby="addSiswaModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSiswaModalLabel">Tambah Siswa</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{route('student.store')}}" method="POST" id="addStudentForm">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="addSiswaName">Nama Siswa</label>
                        <input type="text" class="form-control" id="addSiswaName" placeholder="masukan nama siswa" name="nama_siswa" required>
                    </div>
                    <div class="form-group">
                            <label for="class">Kelas</label>
                            <select class="form-control" id="class" name="class" required>
                                <option value="">--Pilih Kelas--</option>
                                <option value="9">Kelas 9</option>
                                <option value="10">Kelas 10</option>
                                <option value="11">Kelas 11</option>
                                <option value="12">Kelas 12</option>
                            </select>
                        </div>
                        <!-- <div class="form-group">
                            <label for="status">Status</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="status" class="custom-control-input" id="customSwitch1" checked>
                                <label class="custom-control-label" for="customSwitch1">Aktif</label>
                            </div>
                            <input type="checkbox" name="status" class="custom-control-input" id="status" data-toggle="switch" data-on-text="Aktif" data-off-text="Non-Aktif" data-on-color="success" data-off-color="danger" checked>
                        </div> -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script type="module">
    $(document).ready(function () {
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
        // Inisialisasi DataTable
        let table = $('#students-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('student.index') }}",
            columns: [
                { data: 'name', name: 'name' },
                { data: 'class', name: 'class' },
                { 
                    data: 'status', 
                    name: 'status',
                    render: function (data, type, row) {
                        let statusText = data ? 'Aktif' : 'Non-Aktif';
                        let statusClass = data ? 'success' : 'danger';
                        return `
                            <div class="custom-control custom-switch">
                                <input type="checkbox" class="custom-control-input" id="customSwitch${row.id}" data-student-id="${row.id}" ${row} ${data ? 'checked' : ''}>
                                <label class="custom-control-label text-${statusClass}" for="customSwitch${row.id}">${statusText}</label>
                            </div>
                        `;
                    }
                },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        $(document).on('click', '.delete-student-btn', function () {
            let studentId = $(this).data('student-id');
            confirmDelete(studentId);
        });

        function confirmDelete(studentId) {
            Swal.fire({
                title: 'Konfirmasi Hapus',
                text: 'Anda yakin ingin menghapus data ini?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        type: "DELETE",
                        url: "/student/" + studentId,
                        data: {
                            _token: '{{ csrf_token() }}',
                            _method: 'DELETE',
                        },
                        success: function (response) {
                            table.ajax.reload();

                            Toast.fire({
                                icon: 'success',
                                title: response.success
                            });
                        },
                        error: function (error) {
                            console.log('Error:', error);
                        }
                    });
                }
            });
        }

        $('#addStudentForm').submit(function (e) {
            e.preventDefault();

            $.ajax({
                type: "POST",
                url: "{{ route('student.store') }}",
                data: $(this).serialize(),
                success: function (response) {
                    
                    $('#addSiswaModal').modal('hide');

                    Toast.fire({
                        icon: 'success',
                        title: response.success
                    });
                    table.ajax.reload();
                },
                error: function (error) {
                    console.log('Error:', error);
                }
            });
        });

        $('#students-table').on('change', '.custom-control-input', function() {
            let studentId = $(this).data('student-id');
            let status = $(this).prop('checked') ? 1 : 0;
    
            $(this).attr('disabled', true);
            console.log(studentId);
            $.ajax({
                type: "PUT",
                url: `/student/${studentId}/update-status`,
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'PUT',
                    status: status
                },
                success: function (response) {
                    Toast.fire({
                        icon: 'success',
                        title: response.success
                    });
                    table.ajax.reload();
                },
                error: function (error) {
                    console.log('Error:', error);
                },
                complete: function() {
                    setTimeout(function() {
                        $('#customSwitch'+studentId).removeAttr('disabled');
                    }, 1000);
                }
            });
        });
    });




</script>
@endpush