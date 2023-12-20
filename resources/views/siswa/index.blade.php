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
                        <div class="form-group">
                            <label for="status">Status</label>
                            <div class="custom-control custom-switch">
                                <input type="checkbox" name="status" class="custom-control-input" id="customSwitch1" checked>
                                <label class="custom-control-label" for="customSwitch1">Aktif</label>
                            </div>
                            <!-- <input type="checkbox" name="status" class="custom-control-input" id="status" data-toggle="switch" data-on-text="Aktif" data-off-text="Non-Aktif" data-on-color="success" data-off-color="danger" checked> -->
                        </div>
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
<script>
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
                // Submit form untuk menghapus data
                document.getElementById('deleteForm' + studentId).submit();
            }
        });
    }
</script>
<script type="module">
    $(document).ready(function () {
        // Inisialisasi DataTable
        let table = $('#students-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('student.index') }}",
            columns: [
                { data: 'name', name: 'name' },
                { data: 'class', name: 'class' },
                { data: 'status', name: 'status' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });

        $('#addStudentForm').submit(function (e) {
            e.preventDefault();

            $.ajax({
                type: "POST",
                url: "{{ route('student.store') }}",
                data: $(this).serialize(),
                success: function (response) {
                    
                    $('#addSiswaModal').modal('hide');

                    
                    table.ajax.reload();
                },
                error: function (error) {
                    console.log('Error:', error);
                }
            });
        });
    });


    $("#tableSiswa").DataTable();

</script>
@endpush