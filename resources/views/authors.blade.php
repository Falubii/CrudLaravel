@extends('layouts.main')

@section('title-page', 'Authors')

@section('content')
    <div class="container table-mg">
        <table class="table" id="data-table">
            <thead class="thead-dark">
            <tr>
                <th scope="col">Name</th>
                <th scope="col">Surname</th>
                <th scope="col">Patronymic</th>
                <th scope="col">Action</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div class="modal fade py-5" tabindex="-1" role="dialog" id="modalAuthor">
        <div class="modal-dialog" role="document">
            <div class="modal-content rounded-4 shadow">
                <div class="modal-header p-5 pb-4 border-bottom-0">
                    <h1 class="fw-bold mb-0 fs-2" id="modalHeader"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5 pt-0">
                    <form id="authorForm" class="needs-validation">
                        @csrf
                        <input type="hidden" name="author_id" id="author_id">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control rounded-3" id="name" name="name" placeholder="Name" required>
                            <label for="name">Author Name</label>
                            <div class="invalid-feedback" id="error-name"></div>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" minlength="3" class="form-control rounded-3" id="surname" name="surname" placeholder="Surname" required>
                            <label for="surname">Author Surname</label>
                            <div class="invalid-feedback" id="error-surname"></div>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control rounded-3" id="patronymic" name="patronymic" placeholder="Patronymic">
                            <label for="patronymic">Author Patronymic</label>
                            <div class="invalid-feedback" id="error-patronymic"></div>
                        </div>
                        <button class="w-100 mb-2 btn btn-lg rounded-3 btn-primary" type="submit" id="buttonSubmit">Add</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


@endsection

@section('js-scripts')
    <script type="text/javascript">
        window.onload = function() {
            function errorShow(data) {
                if(data){
                    if(data.hasOwnProperty('name')){
                        $('#error-name').text(data.name[0]).show();
                        $('#name').addClass('error');
                    }
                    if(data.hasOwnProperty('surname')){
                        $('#error-surname').text(data.surname[0]).show();
                        $('#surname').addClass('error');
                    }
                }else{
                    $('#error-name').text('').hide();
                    $('#error-surname').text('').hide();
                    $('#name').removeClass('error');
                    $('#surname').removeClass('error');
                }
            }
            var table = $('#data-table').DataTable({
                serverSide: true,
                processing: true,
                dom:'<"table-header-tools"l<"add-author-button">f><t><ip>',
                aLengthMenu: [
                    [15, -1],
                    [15, "All"]
                ],
                ajax:"{{route('authors.index')}}",
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'surname', name: 'surname'},
                    {data: 'patronymic', name: 'patronymic'},
                    {data: 'action', name:'action', orderable: false},
                ]
            });
            $('.add-author-button').html('<a href="javascript:void(0)" class="btn btn-block btn-success" id="createAuthor">Add</a>');

            $('#createAuthor').on('click', function(){
                errorShow(null);
                $('#authorForm').trigger('reset');
                $('#author_id').val('');
                $('#modalHeader').text("Author Add");
                $('#buttonSubmit').text("Add");
                $('#modalAuthor').modal('show');
            });

            $('#buttonSubmit').on('click', function(e){
                errorShow(null);
                e.preventDefault();
                $.ajax({
                    data: $('#authorForm').serialize(),
                    url: "{{route('authors.store')}}",
                    type: "POST",
                    dataType: 'json',
                    success: function(data){
                        $('#authorForm').trigger('reset');
                        $('#modalAuthor').modal('hide');
                        table.draw();
                    },
                    error: function(err) {
                        let errorData = err.responseJSON.errors;
                        errorShow(errorData);
                    }
                });
            });

            $('body').on('click', '#deleteAuthor', function(){
                let author_id = $(this).data('id');
                confirm("Are you sure to delete ?");
                $.ajax({
                    url: "{{route('authors.index')}}/" + author_id,
                    type: "DELETE",
                    dataType: 'json',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(data){
                        table.draw();
                    },
                    error: function(err) {
                        console.log('Error:', err);
                    }
                });
            });

            $('body').on('click', '#editAuthor', function(){
                errorShow(null);
                let author_id = $(this).data('id');
                $.get("{{route('authors.index')}}/" + author_id + '/edit', function(data) {
                    errorShow(null);
                    $('#modalHeader').text("Edit Author");
                    $('#buttonSubmit').text("Save");
                    $('#modalAuthor').modal('show');
                    $('#author_id').val(data.id);
                    $('#name').val(data.name);
                    $('#surname').val(data.surname);
                    $('#patronymic').val(data.patronymic);
                })
            });
        }

    </script>
@endsection




