@inject('Language', 'App\Models\Language')
@inject('notification', 'App\Models\Notification')

<?php
$segment2    =    Request()->segment(1);
$segment3    =    Request()->segment(2);
$segment4    =    Request()->segment(3);
$segment5    =    Request()->segment(4);
$check_user         = Auth::check();

$getActiveLanguages     = getActiveLanguages();
$langarr = [];
if (!empty($getActiveLanguages)) {
  foreach ($getActiveLanguages as $lang) {
    $langarr[$lang->lang_code] = $lang->listing_title;
  }
}
$language = '';
if (Session::has('applocale')) {
  $select_lang = Session::get('applocale');
  $language    = $langarr[$select_lang];
  $image       = $Language
    ->where('listing_title', $language)
    ->pluck('image')
    ->first();
} else {
  $select_lang = Config::get('app.fallback_locale');
  $language    = $langarr[$select_lang];
  $image       = $Language
    ->where('listing_title', $language)
    ->pluck('image')
    ->first();
}
$languageslist = $Language->where('is_active', 1)->get(['id', 'title', 'image', 'lang_code']);

?>

<header id="header" class="dashboard-header">
  <div class="container">
    <div class="row">
      <div class="col-12">
        <nav class="navbar navbar-expand-lg">
          @auth
          <a class="navbar-brand" href="{{ url('customers-profile') }}">
            <img src="{{asset('public/img/logo.png')}}" alt="">
          </a>
          @else
          <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{asset('public/img/logo.png')}}" alt="">
          </a>
          @endauth

          <button class="navbar-toggler" type="button">
            <span class="navbar-toggler-icon"></span>
          </button>
          <div class="overlay" style="display:none"></div>
          <div class="collapse navbar-collapse">
            @auth
            <div class="user_dropdown_forMobile">
              <div class="nav-item dropdown user_dropdown">
                <a class="nav-link dropdown-toggle" href="javascript:void(0);" id="user-drop" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <h4><span>{{trans('messages.Login as')}}:</span> {{ ucfirst(Auth::user()->customer_type) }} {{trans('messages.Customer')}}</h4>
                  <img src="{{ Auth::user()->image != null ? Auth::user()->image : config('constants.NO_IMAGE_PATH') }}" alt="">

                </a>
                <div class="dropdown-menu" aria-labelledby="user-drop">
                  <div class="user_info">
                    <div class="user_name">
                      <div>{{ Auth::user()->name }}</div>
                      <div class="user_email">
                        <small title="{{ Auth::user()->email }}">{{ Auth::user()->email }}</small>
                      </div>
                    </div>
                    <ul>
                      <li>
                        <a href="{{route('customers-profile')}}"><i class="ion-android-person"></i>{{trans('messages.My Profile')}}</a>
                      </li>
                      <li>
                        <a href="{{route('user-logout')}}"><i class="ion-log-out"></i>{{trans('messages.Logout')}}</a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
                            <ul class="navbar-nav side_menu_forMobile" style="border-bottom: 1px solid #ddd;">
                                <li class="nav-item">
                                    <a href="{{route(Auth::user()->customer_type.'.customer-dashboard')}}" class="nav-link">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M9.58301 1.875C9.92819 1.875 10.208 2.15482 10.208 2.5L10.208 8.33333C10.208 8.67851 9.92819 8.95833 9.58301 8.95833L2.49967 8.95833C2.1545 8.95833 1.87467 8.67851 1.87467 8.33333L1.87467 2.5C1.87467 2.15482 2.1545 1.875 2.49967 1.875L9.58301 1.875ZM8.95801 3.125L3.12467 3.125L3.12467 7.70833L8.95801 7.70833L8.95801 3.125Z" fill="black"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M17.5 11.0416C17.8452 11.0416 18.125 11.3214 18.125 11.6666V17.5C18.125 17.8451 17.8452 18.125 17.5 18.125H10.4167C10.0715 18.125 9.79167 17.8451 9.79167 17.5L9.79167 11.6666C9.79167 11.3214 10.0715 11.0416 10.4167 11.0416L17.5 11.0416ZM16.875 12.2916L11.0417 12.2916V16.875H16.875V12.2916Z" fill="black"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12.917 8.95837C12.5718 8.95837 12.292 8.67855 12.292 8.33337V2.50004C12.292 2.15486 12.5718 1.87504 12.917 1.87504H17.5003C17.8455 1.87504 18.1253 2.15486 18.1253 2.50004V8.33337C18.1253 8.67855 17.8455 8.95837 17.5003 8.95837H12.917ZM13.542 7.70837H16.8753V3.12504H13.542V7.70837Z" fill="black"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M2.5 18.125C2.15482 18.125 1.875 17.8452 1.875 17.5L1.875 11.6667C1.875 11.3215 2.15482 11.0417 2.5 11.0417H7.08333C7.42851 11.0417 7.70833 11.3215 7.70833 11.6667V17.5C7.70833 17.8452 7.42851 18.125 7.08333 18.125H2.5ZM3.125 16.875H6.45833V12.2917H3.125L3.125 16.875Z" fill="black"/>
                                        </svg>
                                        {{trans('messages.Dashboard')}}</a>
                                </li>
                                @if(Auth::user()->customer_type == 'business')
                                <li class="nav-item">
                                    <a href="{{route('business.shipment-requests.view-all')}}" class="nav-link">
                                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M1.14551 4.125C1.14551 3.7453 1.45331 3.4375 1.83301 3.4375H20.1663C20.546 3.4375 20.8538 3.7453 20.8538 4.125V11C20.8538 11.3797 20.546 11.6875 20.1663 11.6875C19.7866 11.6875 19.4788 11.3797 19.4788 11V4.8125H2.52051V17.1875H10.9997C11.3794 17.1875 11.6872 17.4953 11.6872 17.875C11.6872 18.2547 11.3794 18.5625 10.9997 18.5625H1.83301C1.45331 18.5625 1.14551 18.2547 1.14551 17.875V4.125Z" fill="black"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M17.422 12.316C17.6943 12.0776 18.1049 12.0913 18.3608 12.3472L20.1941 14.1806C20.3287 14.3151 20.4015 14.4994 20.3951 14.6896C20.3888 14.8798 20.3039 15.0588 20.1607 15.1841L16.4941 18.3924C16.3687 18.5021 16.2079 18.5625 16.0413 18.5625H14.208C13.8283 18.5625 13.5205 18.2547 13.5205 17.875V16.0417C13.5205 15.8435 13.6061 15.6549 13.7553 15.5243L17.422 12.316ZM14.8955 16.3537V17.1875H15.783L18.7022 14.6332L17.8433 13.7743L14.8955 16.3537Z" fill="black"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M1.28305 3.71254C1.51087 3.40878 1.94179 3.34722 2.24555 3.57504L10.9997 10.1407L19.7539 3.57504C20.0576 3.34722 20.4886 3.40878 20.7164 3.71254C20.9442 4.0163 20.8826 4.44722 20.5789 4.67504L11.4122 11.55C11.1678 11.7334 10.8317 11.7334 10.5872 11.55L1.42055 4.67504C1.11679 4.44722 1.05523 4.0163 1.28305 3.71254Z" fill="black"/>
                                            </svg>                                           
                                                
                                            {{trans('messages.Requests')}}
                                    </a>
                                </li>

                                <li class="nav-item">
                                  <a href="{{route('business.shipment.all-invoice')}}" class="nav-link">
                                      <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                                          xmlns="http://www.w3.org/2000/svg">
                                          <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M1.14551 4.125C1.14551 3.7453 1.45331 3.4375 1.83301 3.4375H20.1663C20.546 3.4375 20.8538 3.7453 20.8538 4.125V11C20.8538 11.3797 20.546 11.6875 20.1663 11.6875C19.7866 11.6875 19.4788 11.3797 19.4788 11V4.8125H2.52051V17.1875H10.9997C11.3794 17.1875 11.6872 17.4953 11.6872 17.875C11.6872 18.2547 11.3794 18.5625 10.9997 18.5625H1.83301C1.45331 18.5625 1.14551 18.2547 1.14551 17.875V4.125Z"
                                              fill="black" />
                                          <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M17.422 12.316C17.6943 12.0776 18.1049 12.0913 18.3608 12.3472L20.1941 14.1806C20.3287 14.3151 20.4015 14.4994 20.3951 14.6896C20.3888 14.8798 20.3039 15.0588 20.1607 15.1841L16.4941 18.3924C16.3687 18.5021 16.2079 18.5625 16.0413 18.5625H14.208C13.8283 18.5625 13.5205 18.2547 13.5205 17.875V16.0417C13.5205 15.8435 13.6061 15.6549 13.7553 15.5243L17.422 12.316ZM14.8955 16.3537V17.1875H15.783L18.7022 14.6332L17.8433 13.7743L14.8955 16.3537Z"
                                              fill="black" />
                                          <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M1.28305 3.71254C1.51087 3.40878 1.94179 3.34722 2.24555 3.57504L10.9997 10.1407L19.7539 3.57504C20.0576 3.34722 20.4886 3.40878 20.7164 3.71254C20.9442 4.0163 20.8826 4.44722 20.5789 4.67504L11.4122 11.55C11.1678 11.7334 10.8317 11.7334 10.5872 11.55L1.42055 4.67504C1.11679 4.44722 1.05523 4.0163 1.28305 3.71254Z"
                                              fill="black" />
                                      </svg>

                                      {{trans('messages.my_invoice')}}
                                                                      </a>
                              </li>
                                <li class="nav-item">
                                    <a href="{{route('business.transportation.view-all')}}" class="nav-link">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M9.72082 1.10761C9.89677 1.01963 10.1039 1.01963 10.2798 1.10761L18.6132 5.27428C18.8249 5.38015 18.9587 5.59656 18.9587 5.83329V14.1666C18.9587 14.4034 18.8249 14.6198 18.6132 14.7256L10.2798 18.8923C10.1039 18.9803 9.89677 18.9803 9.72082 18.8923L1.38748 14.7256C1.17574 14.6198 1.04199 14.4034 1.04199 14.1666V5.83329C1.04199 5.59656 1.17574 5.38015 1.38748 5.27428L9.72082 1.10761ZM2.29199 6.21956V13.7804L10.0003 17.6345L17.7087 13.7804V6.21956L10.0003 2.3654L2.29199 6.21956Z" fill="black"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M1.10811 5.55388C1.26248 5.24514 1.6379 5.12 1.94664 5.27437L10.28 9.44104C10.5887 9.59541 10.7138 9.97083 10.5595 10.2796C10.4051 10.5883 10.0297 10.7134 9.72095 10.5591L1.38762 6.39241C1.07888 6.23804 0.953744 5.86262 1.10811 5.55388Z" fill="black"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10 9.375C10.3452 9.375 10.625 9.65482 10.625 10V18.3333C10.625 18.6785 10.3452 18.9583 10 18.9583C9.65482 18.9583 9.375 18.6785 9.375 18.3333V10C9.375 9.65482 9.65482 9.375 10 9.375Z" fill="black"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M18.8925 5.55388C19.0469 5.86262 18.9217 6.23804 18.613 6.39241L10.2796 10.5591C9.97091 10.7134 9.59549 10.5883 9.44112 10.2796C9.28675 9.97083 9.41189 9.59541 9.72063 9.44104L18.054 5.27437C18.3627 5.12 18.7381 5.24514 18.8925 5.55388Z" fill="black"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M14.7255 3.47051C14.8799 3.77924 14.7547 4.15466 14.446 4.30903L6.11265 8.4757C5.80392 8.63007 5.4285 8.50493 5.27413 8.19619C5.11976 7.88745 5.2449 7.51203 5.55364 7.35766L13.887 3.191C14.1957 3.03663 14.5711 3.16177 14.7255 3.47051Z" fill="black"/>
                                        </svg>
                                            
                                        {{trans('messages.shipments')}}</a>
                                </li>
                                @endif
                                <li class="nav-item">
                                    <a href="{{route('customers-profile')}}" class="nav-link">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0003 2.29163C8.50455 2.29163 7.29199 3.50419 7.29199 4.99996C7.29199 6.49573 8.50455 7.70829 10.0003 7.70829C11.4961 7.70829 12.7087 6.49573 12.7087 4.99996C12.7087 3.50419 11.4961 2.29163 10.0003 2.29163ZM6.04199 4.99996C6.04199 2.81383 7.8142 1.04163 10.0003 1.04163C12.1865 1.04163 13.9587 2.81383 13.9587 4.99996C13.9587 7.18609 12.1865 8.95829 10.0003 8.95829C7.8142 8.95829 6.04199 7.18609 6.04199 4.99996Z" fill="black"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M1.875 18.3334C1.875 13.8461 5.5127 10.2084 10 10.2084C14.4873 10.2084 18.125 13.8461 18.125 18.3334C18.125 18.6786 17.8452 18.9584 17.5 18.9584C17.1548 18.9584 16.875 18.6786 16.875 18.3334C16.875 14.5364 13.7969 11.4584 10 11.4584C6.20305 11.4584 3.125 14.5364 3.125 18.3334C3.125 18.6786 2.84518 18.9584 2.5 18.9584C2.15482 18.9584 1.875 18.6786 1.875 18.3334Z" fill="black"/>
                                        </svg>
                                            
                                        {{trans('messages.My Profile')}}</a>
                                </li>

                                
                                
                                {{-- <li class="nav-item">
                                    <a href="#!" class="nav-link">
                                        <svg width="22" height="18" viewBox="0 0 22 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M7 1.75C7 0.783509 7.78348 0 8.75 0H19.75C20.7165 0 21.5 0.783509 21.5 1.75V12.75C21.5 13.7165 20.7165 14.5 19.75 14.5H8.75C7.78349 14.5 7 13.7165 7 12.75V1.75ZM8.75 1.5C8.61192 1.5 8.5 1.61192 8.5 1.75V12.75C8.5 12.8881 8.61191 13 8.75 13H19.75C19.8881 13 20 12.8881 20 12.75V1.75C20 1.61192 19.8881 1.5 19.75 1.5H8.75Z" fill="black"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.74129 6.1989C3.87983 6.07101 4.06145 6 4.25 6H7.75C8.16421 6 8.5 6.33579 8.5 6.75V13.75C8.5 14.1642 8.16421 14.5 7.75 14.5H0.75C0.335786 14.5 0 14.1642 0 13.75V9.98075C0 9.77143 0.0874788 9.57163 0.241291 9.42965L3.74129 6.1989ZM4.54323 7.5L1.5 10.3091V13H7V7.5H4.54323Z" fill="black"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3.75 14C4.16421 14 4.5 14.3358 4.5 14.75C4.5 15.4403 5.05966 16 5.75 16C6.44034 16 7 15.4403 7 14.75C7 14.3358 7.33579 14 7.75 14C8.16421 14 8.5 14.3358 8.5 14.75C8.5 16.2688 7.26876 17.5 5.75 17.5C4.23124 17.5 3 16.2688 3 14.75C3 14.3358 3.33579 14 3.75 14Z" fill="black"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M14.75 14C15.1642 14 15.5 14.3358 15.5 14.75C15.5 15.4403 16.0597 16 16.75 16C17.4403 16 18 15.4403 18 14.75C18 14.3358 18.3358 14 18.75 14C19.1642 14 19.5 14.3358 19.5 14.75C19.5 16.2688 18.2688 17.5 16.75 17.5C15.2312 17.5 14 16.2688 14 14.75C14 14.3358 14.3358 14 14.75 14Z" fill="black"/>
                                        </svg>
                                            
                                        {{trans('messages.Transport Companies')}}</a>
                                </li> --}}
                                @if(Auth::user()->customer_type == 'business')
                                <!-- <li class="nav-item">
                                    <a href="#!" class="nav-link">
                                        <svg width="21" height="18" viewBox="0 0 21 18" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M10.2335 14.6221L10.2335 14.6221C10.496 14.624 10.7822 14.7211 10.9966 14.8031C11.2415 14.8966 11.5235 15.0241 11.8196 15.1662C12.2269 15.3616 12.6949 15.6013 13.1683 15.8438C13.379 15.9518 13.5908 16.0603 13.7987 16.1657C14.4898 16.5158 15.1449 16.8348 15.6648 17.0371C15.7951 17.0878 15.9093 17.1281 16.0074 17.1588C16.0055 17.0875 16.0013 17.0063 15.994 16.9148C15.954 16.4093 15.8378 15.747 15.6994 15.0356C15.6581 14.8234 15.6147 14.6062 15.5714 14.3896C15.4736 13.9007 15.3765 13.4148 15.306 12.9972C15.2547 12.6938 15.2133 12.4032 15.1953 12.154C15.1802 11.9439 15.1685 11.6317 15.2652 11.367L15.2652 11.367C15.36 11.1076 15.5679 10.8723 15.7152 10.7158C15.8905 10.5295 16.112 10.3223 16.3516 10.1087C16.6813 9.81463 17.0763 9.48127 17.4752 9.14459C17.6526 8.99492 17.8307 8.8446 18.0042 8.69679C18.5826 8.20404 19.1148 7.73509 19.4832 7.34869C19.5289 7.30082 19.5703 7.2559 19.6078 7.21394C19.5343 7.1942 19.4523 7.174 19.3616 7.15355C18.8116 7.02957 18.0769 6.92475 17.294 6.82526C17.0581 6.79528 16.8172 6.76572 16.5776 6.73633C16.04 6.67036 15.5091 6.60522 15.0556 6.5379C14.726 6.48899 14.4175 6.43618 14.161 6.37675C13.9399 6.3255 13.6429 6.24591 13.4237 6.09792L13.4237 6.0979C13.1984 5.94577 13.0203 5.69936 12.8985 5.51637C12.7578 5.30506 12.6098 5.04702 12.4611 4.772C12.2564 4.3937 12.0308 3.94582 11.8034 3.49456C11.7026 3.29445 11.6014 3.09367 11.5016 2.898C11.1677 2.24341 10.8453 1.6384 10.5584 1.201C10.4832 1.08625 10.4157 0.991337 10.3563 0.914815C10.2954 0.990732 10.2258 1.08506 10.1482 1.19925C9.85352 1.63252 9.52041 2.23281 9.17498 2.88238C9.07169 3.07662 8.96696 3.27594 8.86258 3.47459C8.6273 3.92237 8.3938 4.36676 8.18257 4.74192C8.029 5.01468 7.87646 5.27041 7.7322 5.4795C7.6072 5.66067 7.42502 5.90391 7.19765 6.05232L10.2335 14.6221ZM10.2335 14.6221C9.97115 14.6202 9.68345 14.713 9.46771 14.7917M10.2335 14.6221L9.46771 14.7917M9.46771 14.7917C9.22132 14.8816 8.93711 15.0049 8.63857 15.1425M9.46771 14.7917L8.63857 15.1425M8.63857 15.1425C8.22795 15.3319 7.75581 15.5647 7.27824 15.8002M8.63857 15.1425L7.27824 15.8002M7.27824 15.8002C7.06562 15.905 6.85193 16.0104 6.64212 16.1127M7.27824 15.8002L6.64212 16.1127M6.64212 16.1127C5.94497 16.4526 5.28425 16.7619 4.7607 16.9566M6.64212 16.1127L4.7607 16.9566M4.7607 16.9566C4.63036 17.0051 4.51615 17.0435 4.4179 17.0727M4.7607 16.9566L4.4179 17.0727M4.4179 17.0727C4.42102 17.0021 4.4267 16.9219 4.43544 16.8318C4.48441 16.3268 4.61224 15.6663 4.76324 14.957C4.80827 14.7454 4.85553 14.5289 4.90265 14.3129C5.00901 13.8255 5.11469 13.3412 5.19258 12.9247C5.24917 12.6221 5.2957 12.3322 5.31807 12.0833C5.33691 11.8736 5.3542 11.5615 5.26207 11.2951C5.1717 11.0338 4.96756 10.795 4.82302 10.6363C4.65097 10.4474 4.43304 10.2368 4.19728 10.0197C3.87276 9.7208 3.48359 9.38158 3.0906 9.03903C2.91595 8.88679 2.74054 8.7339 2.56972 8.5836C2.00007 8.08237 1.47636 7.60573 1.11494 7.21409C1.07009 7.16549 1.02938 7.11993 0.992608 7.07741C1.06722 7.05852 1.15053 7.03929 1.24276 7.01994C1.79485 6.90407 2.53139 6.8101 3.31591 6.72218C3.55233 6.69568 3.79367 6.66968 4.03368 6.64383C4.5724 6.5858 5.10443 6.52849 5.55911 6.46787C5.88953 6.42381 6.19886 6.37557 6.45627 6.31994C6.6784 6.27193 6.97634 6.19677 7.19762 6.05234L4.4179 17.0727ZM10.5635 0.700019C10.5634 0.700431 10.5595 0.70342 10.5518 0.70775C10.5597 0.701771 10.5635 0.699606 10.5635 0.700019ZM15.9832 17.5121C15.9829 17.5116 15.9843 17.505 15.9886 17.4941C15.9857 17.5072 15.9835 17.5127 15.9832 17.5121ZM4.43571 17.429C4.43536 17.4296 4.43329 17.424 4.43053 17.4108C4.43467 17.4218 4.43605 17.4285 4.43571 17.429ZM10.1645 0.704521C10.1568 0.700032 10.1529 0.696953 10.1529 0.696536C10.1528 0.696119 10.1567 0.698363 10.1645 0.704521Z" stroke="black" stroke-width="1.5"/>
                                        </svg>
                                            
                                        {{trans('messages.Business Customer')}} </a>
                                </li>
                                <li class="nav-item">
                                    <a href="#!" class="nav-link">
                                        <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M10.0003 2.29163C8.50455 2.29163 7.29199 3.50419 7.29199 4.99996C7.29199 6.49573 8.50455 7.70829 10.0003 7.70829C11.4961 7.70829 12.7087 6.49573 12.7087 4.99996C12.7087 3.50419 11.4961 2.29163 10.0003 2.29163ZM6.04199 4.99996C6.04199 2.81383 7.8142 1.04163 10.0003 1.04163C12.1865 1.04163 13.9587 2.81383 13.9587 4.99996C13.9587 7.18609 12.1865 8.95829 10.0003 8.95829C7.8142 8.95829 6.04199 7.18609 6.04199 4.99996Z" fill="black"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M1.875 18.3334C1.875 13.8461 5.5127 10.2084 10 10.2084C14.4873 10.2084 18.125 13.8461 18.125 18.3334C18.125 18.6786 17.8452 18.9584 17.5 18.9584C17.1548 18.9584 16.875 18.6786 16.875 18.3334C16.875 14.5364 13.7969 11.4584 10 11.4584C6.20305 11.4584 3.125 14.5364 3.125 18.3334C3.125 18.6786 2.84518 18.9584 2.5 18.9584C2.15482 18.9584 1.875 18.6786 1.875 18.3334Z" fill="black"/>
                                        </svg>
                                            
                                        {{trans('messages.Private Customers')}}</a>
                                </li> -->
                                @endif
                                @if (Auth::user()->customer_type == 'private')
                                <li class="nav-item">
                                  <a href="{{route('business.shipment.all-invoice')}}" class="nav-link">
                                      <svg width="22" height="22" viewBox="0 0 22 22" fill="none"
                                          xmlns="http://www.w3.org/2000/svg">
                                          <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M15.8122 10.0834C15.8122 9.70368 16.12 9.39587 16.4997 9.39587H20.1663C20.546 9.39587 20.8538 9.70368 20.8538 10.0834V17.4167C20.8538 17.7964 20.546 18.1042 20.1663 18.1042H18.1594L16.9858 19.2778C16.7173 19.5463 16.282 19.5463 16.0135 19.2778L14.8399 18.1042H10.083C9.70331 18.1042 9.39551 17.7964 9.39551 17.4167V13.75C9.39551 13.3703 9.70331 13.0625 10.083 13.0625H15.8122V10.0834ZM17.1872 10.7709V13.75C17.1872 14.1297 16.8794 14.4375 16.4997 14.4375H10.7705V16.7292H15.1247C15.307 16.7292 15.4819 16.8016 15.6108 16.9306L16.4997 17.8194L17.3885 16.9306C17.5175 16.8016 17.6923 16.7292 17.8747 16.7292H19.4788V10.7709H17.1872Z"
                                              fill="black" />
                                          <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M1.14551 2.75C1.14551 2.3703 1.45331 2.0625 1.83301 2.0625H16.4997C16.8794 2.0625 17.1872 2.3703 17.1872 2.75V13.75C17.1872 14.1297 16.8794 14.4375 16.4997 14.4375H8.07611L6.44414 16.0695C6.17566 16.338 5.74036 16.338 5.47187 16.0695L3.8399 14.4375H1.83301C1.45331 14.4375 1.14551 14.1297 1.14551 13.75V2.75ZM2.52051 3.4375V13.0625H4.12467C4.30701 13.0625 4.48188 13.1349 4.61081 13.2639L5.95801 14.6111L7.3052 13.2639C7.43414 13.1349 7.609 13.0625 7.79134 13.0625H15.8122V3.4375H2.52051Z"
                                              fill="black" />
                                          <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M4.8125 10.0834C4.8125 9.70368 5.1203 9.39587 5.5 9.39587H8.25C8.6297 9.39587 8.9375 9.70368 8.9375 10.0834C8.9375 10.4631 8.6297 10.7709 8.25 10.7709H5.5C5.1203 10.7709 4.8125 10.4631 4.8125 10.0834Z"
                                              fill="black" />
                                          <path fill-rule="evenodd" clip-rule="evenodd"
                                              d="M4.8125 6.41663C4.8125 6.03693 5.1203 5.72913 5.5 5.72913H11C11.3797 5.72913 11.6875 6.03693 11.6875 6.41663C11.6875 6.79632 11.3797 7.10413 11 7.10413H5.5C5.1203 7.10413 4.8125 6.79632 4.8125 6.41663Z"
                                              fill="black" />
                                      </svg>
  
                                      {{trans('messages.my_invoice')}}
  
                                  </a>
                              </li>
                              @endif
                                <li class="nav-item">
                                    <a href="{{route(Auth::user()->customer_type.'.chat')}}" class="nav-link"> 
                                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M15.8122 10.0834C15.8122 9.70368 16.12 9.39587 16.4997 9.39587H20.1663C20.546 9.39587 20.8538 9.70368 20.8538 10.0834V17.4167C20.8538 17.7964 20.546 18.1042 20.1663 18.1042H18.1594L16.9858 19.2778C16.7173 19.5463 16.282 19.5463 16.0135 19.2778L14.8399 18.1042H10.083C9.70331 18.1042 9.39551 17.7964 9.39551 17.4167V13.75C9.39551 13.3703 9.70331 13.0625 10.083 13.0625H15.8122V10.0834ZM17.1872 10.7709V13.75C17.1872 14.1297 16.8794 14.4375 16.4997 14.4375H10.7705V16.7292H15.1247C15.307 16.7292 15.4819 16.8016 15.6108 16.9306L16.4997 17.8194L17.3885 16.9306C17.5175 16.8016 17.6923 16.7292 17.8747 16.7292H19.4788V10.7709H17.1872Z" fill="black"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M1.14551 2.75C1.14551 2.3703 1.45331 2.0625 1.83301 2.0625H16.4997C16.8794 2.0625 17.1872 2.3703 17.1872 2.75V13.75C17.1872 14.1297 16.8794 14.4375 16.4997 14.4375H8.07611L6.44414 16.0695C6.17566 16.338 5.74036 16.338 5.47187 16.0695L3.8399 14.4375H1.83301C1.45331 14.4375 1.14551 14.1297 1.14551 13.75V2.75ZM2.52051 3.4375V13.0625H4.12467C4.30701 13.0625 4.48188 13.1349 4.61081 13.2639L5.95801 14.6111L7.3052 13.2639C7.43414 13.1349 7.609 13.0625 7.79134 13.0625H15.8122V3.4375H2.52051Z" fill="black"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M4.8125 10.0834C4.8125 9.70368 5.1203 9.39587 5.5 9.39587H8.25C8.6297 9.39587 8.9375 9.70368 8.9375 10.0834C8.9375 10.4631 8.6297 10.7709 8.25 10.7709H5.5C5.1203 10.7709 4.8125 10.4631 4.8125 10.0834Z" fill="black"/>
                                            <path fill-rule="evenodd" clip-rule="evenodd" d="M4.8125 6.41663C4.8125 6.03693 5.1203 5.72913 5.5 5.72913H11C11.3797 5.72913 11.6875 6.03693 11.6875 6.41663C11.6875 6.79632 11.3797 7.10413 11 7.10413H5.5C5.1203 7.10413 4.8125 6.79632 4.8125 6.41663Z" fill="black"/>
                                        </svg>
                                            
                                        {{trans('messages.Chat')}}</a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{route(Auth::user()->customer_type.'.customerservice')}}" class="nav-link">
                                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M18.5625 4.8125H15.125V4.125C15.125 3.57799 14.9077 3.05339 14.5209 2.66659C14.1341 2.2798 13.6095 2.0625 13.0625 2.0625H8.9375C8.39049 2.0625 7.86589 2.2798 7.47909 2.66659C7.0923 3.05339 6.875 3.57799 6.875 4.125V4.8125H3.4375C3.07283 4.8125 2.72309 4.95737 2.46523 5.21523C2.20737 5.47309 2.0625 5.82283 2.0625 6.1875V17.1875C2.0625 17.5522 2.20737 17.9019 2.46523 18.1598C2.72309 18.4176 3.07283 18.5625 3.4375 18.5625H18.5625C18.9272 18.5625 19.2769 18.4176 19.5348 18.1598C19.7926 17.9019 19.9375 17.5522 19.9375 17.1875V6.1875C19.9375 5.82283 19.7926 5.47309 19.5348 5.21523C19.2769 4.95737 18.9272 4.8125 18.5625 4.8125ZM8.25 4.125C8.25 3.94266 8.32243 3.7678 8.45136 3.63886C8.5803 3.50993 8.75516 3.4375 8.9375 3.4375H13.0625C13.2448 3.4375 13.4197 3.50993 13.5486 3.63886C13.6776 3.7678 13.75 3.94266 13.75 4.125V4.8125H8.25V4.125ZM18.5625 6.1875V9.76336C16.242 11.0264 13.642 11.688 11 11.6875C8.35811 11.688 5.75818 11.0268 3.4375 9.76422V6.1875H18.5625ZM18.5625 17.1875H3.4375V11.3128C5.79212 12.4645 8.37879 13.063 11 13.0625C13.6213 13.0626 16.2079 12.4638 18.5625 11.312V17.1875ZM8.9375 9.625C8.9375 9.44266 9.00993 9.2678 9.13886 9.13886C9.2678 9.00993 9.44266 8.9375 9.625 8.9375H12.375C12.5573 8.9375 12.7322 9.00993 12.8611 9.13886C12.9901 9.2678 13.0625 9.44266 13.0625 9.625C13.0625 9.80734 12.9901 9.9822 12.8611 10.1111C12.7322 10.2401 12.5573 10.3125 12.375 10.3125H9.625C9.44266 10.3125 9.2678 10.2401 9.13886 10.1111C9.00993 9.9822 8.9375 9.80734 8.9375 9.625Z" fill="black"/>
                                        </svg>
                                            
                                        {{trans('messages.Customer Service')}}</a>
                                </li>
                                @if(Auth::user()->customer_type == 'business')
                                <li class="nav-item">
                                      <a href="javascript:void(0);" class="nav-link" onclick="openModal()">
                                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                                          <path fill-rule="evenodd" clip-rule="evenodd" d="M1.14551 4.125C1.14551 3.7453 1.45331 3.4375 1.83301 3.4375H20.1663C20.546 3.4375 20.8538 3.7453 20.8538 4.125V11C20.8538 11.3797 20.546 11.6875 20.1663 11.6875C19.7866 11.6875 19.4788 11.3797 19.4788 11V4.8125H2.52051V17.1875H10.9997C11.3794 17.1875 11.6872 17.4953 11.6872 17.875C11.6872 18.2547 11.3794 18.5625 10.9997 18.5625H1.83301C1.45331 18.5625 1.14551 18.2547 1.14551 17.875V4.125Z" fill="black"/>
                                          <path fill-rule="evenodd" clip-rule="evenodd" d="M17.422 12.316C17.6943 12.0776 18.1049 12.0913 18.3608 12.3472L20.1941 14.1806C20.3287 14.3151 20.4015 14.4994 20.3951 14.6896C20.3888 14.8798 20.3039 15.0588 20.1607 15.1841L16.4941 18.3924C16.3687 18.5021 16.2079 18.5625 16.0413 18.5625H14.208C13.8283 18.5625 13.5205 18.2547 13.5205 17.875V16.0417C13.5205 15.8435 13.6061 15.6549 13.7553 15.5243L17.422 12.316ZM14.8955 16.3537V17.1875H15.783L18.7022 14.6332L17.8433 13.7743L14.8955 16.3537Z" fill="black"/>
                                          <path fill-rule="evenodd" clip-rule="evenodd" d="M1.28305 3.71254C1.51087 3.40878 1.94179 3.34722 2.24555 3.57504L10.9997 10.1407L19.7539 3.57504C20.0576 3.34722 20.4886 3.40878 20.7164 3.71254C20.9442 4.0163 20.8826 4.44722 20.5789 4.67504L11.4122 11.55C11.1678 11.7334 10.8317 11.7334 10.5872 11.55L1.42055 4.67504C1.11679 4.44722 1.05523 4.0163 1.28305 3.71254Z" fill="black"/>
                                          </svg>                                           
                                                  
                                {{trans('messages.Send Proposal')}}</a>
                                    
                                </li>
                                @endif
                                <li class="nav-item">
                                    <a href="{{route('user-logout')}}" class="nav-link">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <path d="M12 21C16.411 21 20 17.411 20 13C20 9.64999 17.928 6.77899 15 5.58899V7.81199C15.9109 8.33791 16.6676 9.09405 17.1941 10.0046C17.7207 10.9152 17.9986 11.9482 18 13C18 16.309 15.309 19 12 19C8.691 19 6 16.309 6 13C6.00133 11.9481 6.2792 10.9151 6.80575 10.0045C7.3323 9.09396 8.08901 8.33783 9 7.81199V5.58899C6.072 6.77899 4 9.64999 4 13C4 17.411 7.589 21 12 21Z" fill="black"/>
                                            <path d="M11 2H13V12H11V2Z" fill="black"/>
                                        </svg>
                                            
                                        {{trans('messages.Logout')}}</a>
                                </li>
                            </ul>
                            <style>
                                @media only screen and (min-width: 992px) {
                                    .side_menu_forMobile,.user_dropdown_forMobile {
                                        display: none;
                                    }
                                }
                            </style>
            @endauth
            <ul class="navbar-nav">
              <li class="nav-item {{ Route::currentRouteName() == '' ? 'active' : '' }}">
                <a class="nav-link" href="{{route('index')}}">{{trans('messages.Home')}}</a>
              </li>
              <li class="nav-item {{ Route::currentRouteName() == 'about' ? 'active' : '' }}">
                <a class="nav-link" href="{{route('about')}}"> {{trans('messages.About Us')}} </a>
              </li>
              <li class="nav-item {{ Route::currentRouteName() == 'service' ? 'active' : '' }}">
                <a class="nav-link" href="{{route('service')}}">{{trans('messages.Services')}}</a>
              </li>
              @if(!Auth::user())
                <li class="nav-item {{Route::currentRouteName() == 'truck-company-registration' ? 'active' : '' }}">
                  <a class="nav-link" href="{{route('truckCcompanyRegistration')}}">{{trans('messages.Become A Partner')}}</a>
                </li>
              @endif
              <li class="nav-item {{ Route::currentRouteName() == 'contact' ? 'active' : '' }}">
                <a class="nav-link" href="{{route('contact')}}">{{trans('messages.Contact Us')}}</a>
              </li>

              <!-- <li class="nav-item {{ Route::currentRouteName() == 'contact' ? 'active' : '' }}">
                <a class="nav-link" href="{{route('contact')}}">{{trans('messages.Contact Us')}}</a>
              </li> -->
            </ul>
            <div class="extra_nav ">
              <ul class="navbar-nav">
                <li class="nav-item langugae_filter for_desktop">
                  <a class="nav-link extra_btn dropdown-toggle lang_drop" href="javascript:void(0);">
                    <span class="flag_ico"><img src="{{ config('constants.LANGUAGE_IMAGE_PATH') . $image }}" alt=""></span> {{ strtoupper($select_lang) }} <i class="far fa-chevron-down"></i>
                  </a>
                  <div class="lang_dropdown">
                    @if(!empty($languageslist))
                    @foreach($languageslist as $item)
                    <div class="lang_country" id="{{$item->id}}">
                      <a class="nav-link extra_btn dropdown-toggle lang_drop" href="{{ route('lang.switch', $item->lang_code) }}" data-code="{{$item->lang_code}}" style="color: black">
                        <span class="flag_ico">
                          <img src="{{ config('constants.LANGUAGE_IMAGE_PATH') . $item->image }}" alt="">
                        </span> {{ $item->title }}
                      </a>
                    </div>
                    @endforeach
                    @endif
                  </div>
                </li>
                @auth
                <li class="nav-item">
                  <div class="nav-item dropdown user_dropdown">
                    <a class="nav-link dropdown-toggle" href="javascript:void(0);" id="user-drop" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <h4><span>{{trans('messages.Login as')}}:</span> {{ ucfirst(Auth::user()->customer_type) }} {{trans('messages.Customer')}}</h4>
                      <img src="{{ Auth::user()->image != null ? Auth::user()->image : config('constants.NO_IMAGE_PATH') }}" alt="">

                    </a>
                    <div class="dropdown-menu" aria-labelledby="user-drop">
                      <div class="user_info">
                        <div class="user_name">
                          <div>{{Auth::user()->name}}</div>
                          <div class="user_email">
                            <small title="{{ Auth::user()->email }}">{{Auth::user()->email}}</small>
                          </div>
                        </div>
                        <ul>
                          <li>
                            <a href="{{route(Auth::user()->customer_type.'.customer-dashboard')}}"><i
                                    class="ion-android-person"></i>
                                {{ trans('messages.Dashboard') }}</a>
                        </li>
                          <li>
                            <a href="{{route('customers-profile')}}"><i class="ion-android-person"></i>{{trans('messages.My Profile')}}</a>
                          </li>
                          <li>
                            <a href="{{route('user-logout')}}"><i class="ion-log-out"></i>{{trans('messages.Logout')}}</a>
                          </li>
                        </ul>
                      </div>
                    </div>
                  </div>
                </li>

                <li class="nav-item">
                  @if(Auth::user()->customer_type == 'private')
                  <a href="{{ route('private.chat') }}">
                    @else
                    <a href="{{ route('business.chat') }}">
                    @endif
                    <svg width="21" height="22" viewBox="0 0 21 22" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M5.02344 7.04649C5.02344 6.62265 5.35141 6.27905 5.756 6.27905H14.5467C14.9513 6.27905 15.2793 6.62265 15.2793 7.04649C15.2793 7.47034 14.9513 7.81394 14.5467 7.81394H5.756C5.35141 7.81394 5.02344 7.47034 5.02344 7.04649Z" />
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M5.02344 11.1395C5.02344 10.7157 5.35141 10.3721 5.756 10.3721H14.5467C14.9513 10.3721 15.2793 10.7157 15.2793 11.1395C15.2793 11.5634 14.9513 11.907 14.5467 11.907H5.756C5.35141 11.907 5.02344 11.5634 5.02344 11.1395Z" />
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M5.02344 15.2327C5.02344 14.8088 5.35141 14.4652 5.756 14.4652H10.6397C11.0443 14.4652 11.3723 14.8088 11.3723 15.2327C11.3723 15.6565 11.0443 16.0001 10.6397 16.0001H5.756C5.35141 16.0001 5.02344 15.6565 5.02344 15.2327Z" />
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M10.5 1.53488C5.51017 1.53488 1.46512 5.77256 1.46512 11V20.4651H10.5C15.4898 20.4651 19.5349 16.2274 19.5349 11C19.5349 5.77256 15.4898 1.53488 10.5 1.53488ZM0.732558 21.2326L0 21.2325V11C0 4.92487 4.70101 0 10.5 0C16.299 0 21 4.92487 21 11C21 17.0751 16.299 22 10.5 22H0.732637L0.732558 21.2326ZM0.732558 21.2326L0.732637 22C0.328056 22 0 21.6563 0 21.2325L0.732558 21.2326Z" />
                    </svg></a>

                </li>
                <?php
                $notificationCount = $notification
                    ->where('user_id', Auth::user()->id)
                    ->where('language_id', getAppLocaleId())
                    ->where('is_read', 0)
                    ->count();
                
                
                ?>


                <li class="nav-item">
                  @if(Auth::user()->customer_type == 'private')
                  
                  <a href="{{ route('private.notification-list') }}" class="notification_icon">
                      @else
                      <a href="{{ route('business.notification-list') }}" class="notification_icon item">
                      @endif
                      @if ($notificationCount > 0)    
                         <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                         <path d="M11 1.54054C14.5321 1.54054 17.3954 4.41438 17.3954 7.95946V17.4595H4.60465V7.95946C4.60465 4.41438 7.46794 1.54054 11 1.54054ZM18.9302 17.4595V7.95946C18.9302 3.56356 15.3798 0 11 0C6.62025 0 3.06977 3.56356 3.06977 7.95946V17.4595H0.767442C0.343595 17.4595 0 17.8043 0 18.2297C0 18.6551 0.343595 19 0.767442 19H21.2326C21.6564 19 22 18.6551 22 18.2297C22 17.8043 21.6564 17.4595 21.2326 17.4595H18.9302Z" fill="white" />
                         <path d="M8 18.6667C8 18.2985 8.30996 18 8.69231 18H13.3077C13.69 18 14 18.2985 14 18.6667V19.1111C14 20.7066 12.6568 22 11 22C9.34316 22 8 20.7066 8 19.1111V18.6667ZM9.40097 19.3333C9.51295 20.0872 10.1862 20.6667 11 20.6667C11.8138 20.6667 12.487 20.0872 12.599 19.3333H9.40097Z" fill="white" />
                         </svg>
                        <span>{{ $notificationCount }} </span>
                                    </a>
                     @else
                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                        d="M11 1.54054C14.5321 1.54054 17.3954 4.41438 17.3954 7.95946V17.4595H4.60465V7.95946C4.60465 4.41438 7.46794 1.54054 11 1.54054ZM18.9302 17.4595V7.95946C18.9302 3.56356 15.3798 0 11 0C6.62025 0 3.06977 3.56356 3.06977 7.95946V17.4595H0.767442C0.343595 17.4595 0 17.8043 0 18.2297C0 18.6551 0.343595 19 0.767442 19H21.2326C21.6564 19 22 18.6551 22 18.2297C22 17.8043 21.6564 17.4595 21.2326 17.4595H18.9302Z"
                         fill="white" />
                        <path
                        d="M8 18.6667C8 18.2985 8.30996 18 8.69231 18H13.3077C13.69 18 14 18.2985 14 18.6667V19.1111C14 20.7066 12.6568 22 11 22C9.34316 22 8 20.7066 8 19.1111V18.6667ZM9.40097 19.3333C9.51295 20.0872 10.1862 20.6667 11 20.6667C11.8138 20.6667 12.487 20.0872 12.599 19.3333H9.40097Z"
                        fill="white" />
                        </svg>
                     @endif
                </li>
                @endauth

                @guest
                <li class="nav-item login_btn">
                  <a class="nav-link secondary-btn" href="{{route('login')}}">{{trans('messages.login')}}</a>
                </li>

                <li class="nav-item sign_up_btn">
                  <a class="nav-link primary-btn" href="{{route('sign-up')}}">{{trans('messages.Book a Shipment')}}</a>
                </li>
                @endguest

              </ul>
            </div>
          </div>
          

          <!-- For Mobile -->

          <div class="extra_nav for_mobile">
              <ul class="navbar-nav ms-auto">
                  <li class="nav-item langugae_filter">
                      <a class="nav-link extra_btn dropdown-toggle lang_drop" href="javascript:void(0);">
                          <span class="flag_ico"><img src="{{ config('constants.LANGUAGE_IMAGE_PATH') . $image }}" alt=""></span> {{ strtoupper($select_lang) }} <i
                              class="far fa-chevron-down"></i>
                      </a>
                      <div class="lang_dropdown">
                      @if(!empty($languageslist))
                        @foreach($languageslist as $item)
                        <div class="lang_country" id="{{$item->id}}">
                          <a href="{{ route('lang.switch', $item->lang_code) }}" data-code="{{$item->lang_code}}">
                            <span class="flag_ico">
                              <img src="{{ config('constants.LANGUAGE_IMAGE_PATH') . $item->image }}" alt="">
                            </span > {{ $item->title }}
                          </a>
                        </div>
                        @endforeach
                      @endif
                      </div>
                  </li>
            



              @auth
              <li class="nav-item">
                @if(Auth::user()->customer_type == 'private')
                <a href="{{ route('private.chat') }}">
                  @else
                  <a href="{{ route('business.chat') }}">
                  @endif
                  <svg width="21" height="22" viewBox="0 0 21 22" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M5.02344 7.04649C5.02344 6.62265 5.35141 6.27905 5.756 6.27905H14.5467C14.9513 6.27905 15.2793 6.62265 15.2793 7.04649C15.2793 7.47034 14.9513 7.81394 14.5467 7.81394H5.756C5.35141 7.81394 5.02344 7.47034 5.02344 7.04649Z" />
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M5.02344 11.1395C5.02344 10.7157 5.35141 10.3721 5.756 10.3721H14.5467C14.9513 10.3721 15.2793 10.7157 15.2793 11.1395C15.2793 11.5634 14.9513 11.907 14.5467 11.907H5.756C5.35141 11.907 5.02344 11.5634 5.02344 11.1395Z" />
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M5.02344 15.2327C5.02344 14.8088 5.35141 14.4652 5.756 14.4652H10.6397C11.0443 14.4652 11.3723 14.8088 11.3723 15.2327C11.3723 15.6565 11.0443 16.0001 10.6397 16.0001H5.756C5.35141 16.0001 5.02344 15.6565 5.02344 15.2327Z" />
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10.5 1.53488C5.51017 1.53488 1.46512 5.77256 1.46512 11V20.4651H10.5C15.4898 20.4651 19.5349 16.2274 19.5349 11C19.5349 5.77256 15.4898 1.53488 10.5 1.53488ZM0.732558 21.2326L0 21.2325V11C0 4.92487 4.70101 0 10.5 0C16.299 0 21 4.92487 21 11C21 17.0751 16.299 22 10.5 22H0.732637L0.732558 21.2326ZM0.732558 21.2326L0.732637 22C0.328056 22 0 21.6563 0 21.2325L0.732558 21.2326Z" />
                  </svg></a>
              </li>

              <li class="nav-item">
                @if(Auth::user()->customer_type == 'private')
                <a href="{{ route('private.notification-list') }}" class="notification_icon">
                    @else
                    <a href="{{ route('business.notification-list') }}" class="notification_icon">
                    @endif 
                    @if ($notificationCount > 0)    
                         <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                         <path d="M11 1.54054C14.5321 1.54054 17.3954 4.41438 17.3954 7.95946V17.4595H4.60465V7.95946C4.60465 4.41438 7.46794 1.54054 11 1.54054ZM18.9302 17.4595V7.95946C18.9302 3.56356 15.3798 0 11 0C6.62025 0 3.06977 3.56356 3.06977 7.95946V17.4595H0.767442C0.343595 17.4595 0 17.8043 0 18.2297C0 18.6551 0.343595 19 0.767442 19H21.2326C21.6564 19 22 18.6551 22 18.2297C22 17.8043 21.6564 17.4595 21.2326 17.4595H18.9302Z" fill="white" />
                         <path d="M8 18.6667C8 18.2985 8.30996 18 8.69231 18H13.3077C13.69 18 14 18.2985 14 18.6667V19.1111C14 20.7066 12.6568 22 11 22C9.34316 22 8 20.7066 8 19.1111V18.6667ZM9.40097 19.3333C9.51295 20.0872 10.1862 20.6667 11 20.6667C11.8138 20.6667 12.487 20.0872 12.599 19.3333H9.40097Z" fill="white" />
                         </svg>
                        <span>{{ $notificationCount }} </span>
                                    </a>
                     @else
                        <svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                        d="M11 1.54054C14.5321 1.54054 17.3954 4.41438 17.3954 7.95946V17.4595H4.60465V7.95946C4.60465 4.41438 7.46794 1.54054 11 1.54054ZM18.9302 17.4595V7.95946C18.9302 3.56356 15.3798 0 11 0C6.62025 0 3.06977 3.56356 3.06977 7.95946V17.4595H0.767442C0.343595 17.4595 0 17.8043 0 18.2297C0 18.6551 0.343595 19 0.767442 19H21.2326C21.6564 19 22 18.6551 22 18.2297C22 17.8043 21.6564 17.4595 21.2326 17.4595H18.9302Z"
                         fill="white" />
                        <path
                        d="M8 18.6667C8 18.2985 8.30996 18 8.69231 18H13.3077C13.69 18 14 18.2985 14 18.6667V19.1111C14 20.7066 12.6568 22 11 22C9.34316 22 8 20.7066 8 19.1111V18.6667ZM9.40097 19.3333C9.51295 20.0872 10.1862 20.6667 11 20.6667C11.8138 20.6667 12.487 20.0872 12.599 19.3333H9.40097Z"
                        fill="white" />
                        </svg>
                     @endif
                </a>
              </li>
              @endauth
            </ul>
          </div>
        </nav>
      </div>
    </div>
  </div>
</header>
@section('extraScriptCode')
@include('frontend.send-proposal-modal')
@stop

<style>
  #message{
      height: 100px;
  }
  .cross{
      margin-right: 10px;
  margin-top: 10px;
  font-size: 20px;
  }
  
</style>