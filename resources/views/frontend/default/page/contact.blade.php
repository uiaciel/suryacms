@extends('frontend::app')
@section('content')
<div class="container-xxl contact py-5">
    <div class="container">
        <div class="section-title text-center mx-auto wow fadeInUp" data-wow-delay="0.1s"
            style="max-width: 500px; visibility: visible; animation-delay: 0.1s; animation-name: fadeInUp;">

            <h1 class="display-6">{{ text('Hubungi Kami Sekarang', 'Contact Us Right Now') }}</h1>

        </div>
        <div class="row justify-content-center wow fadeInUp" data-wow-delay="0.1s"
            style="visibility: visible; animation-delay: 0.1s; animation-name: fadeInUp;">
            <div class="col-lg-8">
                <p class="text-center mb-5">
                    {{ text('Kami senang mendengar dari Anda dan siap memberikan informasi lebih lanjut tentang layanan dan produk kami di bidang pertambangan batu bara. Silakan hubungi kami melalui informasi kontak di bawah ini atau gunakan formulir online untuk mengirim pesan kepada tim kami.',
        'We are delighted to hear from you and ready to provide more information about our services and products in the coal mining sector. Please contact us using the contact information below or use the online form to send a message to our team.')}}

                </p>

                @if (count($errors) > 0)
                <div class="alert alert-danger text-center">
                    <ul>
                        @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif

                @if (session('message'))
                <div class="alert alert-info text-center">
                    <h4 class="fw-bold">{{ text('Pesan Diterima', 'Message Received') }}</h4>

                    <p>
                        {{ session('message') }}
                    </p>
                </div>
                @endif

                @if (session('status'))
                <div class="alert alert-info text-center">
                    {{ session('status') }}
                </div>
                @endif

                <div class="row">

                    <form action="{{ route('sendcontact') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Name</label>
                            <input type="text" class="form-control" name="sender" id="sender" aria-describedby="nameId"
                                placeholder="Your Name">

                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Email</label>
                            <input type="email" class="form-control" name="email" id="email" aria-describedby="helpId"
                                placeholder="email@company.com">
                        </div>
                        <div class="mb-3">
                            <label for="" class="form-label fw-bold">Subject </label>
                            <input type="text" class="form-control" name="subject" id="" aria-describedby="helpId"
                                placeholder="Subject">
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label fw-bold">Message</label>
                            <textarea class="form-control" name="message" id="message" rows="8"></textarea>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">Send</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
