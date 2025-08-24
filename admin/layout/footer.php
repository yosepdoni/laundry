    <!-- Footer -->
    <footer class="content-footer footer bg-footer-theme">
      <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
        <div class="mb-2 mb-md-0">
          ~
        </div>
        <div>
          Copyright © Tresia Laundry
          <script>
            document.write(new Date().getFullYear());
          </script>
          . All rights reserved.
        </div>
      </div>
    </footer>
    <!-- / Footer -->

    <div class="content-backdrop fade"></div>
    </div>
    <!-- Content wrapper -->
    </div>
    <!-- / Layout page -->
    </div>

    <!-- Overlay -->
    <div class="layout-overlay layout-menu-toggle"></div>
    </div>
    <!-- / Layout wrapper -->
    <script>
      document.addEventListener("DOMContentLoaded", function() {
        const currentParams = new URLSearchParams(window.location.search);
        const currentPage = currentParams.get("page") || "home";

        document.querySelectorAll(".menu-inner .menu-link").forEach(link => {
          const url = new URL(link.href, window.location.origin);
          const linkParams = new URLSearchParams(url.search);
          const linkPage = linkParams.get("page") || "home";

          if (linkPage === currentPage) {
            const li = link.closest("li.menu-item");
            if (li) li.classList.add("active");
          }
        });
      });
    </script>

    <script>
      function logout() {
        if (confirm("Apakah Anda yakin ingin logout?")) {
          window.location.href = "./../logout.php";
        }
      }
    </script>

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="../assets/vendor/js/menu.js"></script>
    <!-- endbuild -->

    <!-- Vendors JS -->
    <script src="../assets/vendor/libs/apex-charts/apexcharts.js"></script>

    <!-- Main JS -->
    <script src="../assets/js/main.js"></script>

    <!-- Page JS -->
    <script src="../assets/js/dashboards-analytics.js"></script>

    <!-- Place this tag in your head or just before your close body tag. -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    </body>

    </html>