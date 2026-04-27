            </main>

            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div class="text-muted">Copyright &copy; Miamy <?= date('Y') ?></div>
                        <div>
                            <a href="#">Confidentialité</a>
                            &middot;
                            <a href="#">Conditions</a>
                        </div>
                    </div>
                </div>
            </footer>

        </div>
    </div>

    <!-- Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <!-- SB Admin -->
    <script src="<?= $GLOBALS['url'] ?>/assets/admins/js/scripts.js"></script>
    <!-- Datatables -->
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@7.1.2/dist/umd/simple-datatables.min.js" crossorigin="anonymous"></script>
    <script src="<?= $GLOBALS['url'] ?>/assets/admins/js/datatables-simple-demo.js"></script>
    <?php if (isset($custom_js)) { echo $custom_js; } ?>
</body>

</html>
