<section class="contact-section py-4">
    <div class="container">
        <h4>Liên hệ</h4>
        <form method="POST" action="{{ route('contact.send') }}" class="row g-3" novalidate>
            @csrf
            <div class="col-md-6">
                <label class="form-label">Họ và tên</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="col-12">
                <label class="form-label">Nội dung</label>
                <textarea name="message" rows="4" class="form-control" required></textarea>
            </div>
            <div class="col-12">
                <button class="btn btn-primary" type="submit">Gửi</button>
            </div>
        </form>
    </div>
</section>


