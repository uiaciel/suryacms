<div x-data="{ search: @entangle('search') }" class="d-flex">

    <form class="d-flex" @submit.prevent="window.location.href = '{{ route('admin.search.results') }}?query=' + search" style="margin-bottom:0px;">

        <input type="text" class="form-control me-1" placeholder="Search..." x-model="search" required>
        <button type="submit" class="btn btn-dark me-3">
            <i class="bi bi-search"></i>
        </button>

    </form>

            <button id="darkModeToggle" class="btn btn-dark text-white "><i class="bi bi-moon"></i></button>

</div>
