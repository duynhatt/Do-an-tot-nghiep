@include('client.layout.header')

<div class="container-fluid bg-light py-5">
    <div class="col-md-6 m-auto text-center">
        <h1 class="h1">Contact Us</h1>
        <p>
            Hãy để lại lời nhắn cho chúng tôi, chúng tôi sẽ phản hồi bạn sớm nhất có thể.
        </p>
    </div>
</div>

<div class="container py-5">
    <div class="row py-5">
        <form class="col-md-9 m-auto" action="{{ route('contact.store') }}" method="post" role="form">
            @csrf @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <div class="row">
                <div class="form-group col-md-6 mb-3">
                    <label for="inputname">Name</label>
                    <input type="text" class="form-control mt-1" id="name" placeholder="Name" value="{{ Auth::user()->name ?? '' }}" readonly>
                </div>
                <div class="form-group col-md-6 mb-3">
                    <label for="inputemail">Email</label>
                    <input type="email" class="form-control mt-1" id="email" placeholder="Email" value="{{ Auth::user()->email ?? '' }}" readonly>
                </div>
            </div>
            <div class="mb-3">
                <label for="inputsubject">Subject (Tiêu đề)</label>
                <input type="text" class="form-control mt-1" id="subject" name="tieu_de" placeholder="Nhập tiêu đề liên hệ" required>
            </div>
            <div class="mb-3">
                <label for="inputmessage">Message (Nội dung)</label>
                <textarea class="form-control mt-1" id="message" name="noi_dung" placeholder="Nhập nội dung tin nhắn" rows="8" required></textarea>
            </div>
            <div class="row">
                <div class="col text-end mt-2">
                    <button type="submit" class="btn btn-success btn-lg px-3">Let’s Talk</button>
                </div>
            </div>
        </form>
    </div>
</div>
@include('client.layout.scripts')
@include('client.layout.footer')