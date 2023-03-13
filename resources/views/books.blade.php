@extends('layouts.main')

@section('title-page', 'Books')

@section('content')
    <div class="container table-mg">
        <table class="table" id="data-table">
            <thead class="thead-dark">
            <tr>
                <th scope="col">Image</th>
                <th scope="col">Title</th>
                <th scope="col">Description</th>
                <th scope="col">Authors</th>
                <th scope="col">Published</th>
                <th scope="col">Action</th>
            </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>

    <div class="modal fade py-5" tabindex="-1" role="dialog" id="modalBook">
        <div class="modal-dialog" role="document">
            <div class="modal-content rounded-4 shadow">
                <div class="modal-header p-5 pb-4 border-bottom-0">
                    <h1 class="fw-bold mb-0 fs-2" id="modalHeader"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-5 pt-0">
                    <form id="bookForm" class="needs-validation" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="book_id" id="book_id">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control rounded-3" id="title" name="title" placeholder="Title" required>
                            <label for="title">Title</label>
                            <div class="invalid-feedback" id="error-title"></div>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="text" minlength="3" class="form-control rounded-3" id="description" name="description" placeholder="Description">
                            <label for="description">Description</label>
                            <div class="invalid-feedback" id="error-description"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="image">Image</label>
                            <input type="file" name="image" id="image" class="form-control form-control-file rounded-3">
                            <div class="invalid-feedback" id="error-image"></div>
                        </div>
                        <div class="form-group mb-3">
                            <label for="authors">Authors</label>
                            <select name="authors[]" id="authors-select" class="form-control" multiple required>
                            </select>
                            <div class="invalid-feedback" id="error-authors"></div>
                        </div>
                        <div class="form-floating mb-3">
                            <input type="date" class="form-control rounded-3" id="published" name="published" placeholder="Published">
                            <label for="published">Publication Date</label>
                            <div class="invalid-feedback" id="error-published"></div>
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
        function load_author_selection() {
            $.ajax({
                url: '{{route('authors.index')}}',
                type: 'GET',
                dataType: 'json',
                success: function(data) {
                    let options = '';
                    $.each(data.data, function(key, value) {
                        let name = value.name;
                        let surname = value.surname;
                        let patronymic = value.patronymic;
                        if (patronymic !== null) {
                            name += ' ' + patronymic;
                        }
                        options += '<option value="' + value.id + '">' + name + ' ' + surname + '</option>';
                    });
                    $('#authors-select').html(options);
                    $('#authors-select').select2({
                        width: '100%'
                    });
                }
            });
        }
        function errorShow(data) {
            if(data){
                if(data.hasOwnProperty('title')) {
                    $('#error-title').text(data.title[0]).show();
                    $('#title').addClass('error');
                }
                if(data.hasOwnProperty('authors')){
                    $('#error-authors').text(data.authors[0]).show();
                    $('#authors-select').addClass('error');
                }
                if(data.hasOwnProperty('image')){
                    $('#error-image').text(data.image[0]).show();
                    $('#image').addClass('error');
                }
            }else{
                $('#error-title').text('').hide();
                $('#error-authors').text('').hide();
                $('#title').removeClass('error');
                $('#authors-select').removeClass('error');
                $('#image').removeClass('error');
                $('#error-image').text('').hide();
            }
        }
        window.onload = function() {
            function selectAuthorsOption(data){
                let selectedOption = [];
                data.forEach(function(author) {
                    selectedOption.push(author.id);
                });
                $('#authors-select').val(selectedOption).trigger('change');

            }

            var table = $('#data-table').DataTable({
                serverSide: true,
                processing: true,
                dom: '<"table-header-tools"l<"add-book-button">f><t><ip>',
                aLengthMenu: [
                    [15, -1],
                    [15, "All"]
                ],
                ajax: "{{route('books.index')}}",
                columns: [
                    {data: 'image', name: 'image'},
                    {data: 'title', name: 'title'},
                    {data: 'description', name: 'description'},
                    {data: 'authors', name:'authors'},
                    {data: 'publication_date', name:'publication_date'},
                    {data: 'action', name: 'action', orderable: false},
                ]
            });
            $('.add-book-button').html('<a href="javascript:void(0)" class="btn btn-block btn-success" id="createBook">Add</a>');

            $('#createBook').on('click', function () {
                errorShow(null);
                load_author_selection();
                $('#bookForm').trigger('reset');
                $('#book_id').val('');
                $('#modalHeader').text("Book Add");
                $('#buttonSubmit').text("Add");
                $('#modalBook').modal('show');
            });

            $('#buttonSubmit').on('click', function(event) {
                errorShow(null);
                event.preventDefault();
                var data = new FormData($('#bookForm')[0]);
                $.ajax({
                    data: data,
                    url: "{{route('books.store')}}",
                    type: "POST",
                    processData: false,
                    contentType: false,
                    success: function (data) {
                        $('#bookForm').trigger('reset');
                        $('#modalBook').modal('hide');
                        table.draw();
                    },
                    error: function (err) {
                        let errorData = err.responseJSON.errors;
                        errorShow(errorData);
                    }
                });
            });

            $('body').on('click', '#deleteBook', function(){
                let book_id = $(this).data('id');
                confirm("Are you sure to delete ?");
                $.ajax({
                    url: "{{route('books.index')}}/" + book_id,
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

            $('body').on('click', '#editBook', function(){
                errorShow(null);
                load_author_selection();
                let book_id = $(this).data('id');
                $.get("{{route('books.index')}}/" + book_id + '/edit', function(data) {
                    $('#modalHeader').text("Edit Book");
                    $('#buttonSubmit').text("Save");
                    $('#modalBook').modal('show');
                    $('#book_id').val(data.id);
                    $('#title').val(data.title);
                    $('#description').val(data.description);
                    selectAuthorsOption(data.authors);
                    $('#published').val(data.publication_date);
                })
            });
        }
    </script>
@endsection




