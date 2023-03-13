<div class="container">
    <header class="d-flex justify-content-center py-3 border-bottom">
        <ul class="nav nav-pills">
            <li class="nav-item"><a href="{{ route('books.index') }}" class="nav-link {{ request()->is('books') ? 'active' : '' }}">Books</a></li>
            <li class="nav-item"><a href="{{ route('authors.index') }}" class="nav-link {{ request()->is('authors') ? 'active' : '' }}">Authors</a></li>
        </ul>
    </header>
</div>
<div class="diveder"></div>
