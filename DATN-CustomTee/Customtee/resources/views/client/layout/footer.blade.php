<!-- Start Footer -->
    <footer class="bg-dark" id="tempaltemo_footer">
        <div class="container">
            <div class="row">

                <div class="col-md-4 pt-5">
                    <h2 class="h2 text-success border-bottom pb-3 border-light logo">fashionTee</h2>
                    <p class="text-light small mb-3">Thương hiệu thời trang trẻ, chất lượng. Áo thun, áo khoác, quần áo unisex — phong cách của bạn.</p>
                    <ul class="list-unstyled text-light footer-link-list">
                        <li>
                            <i class="fas fa-map-marker-alt fa-fw"></i>
                            Việt Nam
                        </li>
                        <li>
                            <i class="fa fa-phone fa-fw"></i>
                            <a class="text-decoration-none text-light" href="tel:1900-xxxx">1900-xxxx</a>
                        </li>
                        <li>
                            <i class="fa fa-envelope fa-fw"></i>
                            <a class="text-decoration-none text-light" href="mailto:hello@fashiontee.vn">hello@fashiontee.vn</a>
                        </li>
                    </ul>
                </div>

                <div class="col-md-4 pt-5">
                    <h2 class="h2 text-light border-bottom pb-3 border-light">Sản phẩm</h2>
                    <ul class="list-unstyled text-light footer-link-list">
                        <li><a class="text-decoration-none text-light" href="{{ url('/Shop') }}">Tất cả sản phẩm</a></li>
                        <li><a class="text-decoration-none text-light" href="{{ url('/Shop') }}">Áo thun</a></li>
                        <li><a class="text-decoration-none text-light" href="{{ url('/Shop') }}">Áo khoác</a></li>
                        <li><a class="text-decoration-none text-light" href="{{ url('/Shop') }}">Quần</a></li>
                        <li><a class="text-decoration-none text-light" href="{{ url('/Shop') }}">Phụ kiện</a></li>
                    </ul>
                </div>

                <div class="col-md-4 pt-5">
                    <h2 class="h2 text-light border-bottom pb-3 border-light">Liên kết</h2>
                    <ul class="list-unstyled text-light footer-link-list">
                        <li><a class="text-decoration-none text-light" href="{{ url('/') }}">Trang chủ</a></li>
                        <li><a class="text-decoration-none text-light" href="{{ url('/About') }}">Về chúng tôi</a></li>
                        <li><a class="text-decoration-none text-light" href="{{ url('/Shop') }}">Cửa hàng</a></li>
                        <li><a class="text-decoration-none text-light" href="{{ url('/Contact') }}">Liên hệ</a></li>
                        @auth
                        <li><a class="text-decoration-none text-light" href="{{ route('order') }}">Đơn hàng của tôi</a></li>
                        @endauth
                    </ul>
                </div>

            </div>

            <div class="row text-light mb-4">
                <div class="col-12 mb-3">
                    <div class="w-100 my-3 border-top border-light"></div>
                </div>
                <div class="col-auto me-auto">
                    <ul class="list-inline text-left footer-icons">
                        <li class="list-inline-item border border-light rounded-circle text-center">
                            <a class="text-light text-decoration-none" target="_blank" rel="noopener" href="https://facebook.com/"><i class="fab fa-facebook-f fa-lg fa-fw"></i></a>
                        </li>
                        <li class="list-inline-item border border-light rounded-circle text-center">
                            <a class="text-light text-decoration-none" target="_blank" rel="noopener" href="https://www.instagram.com/"><i class="fab fa-instagram fa-lg fa-fw"></i></a>
                        </li>
                        <li class="list-inline-item border border-light rounded-circle text-center">
                            <a class="text-light text-decoration-none" target="_blank" rel="noopener" href="https://twitter.com/"><i class="fab fa-twitter fa-lg fa-fw"></i></a>
                        </li>
                    </ul>
                </div>
                <div class="col-auto">
                    <label class="sr-only" for="subscribeEmail">Email đăng ký nhận tin</label>
                    <div class="input-group mb-2">
                        <input type="text" class="form-control bg-dark border-light" id="subscribeEmail" placeholder="Email nhận tin khuyến mãi">
                        <div class="input-group-text btn-success text-light">Đăng ký</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="w-100 bg-black py-3">
            <div class="container">
                <div class="row pt-2">
                    <div class="col-12">
                        <p class="text-left text-light mb-0">
                            &copy; {{ date('Y') }} <strong>fashionTee</strong>. Bản quyền thuộc fashionTee.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </footer>
    <!-- End Footer -->

    <!-- Start Script -->
    <script src="assets/js/jquery-1.11.0.min.js"></script>
    <script src="assets/js/jquery-migrate-1.2.1.min.js"></script>
    <script src="assets/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/templatemo.js"></script>
    <script src="assets/js/custom.js"></script>
    <!-- End Script -->
</body>

</html>
