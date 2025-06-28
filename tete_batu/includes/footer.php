    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h4>Tete Batu Tourism</h4>
                    <p>Menyediakan informasi terbaik tentang wisata Tete Batu, Lombok Timur, Nusa Tenggara Barat.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h4>Link Cepat</h4>
                    <ul class="list-unstyled">
                        <li><a href="index.php#destinasi" class="text-white">Destinasi Wisata</a></li>
                        <li><a href="index.php#tentang" class="text-white">Tentang Tete Batu</a></li>
                        <li><a href="index.php#kontak" class="text-white">Kontak Kami</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h4>Follow Kami</h4>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white fs-4"><i class="fab fa-facebook"></i></a>
                        <a href="#" class="text-white fs-4"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white fs-4"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white fs-4"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <hr class="bg-light">
            <div class="text-center">
                <p>&copy; <?= date('Y') ?> Tete Batu Tourism. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Login Modal -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Login</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="login.php">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" name="remember" id="remember">
                            <label class="form-check-label" for="remember">Ingat saya</label>
                        </div>
                        <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Register Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Daftar Akun Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="register.php">
                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" class="form-control" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" class="form-control" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control" name="confirm_password" required>
                        </div>
                        <button type="submit" name="register" class="btn btn-primary w-100">Daftar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>