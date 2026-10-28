@extends('frontend::app')
@section('content')

    @if (count($errors) > 0)
        <div>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if (session('message'))
        <div>
            <h4>{{ text('Pesan Diterima', 'Message Received') }}</h4>
            <p>{{ session('message') }}</p>
        </div>
    @endif

    @if (session('status'))
        <div>
            {{ session('status') }}
        </div>
    @endif

    <form action="{{ route('frontend.contact.send') }}" method="POST">
        @csrf

        <input type="text" name="website_url" style="display:none !important" tabindex="-1" autocomplete="off">

        <div>
            <label for="sender">Name</label>
            <input type="text" name="sender" id="sender" aria-describedby="nameId" placeholder="Your Name">
        </div>

        <div>
            <label for="email">Email</label>
            <input type="email" name="email" id="email" aria-describedby="helpId" placeholder="email@company.com">
        </div>

        <div>
            <label for="subject">Subject</label>
            <input type="text" name="subject" id="subject" aria-describedby="helpId" placeholder="Subject">
        </div>

        <div>
            <label for="message">Message</label>
            <textarea name="message" id="message" rows="8"></textarea>
        </div>

        <div>
            <button type="submit">Send</button>
        </div>
    </form>

@endsection
