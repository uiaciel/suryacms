<div>

<div class="container">

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="mb-0 text-primary">Search Results for "{{ $query }}"</h3>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
      <li class="breadcrumb-item"><a href="/admin">Admin</a></li>
      <li class="breadcrumb-item"><a href="/">Search</a></li>
      <li class="breadcrumb-item active"><a href="#">Search Result</a></li>

        </ol>
    </nav>
</div>
@if(!empty($results))
@if(count($results['posts']) > 0)

<div class="card">
   <div class="card-body">
        <h5 class="card-title">Posts</h5>
              @foreach($results['posts'] as $post)
              <li><a href="#" class="text-blue-600 hover:underline">{{ $post->title }}</a></li>
              @endforeach

    </div>
</div>
 @endif
 @if(count($results['pages']) > 0)
<div class="card">
     <div class="card-body">
         <h5 class="card-title">Pages</h5>
         <ul>
             @foreach($results['pages'] as $page)
             <li><a href="#" class="text-blue-600 hover:underline">{{ $page->title }}</a></li>
             @endforeach
         </ul>

     </div>
</div>

@endif

@if(count($results['galleries']) > 0)

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Gallery</h5>
        <ul>
            @foreach($results['galleries'] as $gallery)
            <li><a href="#" class="text-blue-600 hover:underline">{{ $gallery->title }}</a></li>
            @endforeach
        </ul>

    </div>
</div>

@endif

 @if(count($results['inbox_contact_forms']) > 0)

 <div class="card">
     <div class="card-body">
         <h5 class="card-title">Inbox</h5>
         <ul>
             @foreach($results['inbox_contact_forms'] as $contact)
             <li><a href="#" class="text-blue-600 hover:underline">{{ $contact->name }}</a></li>
             @endforeach
         </ul>

     </div>
 </div>

 @endif

 @if(count($results['posts']) == 0 && count($results['pages']) == 0 && count($results['galleries']) == 0 && count($results['inbox_contact_forms']) == 0)
 <p class="mt-4">No results found for "{{ $query }}".</p>
 @endif
 @else
 <p class="mt-4">No search query provided.</p>
 @endif

</div>

</div>

