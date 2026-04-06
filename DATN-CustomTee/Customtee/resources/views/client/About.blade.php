@include('client.layout.header')

<section class="about-hero py-5">
    <div class="container py-4">
        <div class="row align-items-center g-4">
            <div class="col-lg-7 text-white">
                <span class="hero-tag">FashionTee</span>
                <h1 class="display-5 fw-bold mt-3 mb-3">Thời trang mỗi ngày, phong cách theo cách của bạn</h1>
                <p class="lead mb-4">
                    FashionTee là website bán quần áo thời trang tập trung vào áo thun, outfit casual và street style hiện đại.
                    Chúng tôi mang đến sản phẩm dễ mặc, dễ phối, chất liệu tốt và mức giá hợp lý cho mọi ngày trong tuần.
                </p>
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ url('Shop') }}" class="btn btn-light btn-lg px-4">Mua sắm ngay</a>
                    <a href="{{ route('home') }}" class="btn btn-outline-light btn-lg px-4">Về trang chủ</a>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="hero-card shadow">
                    <img src="{{ asset('img/banner_img_01.jpg') }}" alt="FashionTee Hero" class="img-fluid rounded-3">
                </div>
            </div>
        </div>
    </div>
</section>

<section class="container py-5">
    <div class="row mb-4">
        <div class="col-lg-8 mx-auto text-center">
            <h2 class="fw-bold mb-3">Về FashionTee</h2>
            <p class="text-muted mb-0">
                Chúng tôi xây dựng FashionTee với mục tiêu giúp bạn mua đồ online nhanh, rõ ràng và đúng gu:
                hình ảnh thật, thông tin size minh bạch, giao hàng nhanh và hỗ trợ đổi trả linh hoạt.
            </p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-md-6 col-lg-3">
            <div class="feature-card h-100 p-4 text-center">
                <div class="feature-icon mb-3"><i class="fa fa-tshirt"></i></div>
                <h5 class="fw-semibold">Thiết kế bắt trend</h5>
                <p class="text-muted mb-0">Cập nhật mẫu mới liên tục theo phong cách trẻ trung, năng động.</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="feature-card h-100 p-4 text-center">
                <div class="feature-icon mb-3"><i class="fa fa-check-circle"></i></div>
                <h5 class="fw-semibold">Chất lượng ổn định</h5>
                <p class="text-muted mb-0">Ưu tiên form dáng đẹp, chất vải thoải mái và đường may chắc chắn.</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="feature-card h-100 p-4 text-center">
                <div class="feature-icon mb-3"><i class="fa fa-shipping-fast"></i></div>
                <h5 class="fw-semibold">Giao hàng nhanh</h5>
                <p class="text-muted mb-0">Xử lý đơn sớm, cập nhật trạng thái rõ ràng và hỗ trợ tận tâm.</p>
            </div>
        </div>
        <div class="col-md-6 col-lg-3">
            <div class="feature-card h-100 p-4 text-center">
                <div class="feature-icon mb-3"><i class="fa fa-sync-alt"></i></div>
                <h5 class="fw-semibold">Đổi trả linh hoạt</h5>
                <p class="text-muted mb-0">Chính sách đổi trả minh bạch, hỗ trợ nhanh trong khung thời gian cho phép.</p>
            </div>
        </div>
    </div>

    <div class="row mt-5 g-4">
        <div class="col-lg-6">
            <div class="about-block h-100 p-4">
                <h4 class="fw-bold mb-3">Sứ mệnh</h4>
                <p class="text-muted mb-0">
                    Đưa thời trang chất lượng đến gần hơn với mọi người qua trải nghiệm mua sắm trực tuyến tiện lợi,
                    minh bạch và đáng tin cậy.
                </p>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="about-block h-100 p-4">
                <h4 class="fw-bold mb-3">Tầm nhìn</h4>
                <p class="text-muted mb-0">
                    Trở thành địa chỉ mua quần áo online được yêu thích cho giới trẻ Việt Nam, nơi mỗi outfit đều thể hiện
                    rõ cá tính người mặc.
                </p>
            </div>
        </div>
    </div>
</section>

<section class="cta-section py-5">
    <div class="container">
        <div class="cta-box p-4 p-lg-5 text-center">
            <h3 class="fw-bold text-white mb-3">Khám phá bộ sưu tập mới tại FashionTee</h3>
            <p class="text-white-50 mb-4">
                Từ basic everyday đến streetwear nổi bật - chọn ngay item phù hợp với phong cách của bạn.
            </p>
            <div class="d-flex justify-content-center flex-wrap gap-2">
                <a href="{{ url('Shop') }}" class="btn btn-light btn-lg px-4">Xem sản phẩm</a>
                <a href="{{ route('contact') }}" class="btn btn-outline-light btn-lg px-4">Liên hệ hỗ trợ</a>
            </div>
        </div>
    </div>
</section>

<style>
    .about-hero {
        background: linear-gradient(120deg, #14532d 0%, #15803d 45%, #22c55e 100%);
    }

    .hero-tag {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.15);
        font-weight: 600;
        letter-spacing: 0.4px;
    }

    .hero-card img {
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .feature-card {
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #fff;
        transition: all 0.2s ease;
    }

    .feature-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 14px 24px rgba(15, 23, 42, 0.08);
    }

    .feature-icon {
        width: 56px;
        height: 56px;
        margin: 0 auto;
        border-radius: 50%;
        background: #dcfce7;
        color: #15803d;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
    }

    .about-block {
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        background: #fafafa;
    }

    .cta-section {
        background: #f8fafc;
    }

    .cta-box {
        border-radius: 16px;
        background: linear-gradient(135deg, #14532d 0%, #15803d 100%);
    }
</style>

@include('client.layout.scripts')
@include('client.layout.footer')
