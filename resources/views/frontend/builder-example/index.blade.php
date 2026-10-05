@extends('frontend::app')
@section('content')
<section class="hero">
    <p class="eyebrow">BUILDER EXAMPLE</p>
    <h1>Homepage yang siap diedit</h1>
    <p class="lead">Edit HTML dan CSS halaman ini melalui Homepage Builder. Shell theme tetap berasal dari app.blade.php.</p>
    <a class="button" href="#features">Lihat fitur</a>
</section>
<section class="feature-grid" id="features">
    <article><h2>HTML langsung</h2><p>Isi builder disimpan di kolom html dan dirender sebagai HTML.</p></article>
    <article><h2>CSS terpisah</h2><p>CSS builder masuk ke halaman live tanpa mengubah asset theme.</p></article>
    <article><h2>Layout konsisten</h2><p>Homepage dan page memakai app.blade.php yang sama.</p></article>
</section>
@endsection
