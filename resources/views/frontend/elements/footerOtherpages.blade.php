
<footer class="footer_wrapper footer_wrapper_bg">
    <div class="copyright">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <span class="reserved_title">
                        {{ Config('Site.copyright') }}
                    </span>
                </div>
                <div class="col-md-6">
                    <ul class="footer-links footer-links_page text-end">
                        <li><a href="{{ url('privacy-policy') }}">{{trans('messages.Privacy Policy')}}</a></li>
                        <li><a href="{{ url('term-condition') }}">{{trans('messages.Terms & Conditions')}}</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</footer>