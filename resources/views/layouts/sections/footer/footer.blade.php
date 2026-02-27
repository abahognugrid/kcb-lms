@php
$containerFooter = !empty($containerNav) ? $containerNav : 'container-fluid';
@endphp

<!-- Footer-->
<footer class="content-footer footer bg-footer-theme">
  <div class="{{ $containerFooter }}">
    <div class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
      <div class="text-body">
        © <script>document.write(new Date().getFullYear())</script>, made with ❤️ by  GnuGrid
      </div>
      <div class="d-none d-lg-inline-block">
        {{-- <a href="{{ config('variables.licenseUrl') ? config('variables.licenseUrl') : '#' }}" class="footer-link me-4" target="_blank">License</a>
        <a href="{{ config('variables.moreThemes') ? config('variables.moreThemes') : '#' }}" target="_blank" class="footer-link me-4">More Themes</a> --}}
        <a href="#" target="_blank" class="footer-link me-4">
          <i class="menu-icon tf-icons bx bx-book-open"></i>
          Documentation
        </a>
        <a href="#" target="_blank" class="footer-link d-none d-sm-inline-block">
          <i class="menu-icon tf-icons bx bx-support"></i>
          Support
        </a>
      </div>
    </div>
  </div>
</footer>
<!--/ Footer-->
