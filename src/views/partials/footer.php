            </main>
        </div>
    </div>

    <script src="assets/js/vendor/jquery.min.js"></script>
    <?php foreach ($extraScripts ?? [] as $vendorScript): ?>
        <script src="assets/js/vendor/<?= htmlspecialchars($vendorScript, ENT_QUOTES, 'UTF-8') ?>"></script>
    <?php endforeach; ?>
    <script src="assets/js/app.js"></script>
    <script src="assets/js/crud-module.js"></script>
    <?php if (!empty($pageScript)): ?>
        <script src="assets/js/modules/<?= htmlspecialchars($pageScript, ENT_QUOTES, 'UTF-8') ?>"></script>
    <?php endif; ?>
</body>
</html>
