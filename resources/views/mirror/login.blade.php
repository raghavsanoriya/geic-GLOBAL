<!DOCTYPE html>
<html lang="en">



<!-- Mirrored from lms.rocket-soft.org/login by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 25 Aug 2026 16:22:08 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->
<head>
    <meta charset="utf-8">
<!-- CSRF Token -->
<meta name="csrf-token" content="KkDAnXKdDFkgpTFwX3uTuPHuAseZywMbZmqb7QZE">

<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">

<meta name='robots' content="index, follow, all">

    <meta name="description" content="Login Page Description">
    <meta property="og:description" content="Login Page Description">
    <meta name='twitter:description' content='Login Page Description'>

<link rel='shortcut icon' type='image/x-icon' href="/store/1/geic-icon.png">
<link rel="manifest" href="mix-manifest7b30.json?v=4">
<meta name="theme-color" content="#FFF">
<!-- Windows Phone -->
<meta name="msapplication-starturl" content="/">
<meta name="msapplication-TileColor" content="#FFF">
<meta name="msapplication-TileImage" content="ms-icon-144x144.html">
<!-- iOS Safari -->
<meta name="apple-mobile-web-app-title" content="Rocket LMS">
<link rel="apple-touch-icon" href="/store/1/geic-icon.png">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
<!-- Android -->
<link rel='icon' href='/store/1/geic-icon.png'>
<meta name="application-name" content="Rocket LMS">
<meta name="mobile-web-app-capable" content="yes">
<!-- Other -->
<meta name="layoutmode" content="fitscreen/standard">
<link rel="home" href="index.html">

<!-- Open Graph -->
<meta property='og:title' content='Login'>
<meta name='twitter:card' content='summary'>
<meta name='twitter:title' content='Login'>


<meta property='og:site_name' content='https://lms.rocket-soft.org/Rocket LMS'>
<meta property='og:image' content='/store/1/geic-icon.png'>
<meta name='twitter:image' content='/store/1/geic-icon.png'>
<meta property='og:locale' content='en_US.html'>
<meta property='og:type' content='website'>



    <title>Login | Rocket LMS</title>

    <!-- General CSS File -->
    <link rel="stylesheet" href="assets/default/vendors/simplebar/simplebar.css">
    <link rel="stylesheet" href="assets/design_1/css/app.min.css">

    
            <link rel="stylesheet" href="assets/design_1/css/parts/theme/headers/header_1.min.css">
    
            <link rel="stylesheet" href="assets/design_1/css/parts/theme/footers/footer_1.min.css">
    
        <link rel="stylesheet" href="assets/default/vendors/swiper/swiper-bundle.min.css">
    <link rel="stylesheet" href="assets/design_1/css/parts/auth/theme_1.min.css">
    
    <style>
        

        @font-face {
                      font-family: 'main-font-family';
                      font-style: normal;
                      font-weight: 400;
                      font-display: swap;
                      src: url(store/1/fonts/Gilroy-Regular.woff2) format('woff2');
                    }@font-face {
                      font-family: 'main-font-family';
                      font-style: normal;
                      font-weight: bold;
                      font-display: swap;
                      src: url(store/1/fonts/Gilroy-Bold.woff2) format('woff2');
                    }@font-face {
                      font-family: 'main-font-family';
                      font-style: normal;
                      font-weight: 500;
                      font-display: swap;
                      src: url(store/1/fonts/Gilroy-Medium.woff2) format('woff2');
                    }@font-face {
                      font-family: 'rtl-font-family';
                      font-style: normal;
                      font-weight: 400;
                      font-display: swap;
                      src: url(store/1/fonts/Tajawal-Regular.woff2) format('woff2');
                    }@font-face {
                      font-family: 'rtl-font-family';
                      font-style: normal;
                      font-weight: bold;
                      font-display: swap;
                      src: url(store/1/fonts/Tajawal-Bold.woff2) format('woff2');
                    }@font-face {
                      font-family: 'rtl-font-family';
                      font-style: normal;
                      font-weight: 500;
                      font-display: swap;
                      src: url(store/1/fonts/Tajawal-Medium.woff2) format('woff2');
                    }

        :root{
--primary:#E31E24;
--primary-hover:#0064e5;
--primary-border:#E31E24;
--primary-hover-border:#0064e5;
--primary-btn-color:#ffffff;
--primary-btn-hover-color:#ffffff;
--primary-saturated:#67a9ff;
--secondary:#0e2145;
--secondary-hover:#0c1d3e;
--secondary-border:#0e2145;
--secondary-hover-border:#0c1d3e;
--secondary-btn-color:#ffffff;
--secondary-btn-hover-color:#ffffff;
--accent:#fe6257;
--accent-hover:#e4584e;
--accent-border:#fe6257;
--accent-hover-border:#e4584e;
--accent-btn-color:#ffffff;
--accent-btn-hover-color:#ffffff;
--success:#3fcd82;
--success-hover:#38b875;
--success-border:#3fcd82;
--success-hover-border:#38b875;
--success-btn-color:#ffffff;
--success-btn-hover-color:#ffffff;
--info:#67a9ff;
--info-hover:#5c98e5;
--info-border:#67a9ff;
--info-hover-border:#5c98e5;
--info-btn-color:#ffffff;
--info-btn-hover-color:#ffffff;
--warning:#ffa200;
--warning-hover:#e59100;
--warning-border:#ffa200;
--warning-hover-border:#e59100;
--warning-btn-color:#ffffff;
--warning-btn-hover-color:#ffffff;
--danger:#f63c3c;
--danger-hover:#dd3636;
--danger-border:#f63c3c;
--danger-hover-border:#dd3636;
--danger-btn-color:#ffffff;
--danger-btn-hover-color:#ffffff;
--dark:#121f3e;
--black:#000000;
--white:#ffffff;
--white-hover:#e5e5e5;
--white-border:#ffffff;
--white-hover-border:#e5e5e5;
--white-btn-color:#ffffff;
--white-btn-hover-color:#ffffff;
--gray-100:#fafcff;
--gray-200:#f0f4f9;
--gray-300:#e9edf3;
--gray-400:#cdd5e2;
--gray-500:#97a7bf;
--gray:#f5f8f9;
--section-bg:#eaf0f3;
}

    </style>

</head>

<body class="bg-gray  light-mode">

<div id="app">

    
            <div id="appHeaderArea">
            <div id="themeHeaderVacuum"></div>
    <div class="theme-header-1">
        
                    <div class="theme-header-1__top-navbar bg-primary pb-54 pt-12">
    <div class="container">
        <div class="row align-items-center">

            <div class="col-12 col-lg-4">
                <div class="d-flex align-items-center gap-24">
                    
                                            <div class="d-flex align-items-center gap-8 opacity-75">
                            <svg width="16px" height="16x" class="icons text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-miterlimit="10" stroke-width="1.5" d="M21.97 18.33c0 .36-.08.73-.25 1.09-.17.36-.39.7-.68 1.02-.49.54-1.03.93-1.64 1.18-.6.25-1.25.38-1.95.38-1.02 0-2.11-.24-3.26-.73s-2.3-1.15-3.44-1.98a28.75 28.75 0 01-3.28-2.8 28.414 28.414 0 01-2.79-3.27c-.82-1.14-1.48-2.28-1.96-3.41C2.24 8.67 2 7.58 2 6.54c0-.68.12-1.33.36-1.93.24-.61.62-1.17 1.15-1.67C4.15 2.31 4.85 2 5.59 2c.28 0 .56.06.81.18.26.12.49.3.67.56l2.32 3.27c.18.25.31.48.4.7.09.21.14.42.14.61 0 .24-.07.48-.21.71-.13.23-.32.47-.56.71l-.76.79c-.11.11-.16.24-.16.4 0 .08.01.15.03.23.03.08.06.14.08.2.18.33.49.76.93 1.28.45.52.93 1.05 1.45 1.58.54.53 1.06 1.02 1.59 1.47.52.44.95.74 1.29.92.05.02.11.05.18.08.08.03.16.04.25.04.17 0 .3-.06.41-.17l.76-.75c.25-.25.49-.44.72-.56.23-.14.46-.21.71-.21.19 0 .39.04.61.13.22.09.45.22.7.39l3.31 2.35c.26.18.44.39.55.64.1.25.16.5.16.78z"/>
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.5 9c0-.6-.47-1.52-1.17-2.27-.64-.69-1.49-1.23-2.33-1.23M22 9c0-3.87-3.13-7-7-7"/>
</svg>                            <span class="text-white">+1 (323) 555-9876</span>
                        </div>
                    
                    
                                            <div class="d-flex align-items-center gap-8 opacity-75">
                            <svg width="16px" height="16x" class="icons text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="1.5" d="M17 20.5H7c-3 0-5-1.5-5-5v-7c0-3.5 2-5 5-5h10c3 0 5 1.5 5 5v7c0 3.5-2 5-5 5z"/>
  <path stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="1.5" d="M17 9l-3.13 2.5c-1.03.82-2.72.82-3.75 0L7 9"/>
</svg>                            <span class="text-white">mail@rocket-soft.org</span>
                        </div>
                    
                    
                                            <div class="js-theme-color-toggle theme-color-toggle light-mode d-flex-center size-16 opacity-75">
                            <svg width="16px" height="16px" class="dark-icon icons text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.03 12.42c.36 5.15 4.73 9.34 9.96 9.57 3.69.16 6.99-1.56 8.97-4.27.82-1.11.38-1.85-.99-1.6-.67.12-1.36.17-2.08.14C13 16.06 9 11.97 8.98 7.14c-.01-1.3.26-2.53.75-3.65.54-1.24-.11-1.83-1.36-1.3C4.41 3.86 1.7 7.85 2.03 12.42z"/>
</svg>                            <svg width="16px" height="16px" class="light-icon icons text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18.5a6.5 6.5 0 100-13 6.5 6.5 0 000 13z"/>
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.14 19.14l-.13-.13m0-14.02l.13-.13-.13.13zM4.86 19.14l.13-.13-.13.13zM12 2.08V2v.08zM12 22v-.08.08zM2.08 12H2h.08zM22 12h-.08.08zM4.99 4.99l-.13-.13.13.13z"/>
</svg>                        </div>
                    
                </div>
            </div>

            <div class="col-12 col-lg-8 mt-12 mt-lg-0">
                <div class="row">
                    
                    <div class="col-12 col-lg-4">
                        <form action="https://lms.rocket-soft.org/search" method="get" class="theme-header-1__top-navbar-search position-relative">
                            <input class="form-control bg-transparent opacity-75" type="text" name="search" placeholder="Search..." aria-label="Search">

                            <button type="submit" class="btn-transparent d-flex-center search-icon">
                                <svg width="16px" height="16px" class="icons text-white opacity-75" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 20a9 9 0 100-18 9 9 0 000 18zM18.93 20.69c.53 1.6 1.74 1.76 2.67.36.85-1.28.29-2.33-1.25-2.33-1.14-.01-1.78.88-1.42 1.97z"/>
</svg>                            </button>
                        </form>
                    </div>
                                         <div class="col-12 col-lg-8 mt-12 mt-lg-8">
                         
                        <div class="d-flex align-items-center justify-content-between gap-12 gap-lg-24">
                            <div class="d-flex align-items-center gap-12 gap-lg-24">
                                
                                <div class="js-language-select theme-header-1__dropdown position-relative">
    <form action="https://lms.rocket-soft.org/locale" method="post">
        <input type="hidden" name="_token" value="KkDAnXKdDFkgpTFwX3uTuPHuAseZywMbZmqb7QZE">
        <input type="hidden" name="locale" value="en">

                                    <div class="d-flex align-items-center gap-8">
                    <div class="size-32 d-flex-center bg-white-10 rounded-8">
                        <img src="vendor/blade-country-flags/4x3-us.svg" class="img-fluid" width="16px" height="16px" alt="English flag"/>
                    </div>
                    <span class="js-lang-title text-white opacity-75 d-none d-md-flex">English</span>
                    <svg width="16px" height="16px" class="icons text-white opacity-75" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="1.5" d="M19.92 8.95l-6.52 6.52c-.77.77-2.03.77-2.8 0L4.08 8.95"/>
</svg>                </div>
                                                                </form>

    <div class="header-1-dropdown-menu py-8 mx-w-200">

        <div class="py-8 px-16 font-12 text-gray-500">Select a Language</div>

                    <div class="js-language-dropdown-item header-1-dropdown-menu__item cursor-pointer active" data-value="EN" data-title="English">
                <div class=" d-flex align-items-center w-100 px-16 py-8 text-dark bg-transparent">
                    <div class="header-1-dropdown-menu__flag">
                        <img src="vendor/blade-country-flags/4x3-us.svg" class="img-cover" alt="English flag"/>
                    </div>
                    <span class="ml-8 font-14">English</span>
                </div>
            </div>
                    <div class="js-language-dropdown-item header-1-dropdown-menu__item cursor-pointer " data-value="AR" data-title="Arabic">
                <div class=" d-flex align-items-center w-100 px-16 py-8 text-dark bg-transparent">
                    <div class="header-1-dropdown-menu__flag">
                        <img src="vendor/blade-country-flags/4x3-sa.svg" class="img-cover" alt="Arabic flag"/>
                    </div>
                    <span class="ml-8 font-14">Arabic</span>
                </div>
            </div>
                    <div class="js-language-dropdown-item header-1-dropdown-menu__item cursor-pointer " data-value="ES" data-title="Spanish">
                <div class=" d-flex align-items-center w-100 px-16 py-8 text-dark bg-transparent">
                    <div class="header-1-dropdown-menu__flag">
                        <img src="vendor/blade-country-flags/4x3-es.svg" class="img-cover" alt="Spanish flag"/>
                    </div>
                    <span class="ml-8 font-14">Spanish</span>
                </div>
            </div>
        
    </div>
</div>

                                
                                <div class="js-currency-select theme-header-1__dropdown position-relative">
        <form action="https://lms.rocket-soft.org/set-currency" method="post">
            <input type="hidden" name="_token" value="KkDAnXKdDFkgpTFwX3uTuPHuAseZywMbZmqb7QZE">
            <input type="hidden" name="currency" value="USD">

                                                <div class="d-flex align-items-center gap-8">
                        <div class="size-32 d-flex-center bg-white-10 rounded-8">
                            <span class="font-12 text-white opacity-75">$</span>
                        </div>
                        <span class="js-lang-title text-white opacity-75 d-none d-md-flex">USD</span>
                        <svg width="16px" height="16px" class="icons text-white opacity-75" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="1.5" d="M19.92 8.95l-6.52 6.52c-.77.77-2.03.77-2.8 0L4.08 8.95"/>
</svg>                    </div>
                                                                                            </form>

        <div class="header-1-dropdown-menu py-8">

            <div class="py-8 px-16 font-12 text-gray-500">Select a Currency</div>

                            <div class="js-currency-dropdown-item header-1-dropdown-menu__item cursor-pointer active" data-value="USD" data-title="USD">
                    <div class=" d-flex align-items-center justify-content-between w-100 px-16 py-8 bg-transparent">
                        <span class="text-gray-500 text-dark">United States Dollar</span>

                        <div class="header-1-dropdown-menu__item-sign-box position-relative d-flex-center rounded-8">
                            $
                        </div>
                    </div>
                </div>
                            <div class="js-currency-dropdown-item header-1-dropdown-menu__item cursor-pointer " data-value="EUR" data-title="EUR">
                    <div class=" d-flex align-items-center justify-content-between w-100 px-16 py-8 bg-transparent">
                        <span class="text-gray-500 text-dark">Euro Member Countries</span>

                        <div class="header-1-dropdown-menu__item-sign-box position-relative d-flex-center rounded-8">
                            €
                        </div>
                    </div>
                </div>
                            <div class="js-currency-dropdown-item header-1-dropdown-menu__item cursor-pointer " data-value="INR" data-title="INR">
                    <div class=" d-flex align-items-center justify-content-between w-100 px-16 py-8 bg-transparent">
                        <span class="text-gray-500 text-dark">India Rupee</span>

                        <div class="header-1-dropdown-menu__item-sign-box position-relative d-flex-center rounded-8">
                            ₹
                        </div>
                    </div>
                </div>
            
        </div>
    </div>

                                
                                                                    <div class="js-view-cart-drawer position-relative d-flex-center size-32 bg-white-10 rounded-8 cursor-pointer">
                                        <svg width="20px" height="20px" class="icons text-white opacity-75" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="1.5" d="M8.81 2L5.19 5.63M15.19 2l3.62 3.63"/>
  <path stroke-width="1.5" d="M2 7.85c0-1.85.99-2 2.22-2h15.56c1.23 0 2.22.15 2.22 2 0 2.15-.99 2-2.22 2H4.22C2.99 9.85 2 10 2 7.85z"/>
  <path stroke-linecap="round" stroke-width="1.5" d="M9.76 14v3.55M14.36 14v3.55M3.5 10l1.41 8.64C5.23 20.58 6 22 8.86 22h6.03c3.11 0 3.57-1.36 3.93-3.24L20.5 10"/>
</svg>                                        <span class="js-cart-counter theme-header-1__top-navbar-cart-counter d-inline-flex-center font-12 text-white d-none">0</span>
                                    </div>
                                                            </div>

                            <div class="d-flex align-items-center">
                                                                                                            <a href="login.html" class="d-flex align-items-center text-white opacity-75">
                                            <span class="">Login</span>
                                        </a>
                                    
                                                                            <a href="register.html" class="d-flex align-items-center text-white opacity-75 ml-32">
                                            <span class="">Register</span>
                                        </a>
                                                                                                </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
        
        
        <div id="themeHeaderSticky" class="theme-header-1__main">
    <div class="container h-100 position-relative">
        <div class="theme-header-1__main-mask"></div>

        <div class="position-relative z-index-2 bg-white rounded-24 w-100 h-100 p-16">
            <div class="row align-items-center h-100">
                
                <div class="col-6 col-lg-2">
                    <a href="index.html" class="theme-header-1__logo text-left d-block">
                                                    <img src="store/1/default_images/logo.svg" class="img-fluid light-only" alt="Rocket LMS">
                        
                                                    <img src="store/1/default_images/logo-dark.svg" class="img-fluid dark-only" alt="Rocket LMS">
                                            </a>
                </div>

                
                <div class="col-6 col-lg-2 d-flex align-items-center justify-content-end">
                    <div class="theme-header-1__dropdown position-relative">
    <div class="d-inline-flex align-items-center gap-8 p-16 rounded-12 bg-gray-100">
        <svg width="16px" height="16px" class="icons text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="1.5" d="M5 10h2c2 0 3-1 3-3V5c0-2-1-3-3-3H5C3 2 2 3 2 5v2c0 2 1 3 3 3zM17 10h2c2 0 3-1 3-3V5c0-2-1-3-3-3h-2c-2 0-3 1-3 3v2c0 2 1 3 3 3zM17 22h2c2 0 3-1 3-3v-2c0-2-1-3-3-3h-2c-2 0-3 1-3 3v2c0 2 1 3 3 3zM5 22h2c2 0 3-1 3-3v-2c0-2-1-3-3-3H5c-2 0-3 1-3 3v2c0 2 1 3 3 3z"/>
</svg>        <span class="text-gray-500">Categories</span>
    </div>

    <div class="header-1-dropdown-menu auth-user-info-dropdown-menu py-12">

        <ul class="theme-header-1__categories">
                            <li class="header-1-dropdown-menu__item position-relative">
                    <a href="categories/Development.html" class="d-flex align-items-center justify-content-between w-100 px-16 py-8  js-has-subcategory">
                        <div class="d-flex align-items-center">
                                                            <img src="store/1/default_images/categories_icons/code.png" class="cat-dropdown-menu-icon mr-8" alt="Development icon">
                            
                            <span class="">Development</span>
                        </div>

                                                    <svg width="16px" height="16px" class="icons" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="1.5" d="M8.91 19.92l6.52-6.52c.77-.77.77-2.03 0-2.8L8.91 4.08"/>
</svg>                                            </a>

                                            <ul class="header-1-dropdown-menu__sub-menu py-12">
                                                            <li class="">
                                    <a href="categories/Development/Web-Development.html" class="d-flex align-items-center w-100 px-16 py-8">
                                        <div class="d-flex align-items-center w-100">
                                                                                            <img src="store/1/default_images/categories_icons/sub_categories/layout.png" class="cat-dropdown-menu-icon mr-8" alt="Web Development icon">
                                            
                                            <span class="">Web Development</span>
                                        </div>
                                    </a>
                                </li>
                                                            <li class="">
                                    <a href="categories/Development/Mobile-Development.html" class="d-flex align-items-center w-100 px-16 py-8">
                                        <div class="d-flex align-items-center w-100">
                                                                                            <img src="store/1/default_images/categories_icons/sub_categories/smartphone.png" class="cat-dropdown-menu-icon mr-8" alt="Mobile Development icon">
                                            
                                            <span class="">Mobile Development</span>
                                        </div>
                                    </a>
                                </li>
                                                            <li class="">
                                    <a href="categories/Development/Game-Development.html" class="d-flex align-items-center w-100 px-16 py-8">
                                        <div class="d-flex align-items-center w-100">
                                                                                            <img src="store/1/default_images/categories_icons/sub_categories/codesandbox.png" class="cat-dropdown-menu-icon mr-8" alt="Game Development icon">
                                            
                                            <span class="">Game Development</span>
                                        </div>
                                    </a>
                                </li>
                                                    </ul>
                                    </li>
                            <li class="header-1-dropdown-menu__item position-relative">
                    <a href="categories/Business.html" class="d-flex align-items-center justify-content-between w-100 px-16 py-8  js-has-subcategory">
                        <div class="d-flex align-items-center">
                                                            <img src="store/1/default_images/categories_icons/anchor.png" class="cat-dropdown-menu-icon mr-8" alt="Business icon">
                            
                            <span class="">Business</span>
                        </div>

                                                    <svg width="16px" height="16px" class="icons" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="1.5" d="M8.91 19.92l6.52-6.52c.77-.77.77-2.03 0-2.8L8.91 4.08"/>
</svg>                                            </a>

                                            <ul class="header-1-dropdown-menu__sub-menu py-12">
                                                            <li class="">
                                    <a href="categories/Business/Management.html" class="d-flex align-items-center w-100 px-16 py-8">
                                        <div class="d-flex align-items-center w-100">
                                                                                            <img src="store/1/default_images/categories_icons/sub_categories/users.png" class="cat-dropdown-menu-icon mr-8" alt="Management icon">
                                            
                                            <span class="">Management</span>
                                        </div>
                                    </a>
                                </li>
                                                            <li class="">
                                    <a href="categories/Business/Communications.html" class="d-flex align-items-center w-100 px-16 py-8">
                                        <div class="d-flex align-items-center w-100">
                                                                                            <img src="store/1/default_images/categories_icons/sub_categories/share-2.png" class="cat-dropdown-menu-icon mr-8" alt="Communications icon">
                                            
                                            <span class="">Communications</span>
                                        </div>
                                    </a>
                                </li>
                                                            <li class="">
                                    <a href="categories/Business/Business-Strategy.html" class="d-flex align-items-center w-100 px-16 py-8">
                                        <div class="d-flex align-items-center w-100">
                                                                                            <img src="store/1/default_images/categories_icons/sub_categories/target.png" class="cat-dropdown-menu-icon mr-8" alt="Business Strategy icon">
                                            
                                            <span class="">Business Strategy</span>
                                        </div>
                                    </a>
                                </li>
                                                    </ul>
                                    </li>
                            <li class="header-1-dropdown-menu__item position-relative">
                    <a href="categories/Marketing.html" class="d-flex align-items-center justify-content-between w-100 px-16 py-8  ">
                        <div class="d-flex align-items-center">
                                                            <img src="store/1/default_images/categories_icons/pie-chart.png" class="cat-dropdown-menu-icon mr-8" alt="Marketing icon">
                            
                            <span class="">Marketing</span>
                        </div>

                                            </a>

                                    </li>
                            <li class="header-1-dropdown-menu__item position-relative">
                    <a href="categories/Lifestyles.html" class="d-flex align-items-center justify-content-between w-100 px-16 py-8  js-has-subcategory">
                        <div class="d-flex align-items-center">
                                                            <img src="store/1/default_images/categories_icons/umbrella.png" class="cat-dropdown-menu-icon mr-8" alt="Lifestyle icon">
                            
                            <span class="">Lifestyle</span>
                        </div>

                                                    <svg width="16px" height="16px" class="icons" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="1.5" d="M8.91 19.92l6.52-6.52c.77-.77.77-2.03 0-2.8L8.91 4.08"/>
</svg>                                            </a>

                                            <ul class="header-1-dropdown-menu__sub-menu py-12">
                                                            <li class="">
                                    <a href="categories/Lifestyles/Lifestyle.html" class="d-flex align-items-center w-100 px-16 py-8">
                                        <div class="d-flex align-items-center w-100">
                                                                                            <img src="store/1/default_images/categories_icons/sub_categories/sun.png" class="cat-dropdown-menu-icon mr-8" alt="Lifestyle icon">
                                            
                                            <span class="">Lifestyle</span>
                                        </div>
                                    </a>
                                </li>
                                                            <li class="">
                                    <a href="categories/Lifestyles/Beauty-and-Makeup.html" class="d-flex align-items-center w-100 px-16 py-8">
                                        <div class="d-flex align-items-center w-100">
                                                                                            <img src="store/1/default_images/categories_icons/sub_categories/droplet.png" class="cat-dropdown-menu-icon mr-8" alt="Beauty &amp; Makeup icon">
                                            
                                            <span class="">Beauty &amp; Makeup</span>
                                        </div>
                                    </a>
                                </li>
                                                    </ul>
                                    </li>
                            <li class="header-1-dropdown-menu__item position-relative">
                    <a href="categories/Health-and-Fitness.html" class="d-flex align-items-center justify-content-between w-100 px-16 py-8  ">
                        <div class="d-flex align-items-center">
                                                            <img src="store/1/default_images/categories_icons/heart.png" class="cat-dropdown-menu-icon mr-8" alt="Health &amp; Fitness icon">
                            
                            <span class="">Health &amp; Fitness</span>
                        </div>

                                            </a>

                                    </li>
                            <li class="header-1-dropdown-menu__item position-relative">
                    <a href="categories/Academics.html" class="d-flex align-items-center justify-content-between w-100 px-16 py-8  js-has-subcategory">
                        <div class="d-flex align-items-center">
                                                            <img src="store/1/default_images/categories_icons/briefcase.png" class="cat-dropdown-menu-icon mr-8" alt="Academics icon">
                            
                            <span class="">Academics</span>
                        </div>

                                                    <svg width="16px" height="16px" class="icons" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="1.5" d="M8.91 19.92l6.52-6.52c.77-.77.77-2.03 0-2.8L8.91 4.08"/>
</svg>                                            </a>

                                            <ul class="header-1-dropdown-menu__sub-menu py-12">
                                                            <li class="">
                                    <a href="categories/Academics/Math.html" class="d-flex align-items-center w-100 px-16 py-8">
                                        <div class="d-flex align-items-center w-100">
                                                                                            <img src="store/1/default_images/categories_icons/sub_categories/divide-square.png" class="cat-dropdown-menu-icon mr-8" alt="Math icon">
                                            
                                            <span class="">Math</span>
                                        </div>
                                    </a>
                                </li>
                                                            <li class="">
                                    <a href="categories/Academics/Science.html" class="d-flex align-items-center w-100 px-16 py-8">
                                        <div class="d-flex align-items-center w-100">
                                                                                            <img src="store/1/default_images/categories_icons/sub_categories/zap.png" class="cat-dropdown-menu-icon mr-8" alt="Science icon">
                                            
                                            <span class="">Science</span>
                                        </div>
                                    </a>
                                </li>
                                                            <li class="">
                                    <a href="categories/Academics/Language.html" class="d-flex align-items-center w-100 px-16 py-8">
                                        <div class="d-flex align-items-center w-100">
                                                                                            <img src="store/1/default_images/categories_icons/sub_categories/globe.png" class="cat-dropdown-menu-icon mr-8" alt="Language icon">
                                            
                                            <span class="">Language</span>
                                        </div>
                                    </a>
                                </li>
                                                    </ul>
                                    </li>
                            <li class="header-1-dropdown-menu__item position-relative">
                    <a href="categories/Design.html" class="d-flex align-items-center justify-content-between w-100 px-16 py-8  ">
                        <div class="d-flex align-items-center">
                                                            <img src="store/1/default_images/categories_icons/feather.png" class="cat-dropdown-menu-icon mr-8" alt="Design icon">
                            
                            <span class="">Design</span>
                        </div>

                                            </a>

                                    </li>
                    </ul>

    </div>
</div>
                </div>

                
                <div class="col-6 col-lg-5 mt-12 mt-lg-0">
                                            <div class="d-flex align-items-center gap-16 gap-lg-32">
                                                            <a href="index.html" class="text-dark">Home</a>
                                                            <a href="classes8676.html?sort=newest" class="text-dark">Courses</a>
                                                            <a href="instructor-finder.html" class="text-dark">Instructors</a>
                                                            <a href="products.html" class="text-dark">Store</a>
                                                            <a href="forums.html" class="text-dark">Forums</a>
                                                            <a href="events.html" class="text-dark">Events</a>
                                                            <a href="jobs.html" class="text-dark">Jobs</a>
                                                    </div>
                                    </div>

                
                <div class="col-6 col-lg-3 mt-12 mt-lg-0 d-flex align-items-center justify-content-end">
                                            <a href="login.html" class="btn-flip-effect btn btn-primary btn-lg gap-8 text-white" data-text="Start Learning">
                                                            <svg width="20px" height="20px" class="icons" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
  <path d="M18.38 12.84v4.93c0 1.27-.99 2.63-2.18 3.03l-3.19 1.06c-.56.19-1.47.19-2.02 0L7.8 20.8c-1.2-.4-2.18-1.76-2.18-3.03l.01-4.93 4.42 2.88c1.08.71 2.86.71 3.94 0l4.39-2.88z" opacity=".4"/>
  <path d="M19.98 6.46l-5.99-3.93c-1.08-.71-2.86-.71-3.94 0L4.03 6.46c-1.93 1.25-1.93 4.08 0 5.34l1.6 1.04 4.42 2.88c1.08.71 2.86.71 3.94 0l4.39-2.88 1.37-.9V15c0 .41.34.75.75.75s.75-.34.75-.75v-4.92c.4-1.29-.01-2.79-1.27-3.62z"/>
</svg>                            
                            <span class="btn-flip-effect__text text-white">Start Learning</span>
                        </a>
                                    </div>

            </div>
        </div>
    </div>
</div>
    </div>
        </div>
    
    
        <section class="container mt-96 mb-104 position-relative">
        <div class="row justify-content-center">
            <div class="col-12 col-lg-8">
                <div class="auth-page-card position-relative">
                    <div class="auth-page-card__mask"></div>

                    <div class="position-relative bg-white rounded-32 p-16 z-index-2">
                        <div class="row">
                            <div class="col-12 col-lg-6">

                                    <form method="Post" action="https://lms.rocket-soft.org/login" class="">
        <input type="hidden" name="_token" value="KkDAnXKdDFkgpTFwX3uTuPHuAseZywMbZmqb7QZE">

        <div class="pl-16 pt-16">
            <div class="font-16 font-weight-bold">Welcome Back! 👋</div>
            <h1 class="font-24 mt-4 mb-32">Log in to your account</h1>

            
            <div class="d-flex align-items-center gap-4 p-4 rounded-12 border-gray-300 mb-28">
        <div class="auth-register-method-item flex-1">
            <input type="radio" name="type" value="email" id="emailType" class="" checked>
            <label class="d-flex-center cursor-pointer" for="emailType">Email</label>
        </div>

        <div class="auth-register-method-item flex-1">
            <input type="radio" name="type" value="mobile" id="mobileType" class="" >
            <label class="d-flex-center cursor-pointer" for="mobileType">Phone</label>
        </div>
    </div>

    <div class="js-email-fields form-group ">
        <label class="form-group-label" for="email">Email:</label>
        <input name="email" type="email" class="form-control " id="email" value="" aria-describedby="emailHelp">
            </div>

    <div class="js-mobile-fields d-none">
        <div class="form-group js-auth-mobile-container">
    <div class="register-mobile-form-group position-relative ">
        <label class="form-group-label">Phone </label>

        <div class="row">
            <div class="col-4 h-100 pr-0">
                <select name="country_code" class="form-control country-code-select2" data-dropdown-parent=".js-auth-page-form">
                                            <option value="+1" >USA (+1)</option>
                                            <option value="+44" >UK (+44)</option>
                                            <option value="+213" >Algeria (+213)</option>
                                            <option value="+376" >Andorra (+376)</option>
                                            <option value="+244" >Angola (+244)</option>
                                            <option value="+1264" >Anguilla (+1264)</option>
                                            <option value="+1268" >Antigua &amp;amp; Barbuda (+1268)</option>
                                            <option value="+54" >Argentina (+54)</option>
                                            <option value="+374" >Armenia (+374)</option>
                                            <option value="+297" >Aruba (+297)</option>
                                            <option value="+61" >Australia (+61)</option>
                                            <option value="+43" >Austria (+43)</option>
                                            <option value="+994" >Azerbaijan (+994)</option>
                                            <option value="+1242" >Bahamas (+1242)</option>
                                            <option value="+973" >Bahrain (+973)</option>
                                            <option value="+880" >Bangladesh (+880)</option>
                                            <option value="+1246" >Barbados (+1246)</option>
                                            <option value="+375" >Belarus (+375)</option>
                                            <option value="+32" >Belgium (+32)</option>
                                            <option value="+501" >Belize (+501)</option>
                                            <option value="+229" >Benin (+229)</option>
                                            <option value="+1441" >Bermuda (+1441)</option>
                                            <option value="+975" >Bhutan (+975)</option>
                                            <option value="+591" >Bolivia (+591)</option>
                                            <option value="+387" >Bosnia Herzegovina (+387)</option>
                                            <option value="+267" >Botswana (+267)</option>
                                            <option value="+55" >Brazil (+55)</option>
                                            <option value="+673" >Brunei (+673)</option>
                                            <option value="+359" >Bulgaria (+359)</option>
                                            <option value="+226" >Burkina Faso (+226)</option>
                                            <option value="+257" >Burundi (+257)</option>
                                            <option value="+855" >Cambodia (+855)</option>
                                            <option value="+237" >Cameroon (+237)</option>
                                            <option value="+1" >Canada (+1)</option>
                                            <option value="+238" >Cape Verde Islands (+238)</option>
                                            <option value="+1345" >Cayman Islands (+1345)</option>
                                            <option value="+236" >Central African Republic (+236)</option>
                                            <option value="+56" >Chile (+56)</option>
                                            <option value="+86" >China (+86)</option>
                                            <option value="+57" >Colombia (+57)</option>
                                            <option value="+269" >Comoros (+269)</option>
                                            <option value="+242" >Congo (+242)</option>
                                            <option value="+682" >Cook Islands (+682)</option>
                                            <option value="+506" >Costa Rica (+506)</option>
                                            <option value="+385" >Croatia (+385)</option>
                                            <option value="+53" >Cuba (+53)</option>
                                            <option value="+90" >Cyprus - North (+90)</option>
                                            <option value="+357" >Cyprus - South (+357)</option>
                                            <option value="+420" >Czech Republic (+420)</option>
                                            <option value="+45" >Denmark (+45)</option>
                                            <option value="+253" >Djibouti (+253)</option>
                                            <option value="+1809" >Dominica (+1809)</option>
                                            <option value="+1809" >Dominican Republic (+1809)</option>
                                            <option value="+593" >Ecuador (+593)</option>
                                            <option value="+20" >Egypt (+20)</option>
                                            <option value="+503" >El Salvador (+503)</option>
                                            <option value="+240" >Equatorial Guinea (+240)</option>
                                            <option value="+291" >Eritrea (+291)</option>
                                            <option value="+372" >Estonia (+372)</option>
                                            <option value="+251" >Ethiopia (+251)</option>
                                            <option value="+500" >Falkland Islands (+500)</option>
                                            <option value="+298" >Faroe Islands (+298)</option>
                                            <option value="+679" >Fiji (+679)</option>
                                            <option value="+358" >Finland (+358)</option>
                                            <option value="+33" >France (+33)</option>
                                            <option value="+594" >French Guiana (+594)</option>
                                            <option value="+689" >French Polynesia (+689)</option>
                                            <option value="+241" >Gabon (+241)</option>
                                            <option value="+220" >Gambia (+220)</option>
                                            <option value="+7880" >Georgia (+7880)</option>
                                            <option value="+49" >Germany (+49)</option>
                                            <option value="+233" >Ghana (+233)</option>
                                            <option value="+350" >Gibraltar (+350)</option>
                                            <option value="+30" >Greece (+30)</option>
                                            <option value="+299" >Greenland (+299)</option>
                                            <option value="+1473" >Grenada (+1473)</option>
                                            <option value="+590" >Guadeloupe (+590)</option>
                                            <option value="+671" >Guam (+671)</option>
                                            <option value="+502" >Guatemala (+502)</option>
                                            <option value="+224" >Guinea (+224)</option>
                                            <option value="+245" >Guinea - Bissau (+245)</option>
                                            <option value="+592" >Guyana (+592)</option>
                                            <option value="+509" >Haiti (+509)</option>
                                            <option value="+504" >Honduras (+504)</option>
                                            <option value="+852" >Hong Kong (+852)</option>
                                            <option value="+36" >Hungary (+36)</option>
                                            <option value="+354" >Iceland (+354)</option>
                                            <option value="+91" >India (+91)</option>
                                            <option value="+62" >Indonesia (+62)</option>
                                            <option value="+964" >Iraq (+964)</option>
                                            <option value="+98" >Iran (+98)</option>
                                            <option value="+353" >Ireland (+353)</option>
                                            <option value="+972" >Israel (+972)</option>
                                            <option value="+39" >Italy (+39)</option>
                                            <option value="+1876" >Jamaica (+1876)</option>
                                            <option value="+81" >Japan (+81)</option>
                                            <option value="+962" >Jordan (+962)</option>
                                            <option value="+7" >Kazakhstan (+7)</option>
                                            <option value="+254" >Kenya (+254)</option>
                                            <option value="+686" >Kiribati (+686)</option>
                                            <option value="+850" >Korea - North (+850)</option>
                                            <option value="+82" >Korea - South (+82)</option>
                                            <option value="+965" >Kuwait (+965)</option>
                                            <option value="+996" >Kyrgyzstan (+996)</option>
                                            <option value="+856" >Laos (+856)</option>
                                            <option value="+371" >Latvia (+371)</option>
                                            <option value="+961" >Lebanon (+961)</option>
                                            <option value="+266" >Lesotho (+266)</option>
                                            <option value="+231" >Liberia (+231)</option>
                                            <option value="+218" >Libya (+218)</option>
                                            <option value="+417" >Liechtenstein (+417)</option>
                                            <option value="+370" >Lithuania (+370)</option>
                                            <option value="+352" >Luxembourg (+352)</option>
                                            <option value="+853" >Macao (+853)</option>
                                            <option value="+389" >Macedonia (+389)</option>
                                            <option value="+261" >Madagascar (+261)</option>
                                            <option value="+265" >Malawi (+265)</option>
                                            <option value="+60" >Malaysia (+60)</option>
                                            <option value="+960" >Maldives (+960)</option>
                                            <option value="+223" >Mali (+223)</option>
                                            <option value="+356" >Malta (+356)</option>
                                            <option value="+692" >Marshall Islands (+692)</option>
                                            <option value="+596" >Martinique (+596)</option>
                                            <option value="+222" >Mauritania (+222)</option>
                                            <option value="+269" >Mayotte (+269)</option>
                                            <option value="+52" >Mexico (+52)</option>
                                            <option value="+691" >Micronesia (+691)</option>
                                            <option value="+373" >Moldova (+373)</option>
                                            <option value="+377" >Monaco (+377)</option>
                                            <option value="+976" >Mongolia (+976)</option>
                                            <option value="+1664" >Montserrat (+1664)</option>
                                            <option value="+212" >Morocco (+212)</option>
                                            <option value="+258" >Mozambique (+258)</option>
                                            <option value="+95" >Myanmar (+95)</option>
                                            <option value="+264" >Namibia (+264)</option>
                                            <option value="+674" >Nauru (+674)</option>
                                            <option value="+977" >Nepal (+977)</option>
                                            <option value="+31" >Netherlands (+31)</option>
                                            <option value="+687" >New Caledonia (+687)</option>
                                            <option value="+64" >New Zealand (+64)</option>
                                            <option value="+505" >Nicaragua (+505)</option>
                                            <option value="+227" >Niger (+227)</option>
                                            <option value="+234" >Nigeria (+234)</option>
                                            <option value="+683" >Niue (+683)</option>
                                            <option value="+672" >Norfolk Islands (+672)</option>
                                            <option value="+670" >Northern Marianas (+670)</option>
                                            <option value="+47" >Norway (+47)</option>
                                            <option value="+968" >Oman (+968)</option>
                                            <option value="+92" >Pakistan (+92)</option>
                                            <option value="+680" >Palau (+680)</option>
                                            <option value="+507" >Panama (+507)</option>
                                            <option value="+675" >Papua New Guinea (+675)</option>
                                            <option value="+595" >Paraguay (+595)</option>
                                            <option value="+51" >Peru (+51)</option>
                                            <option value="+63" >Philippines (+63)</option>
                                            <option value="+48" >Poland (+48)</option>
                                            <option value="+351" >Portugal (+351)</option>
                                            <option value="+1787" >Puerto Rico (+1787)</option>
                                            <option value="+974" >Qatar (+974)</option>
                                            <option value="+262" >Reunion (+262)</option>
                                            <option value="+40" >Romania (+40)</option>
                                            <option value="+7" >Russia (+7)</option>
                                            <option value="+250" >Rwanda (+250)</option>
                                            <option value="+378" >San Marino (+378)</option>
                                            <option value="+239" >Sao Tome &amp;amp; Principe (+239)</option>
                                            <option value="+966" >Saudi Arabia (+966)</option>
                                            <option value="+221" >Senegal (+221)</option>
                                            <option value="+381" >Serbia (+381)</option>
                                            <option value="+248" >Seychelles (+248)</option>
                                            <option value="+232" >Sierra Leone (+232)</option>
                                            <option value="+65" >Singapore (+65)</option>
                                            <option value="+421" >Slovak Republic (+421)</option>
                                            <option value="+386" >Slovenia (+386)</option>
                                            <option value="+677" >Solomon Islands (+677)</option>
                                            <option value="+252" >Somalia (+252)</option>
                                            <option value="+27" >South Africa (+27)</option>
                                            <option value="+34" >Spain (+34)</option>
                                            <option value="+94" >Sri Lanka (+94)</option>
                                            <option value="+290" >St. Helena (+290)</option>
                                            <option value="+1869" >St. Kitts (+1869)</option>
                                            <option value="+1758" >St. Lucia (+1758)</option>
                                            <option value="+597" >Suriname (+597)</option>
                                            <option value="+249" >Sudan (+249)</option>
                                            <option value="+268" >Swaziland (+268)</option>
                                            <option value="+46" >Sweden (+46)</option>
                                            <option value="+41" >Switzerland (+41)</option>
                                            <option value="+963" >Syria (+963)</option>
                                            <option value="+886" >Taiwan (+886)</option>
                                            <option value="+255" >Tanzania (+255)</option>
                                            <option value="+992" >Tajikistan (+992)</option>
                                            <option value="+66" >Thailand (+66)</option>
                                            <option value="+228" >Togo (+228)</option>
                                            <option value="+676" >Tonga (+676)</option>
                                            <option value="+1868" >Trinidad &amp;amp; Tobago (+1868)</option>
                                            <option value="+216" >Tunisia (+216)</option>
                                            <option value="+90" >Turkey (+90)</option>
                                            <option value="+993" >Turkmenistan (+993)</option>
                                            <option value="+1649" >Turks &amp;amp; Caicos Islands (+1649)</option>
                                            <option value="+688" >Tuvalu (+688)</option>
                                            <option value="+256" >Uganda (+256)</option>
                                            <option value="+380" >Ukraine (+380)</option>
                                            <option value="+971" >United Arab Emirates (+971)</option>
                                            <option value="+598" >Uruguay (+598)</option>
                                            <option value="+998" >Uzbekistan (+998)</option>
                                            <option value="+678" >Vanuatu (+678)</option>
                                            <option value="+379" >Vatican City (+379)</option>
                                            <option value="+58" >Venezuela (+58)</option>
                                            <option value="+84" >Vietnam (+84)</option>
                                            <option value="+1" >Virgin Islands - British (+1)</option>
                                            <option value="+1" >Virgin Islands - US (+1)</option>
                                            <option value="+681" >Wallis &amp;amp; Futuna (+681)</option>
                                            <option value="+969" >Yemen (North)(+969)</option>
                                            <option value="+967" >Yemen (South)(+967)</option>
                                            <option value="+260" >Zambia (+260)</option>
                                            <option value="+263" >Zimbabwe (+263)</option>
                                    </select>
            </div>
            <div class="col-8 h-100 pl-4">
                <input type="tel" name="mobile" class="register-mobile-form-group__input">
            </div>
        </div>
    </div>

    </div>
    </div>

            <div class="position-relative form-group mt-28 mb-0">
                <label class="form-group-label" for="password">Password:</label>
                <input type="password" name="password" class="form-control " id="password" aria-describedby="passwordHelp">

                <div class="password-input-visibility cursor-pointer size-24">
                    <svg width="24px" height="24px" class="icons-eye-slash text-gray-400 d-none" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.53 9.47l-5.06 5.06a3.576 3.576 0 115.06-5.06z"/>
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.82 5.77C16.07 4.45 14.07 3.73 12 3.73c-3.53 0-6.82 2.08-9.11 5.68-.9 1.41-.9 3.78 0 5.19.79 1.24 1.71 2.31 2.71 3.17M8.42 19.53c1.14.48 2.35.74 3.58.74 3.53 0 6.82-2.08 9.11-5.68.9-1.41.9-3.78 0-5.19-.33-.52-.69-1.01-1.06-1.47M15.51 12.7a3.565 3.565 0 01-2.82 2.82M9.47 14.53L2 22M22 2l-7.47 7.47"/>
</svg>                    <svg width="24px" height="24px" class="icons-eye text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15.58 12c0 1.98-1.6 3.58-3.58 3.58S8.42 13.98 8.42 12s1.6-3.58 3.58-3.58 3.58 1.6 3.58 3.58z"/>
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 20.27c3.53 0 6.82-2.08 9.11-5.68.9-1.41.9-3.78 0-5.19-2.29-3.6-5.58-5.68-9.11-5.68-3.53 0-6.82 2.08-9.11 5.68-.9 1.41-.9 3.78 0 5.19 2.29 3.6 5.58 5.68 9.11 5.68z"/>
</svg>                </div>

                            </div>

            
            <div class="text-right mt-12">
                <a href="forget-password.html" target="_blank" class="font-12 text-dark">Forgot your password?</a>
            </div>

            <button type="button" class="js-submit-form-btn btn btn-primary btn-lg btn-block mt-12">Login</button>
            
            
                  <div class="text-center mt-20 mb-20">
                        <span class="badge text-secondary d-inline-flex align-items-center justify-content-center">Login as</span>
                    </div>
                      
            <div class="form-group text-center ml-5">
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-8" onclick="$('#email').val('student@demo.com'); $('#password').val('student');">Student</button>

                <button type="button" class="btn btn-sm btn-outline-secondary rounded-8" onclick="$('#email').val('instructor@demo.com'); $('#password').val('instructor');">Instructor</button>

                <button type="button" class="btn btn-sm btn-outline-secondary rounded-8" onclick="$('#email').val('organization@demo.com'); $('#password').val('organization');">Organization</button>
              </div>
            
            
        </div>
    </form>

    
    <div class="d-flex-center flex-column text-center mt-24">
        <div class="font-12 text-gray-500">or continue with</div>

        <div class="d-flex-center gap-20">
            
                    </div>

        <div class="font-14 text-gray-500 mt-32">Don&#039;t have an account?</div>

        <a href="register.html" class="font-14 font-weight-bold mt-8 text-dark">Sign Up</a>
    </div>

                            </div>

                            <div class="col-12 col-lg-6 d-none d-lg-block">
                                <div class="auth-slider-container w-100 rounded-16 bg-gray-100"  style="background-image: url('store/1/themes/general/authentication_background.svg')" >
            <div class="position-relative h-100 w-100">
            <div class="swiper-container js-make-swiper auth-theme-slider pb-0 h-100"
                 data-item="auth-theme-slider"
                 data-autoplay="true"
                 data-loop="true"
                 data-pagination="auth-theme-slider-pagination"
            >
                <div class="swiper-wrapper py-0 ">
                                            <div class="swiper-slide">
                            <div class="d-flex-center flex-column text-center h-90 p-16">
                                                                    <div class="auth-slider-image d-flex-center">
                                        <img src="store/1/themes/general/authentication_slide1.png" alt="image" class="img-fluid">
                                    </div>
                                
                                                                    <h4 class="font-16 mt-16">Instant Certificate Access</h4>
                                
                                                                    <div class="font-14 mt-8 text-gray-500">Download certificates right after completion</div>
                                
                            </div>
                        </div>
                                            <div class="swiper-slide">
                            <div class="d-flex-center flex-column text-center h-90 p-16">
                                                                    <div class="auth-slider-image d-flex-center">
                                        <img src="store/1/themes/general/authentication_slide2.png" alt="image" class="img-fluid">
                                    </div>
                                
                                                                    <h4 class="font-16 mt-16">Affordable Quality Education</h4>
                                
                                                                    <div class="font-14 mt-8 text-gray-500">High-value courses at accessible prices</div>
                                
                            </div>
                        </div>
                                            <div class="swiper-slide">
                            <div class="d-flex-center flex-column text-center h-90 p-16">
                                                                    <div class="auth-slider-image d-flex-center">
                                        <img src="store/1/themes/general/authentication_slide3.png" alt="image" class="img-fluid">
                                    </div>
                                
                                                                    <h4 class="font-16 mt-16">Advance Your Career</h4>
                                
                                                                    <div class="font-14 mt-8 text-gray-500">Build your resume with proven expertise</div>
                                
                            </div>
                        </div>
                                    </div>

                <div class="swiper-pagination auth-theme-slider-pagination"></div>
            </div>
        </div>
    </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


            <div id="appFooterArea">
            <div class="theme-footer-1 position-relative has-newsletter">
        <div class="theme-footer-1__section position-relative">
            <div class="theme-footer-1__section-bg-wrapper light-only" style="background-color: var(--secondary); background-image: url(store/themes/footers/2/footer_background_7gn.png); "></div>
            <div class="theme-footer-1__section-bg-wrapper dark-only" style="background-color: var(--secondary); background-image: url(store/themes/footers/2/footer_background_7gn.png); "></div>


            
                            <div class="theme-footer-1__newsletter">
    <div class="container position-relative">
        <div class="theme-footer-1__newsletter-mask"></div>

        <div class="position-relative z-index-2 bg-white p-16 rounded-24">
            <div class="row align-items-center">
                <div class="col-12 col-lg-6">
                    <div class="">
                        <div class="d-flex align-items-center gap-4">
                                                            <h4 class="font-20">Subscribe to Our Newsletter</h4>
                            
                                                            <div class="theme-footer-1__newsletter-emoji">
                                    <img src="store/themes/footers/2/happy_emoji_zoa.svg" alt="emoji" class="img-fluid" width="20px" height="20px">
                                </div>
                                                    </div>

                                                    <div class="mt-8 font-14 text-gray-500">Receive expert insights, course updates, and learning resources directly in your inbox and get notified</div>
                        
                    </div>
                </div>

                <div class="col-12 col-lg-6 mt-16 mt-lg-0 d-flex justify-content-end">
                    <div class="js-newsletter-form newsletter-form d-flex align-items-center justify-content-between p-12 rounded-12 border-gray-200">
                        <div class="form-group mb-0 flex-1">
                            <div class="d-flex align-items-center gap-8 px-12 flex-1">
                                <svg width="24px" height="24px" class="icons text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="1.5" d="M17 20.5H7c-3 0-5-1.5-5-5v-7c0-3.5 2-5 5-5h10c3 0 5 1.5 5 5v7c0 3.5-2 5-5 5z"/>
  <path stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="1.5" d="M17 9l-3.13 2.5c-1.03.82-2.72.82-3.75 0L7 9"/>
</svg>                                <input type="email" name="newsletter_email" class="js-ajax-newsletter_email flex-1" placeholder="Enter your email address here">
                            </div>

                            <div class="invalid-feedback d-block position-absolute position-bottom-0"></div>
                        </div>

                        <button type="button" class="js-submit-newsletter-btn btn btn-primary btn-lg text-white">Join</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
            
            <div class="position-relative z-index-2">

                <div class="container position-relative">
                    <div class="row">
                        <div class="col-12 col-lg-5">
                                                            <div class="d-inline-flex-center gap-8 border-2 border-white rounded-32 bg-white-10 text-white px-16 py-12">
                                                                            <div class="size-24">
                                            <img src="store/themes/footers/2/power_emoji_42t.svg" alt="footer cta btn icon" class="img-fluid" width="24px" height="24px">
                                        </div>
                                    
                                                                            <span class="">Let’s get started now!</span>
                                                                    </div>

                                                                    <h3 class="mt-16 font-44 text-white mr-0 mr-lg-48">Take the First Step Towards Mastery!</h3>
                                
                                                                    <a href="classes.html" class="btn-flip-effect btn btn-xlg btn-primary gap-8 mt-32" data-text="Enroll on Courses">
                                                                                    <svg width="24px" height="24px" class="icons" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
  <path d="M18.38 12.84v4.93c0 1.27-.99 2.63-2.18 3.03l-3.19 1.06c-.56.19-1.47.19-2.02 0L7.8 20.8c-1.2-.4-2.18-1.76-2.18-3.03l.01-4.93 4.42 2.88c1.08.71 2.86.71 3.94 0l4.39-2.88z" opacity=".4"/>
  <path d="M19.98 6.46l-5.99-3.93c-1.08-.71-2.86-.71-3.94 0L4.03 6.46c-1.93 1.25-1.93 4.08 0 5.34l1.6 1.04 4.42 2.88c1.08.71 2.86.71 3.94 0l4.39-2.88 1.37-.9V15c0 .41.34.75.75.75s.75-.34.75-.75v-4.92c.4-1.29-.01-2.79-1.27-3.62z"/>
</svg>                                        
                                        <span class="btn-flip-effect__text">Enroll on Courses</span>
                                    </a>
                                                                                    </div>

                        <div class="col-6 col-lg-2 mt-32 mt-lg-0">
                                                            <h4 class="font-16 text-white">Additional Links</h4>
                            
                                                                                                                                        <a href="login.html" target="_blank" class="d-block font-16 text-white opacity-70 mt-16">
                                            <span class="">Login</span>
                                        </a>
                                                                                                                                                <a href="register.html" target="_blank" class="d-block font-16 text-white opacity-70 mt-12">
                                            <span class="">Register</span>
                                        </a>
                                                                                                                                                <a href="contact.html" target="_blank" class="d-block font-16 text-white opacity-70 mt-12">
                                            <span class="">Contact</span>
                                        </a>
                                                                                                                                                <a href="certificate_validation.html" target="_blank" class="d-block font-16 text-white opacity-70 mt-12">
                                            <span class="">Certificate Validation</span>
                                        </a>
                                                                                                                                                <a href="login.html" target="_blank" class="d-block font-16 text-white opacity-70 mt-12">
                                            <span class="">Become Instructor</span>
                                        </a>
                                                                                                                                                <a href="pages/about.html" target="_blank" class="d-block font-16 text-white opacity-70 mt-12">
                                            <span class="">About</span>
                                        </a>
                                                                                                                                                <a href="pages/terms.html" target="_blank" class="d-block font-16 text-white opacity-70 mt-12">
                                            <span class="">Terms and Policies</span>
                                        </a>
                                                                                                                                                                                            </div>

                        <div class="col-6 col-lg-2 mt-32 mt-lg-0">
                                                            <h4 class="font-16 text-white">Popular Categories</h4>
                            
                                                                                                                                        <a href="categories/Development.html" target="_blank" class="d-block font-16 text-white opacity-70 mt-16">
                                            <span class="">Development</span>
                                        </a>
                                                                                                                                                <a href="categories/Business.html" target="_blank" class="d-block font-16 text-white opacity-70 mt-12">
                                            <span class="">Business</span>
                                        </a>
                                                                                                                                                <a href="categories/Marketing.html" target="_blank" class="d-block font-16 text-white opacity-70 mt-12">
                                            <span class="">Marketing</span>
                                        </a>
                                                                                                                                                <a href="categories/Lifestyles.html" target="_blank" class="d-block font-16 text-white opacity-70 mt-12">
                                            <span class="">Lifestyle</span>
                                        </a>
                                                                                                                                                <a href="categories/Health-and-Fitness.html" target="_blank" class="d-block font-16 text-white opacity-70 mt-12">
                                            <span class="">Health</span>
                                        </a>
                                                                                                                                                <a href="categories/Academics.html" target="_blank" class="d-block font-16 text-white opacity-70 mt-12">
                                            <span class="">Academics</span>
                                        </a>
                                                                                                                                                <a href="categories/Design.html" target="_blank" class="d-block font-16 text-white opacity-70 mt-12">
                                            <span class="">Design</span>
                                        </a>
                                                                                                                                                                                            </div>

                        <div class="col-12 col-lg-3 mt-32 mt-lg-0">
                                                                                                <h4 class="font-16 text-white">Contact US</h4>
                                
                                                                    <div class="d-flex align-items-start gap-8 mt-20">
                                        <div class="size-24">
                                            <svg width="24px" height="24px" class="text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-width="1.5" d="M12 13.43a3.12 3.12 0 100-6.24 3.12 3.12 0 000 6.24z"/>
  <path stroke-width="1.5" d="M3.62 8.49c1.97-8.66 14.8-8.65 16.76.01 1.15 5.08-2.01 9.38-4.78 12.04a5.193 5.193 0 01-7.21 0c-2.76-2.66-5.92-6.97-4.77-12.05z"/>
</svg>                                        </div>
                                        <span class="font-16 text-white opacity-70">1234 Sunset Blvd, Suite 567 Los Angeles, CA 90026 United States</span>
                                    </div>
                                
                                                                    <div class="d-flex align-items-start gap-8 mt-16">
                                        <div class="size-24">
                                            <svg width="24px" height="24px" class="text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-miterlimit="10" stroke-width="1.5" d="M21.97 18.33c0 .36-.08.73-.25 1.09-.17.36-.39.7-.68 1.02-.49.54-1.03.93-1.64 1.18-.6.25-1.25.38-1.95.38-1.02 0-2.11-.24-3.26-.73s-2.3-1.15-3.44-1.98a28.75 28.75 0 01-3.28-2.8 28.414 28.414 0 01-2.79-3.27c-.82-1.14-1.48-2.28-1.96-3.41C2.24 8.67 2 7.58 2 6.54c0-.68.12-1.33.36-1.93.24-.61.62-1.17 1.15-1.67C4.15 2.31 4.85 2 5.59 2c.28 0 .56.06.81.18.26.12.49.3.67.56l2.32 3.27c.18.25.31.48.4.7.09.21.14.42.14.61 0 .24-.07.48-.21.71-.13.23-.32.47-.56.71l-.76.79c-.11.11-.16.24-.16.4 0 .08.01.15.03.23.03.08.06.14.08.2.18.33.49.76.93 1.28.45.52.93 1.05 1.45 1.58.54.53 1.06 1.02 1.59 1.47.52.44.95.74 1.29.92.05.02.11.05.18.08.08.03.16.04.25.04.17 0 .3-.06.41-.17l.76-.75c.25-.25.49-.44.72-.56.23-.14.46-.21.71-.21.19 0 .39.04.61.13.22.09.45.22.7.39l3.31 2.35c.26.18.44.39.55.64.1.25.16.5.16.78z"/>
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18.5 9c0-.6-.47-1.52-1.17-2.27-.64-.69-1.49-1.23-2.33-1.23M22 9c0-3.87-3.13-7-7-7"/>
</svg>                                        </div>
                                        <span class="font-16 text-white opacity-70">+1 (323) 555-9876</span>
                                    </div>
                                
                                                                    <div class="d-flex align-items-start gap-8 mt-16">
                                        <div class="size-24">
                                            <svg width="24px" height="24px" class="text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 18 22" stroke="currentColor" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 6v10c0 4-1 5-5 5H6c-4 0-5-1-5-5V6c0-4 1-5 5-5h6c4 0 5 1 5 5zM11 4.5H7"/>
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 18.1A1.55 1.55 0 109 15a1.55 1.55 0 000 3.1z"/>
</svg>                                        </div>
                                        <span class="font-16 text-white opacity-70">+1 (213) 555-4321</span>
                                    </div>
                                
                                                                    <div class="d-flex align-items-start gap-8 mt-16">
                                        <div class="size-24">
                                            <svg width="24px" height="24px" class="text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="1.5" d="M17 20.5H7c-3 0-5-1.5-5-5v-7c0-3.5 2-5 5-5h10c3 0 5 1.5 5 5v7c0 3.5-2 5-5 5z"/>
  <path stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="1.5" d="M17 9l-3.13 2.5c-1.03.82-2.72.82-3.75 0L7 9"/>
</svg>                                        </div>
                                        <span class="font-16 text-white opacity-70">mail@lms.rocket-soft.org</span>
                                    </div>
                                                                                    </div>


                    </div>
                </div>

                <div class="theme-footer-1__bottom-section-divider"></div>

                <div class="container d-flex flex-column flex-lg-row align-items-lg-center justify-content-lg-between py-24 px-16 gap-16">
                                            <div class="font-14 text-white opacity-70">© 2025 Rocket Soft. All Rights Reserved. Empowering Learning Worldwide.</div>
                    
                    <div class="d-flex align-items-center justify-content-center gap-16 gap-lg-24">
                                                    
                                                                                                                                        <a href="https://www.instagram.com/" target="_blank" rel="nofollow" title="Instagram" class="d-flex-center size-24">
                                            <img src="store/1/default_images/social/instagram.svg" alt="Instagram" class="img-cover">
                                        </a>
                                                                                                                                                                                                            <a href="https://web.whatsapp.com/" target="_blank" rel="nofollow" title="Whatsapp" class="d-flex-center size-24">
                                            <img src="store/1/default_images/social/whatsapp.svg" alt="Whatsapp" class="img-cover">
                                        </a>
                                                                                                                                                                                                            <a href="https://twitter.com/" target="_blank" rel="nofollow" title="Messenger" class="d-flex-center size-24">
                                            <img src="store/1/default_images/social/messenger.svg" alt="Messenger" class="img-cover">
                                        </a>
                                                                                                                                                                                                            <a href="https://www.facebook.com/" target="_blank" rel="nofollow" title="Facebook" class="d-flex-center size-24">
                                            <img src="store/1/default_images/social/facebook.svg" alt="Facebook" class="img-cover">
                                        </a>
                                                                                                                                            </div>

                </div>
            </div>
        </div>
    </div>
        </div>
    
    
    
    
    <div class="cart-drawer no-footer bg-white py-16">
    <div class="d-flex align-items-center pb-16 border-bottom-gray-bg px-16">
        <button type="button" class="js-cart-drawer-close d-flex btn-transparent">
            <svg width="25px" height="25px" class="icons text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-miterlimit="10" stroke-width="1.5" d="M14.43 5.93L20.5 12l-6.07 6.07M3.5 12h16.83"/>
</svg>        </button>

        <span class="font-14 font-weight-bold ml-8">Cart</span>
    </div>

    <div class="cart-drawer__body pb-32" data-simplebar >

    </div>

    <div class="cart-drawer__footer pt-16 border-top-gray-bg d-none px-16">
        <div class="d-flex align-items-center justify-content-between">
            <span class="text-gray-500">Subtotal</span>
            <span class="js-side-cart-subtotal text-dark font-weight-bold"></span>
        </div>

        <div class="mt-12">
            <a href="login.html" class="btn btn-outline-primary btn-block">View Cart</a>
        </div>
    </div>
</div>
<div class="cart-drawer-mask"></div>

</div>

<!-- Template JS File -->
<script>
    var siteDomain = 'index.html';
    var deleteAlertTitle = 'Are you sure?';
    var deleteAlertHint = 'This action cannot be undone!';
    var deleteAlertConfirm = 'Delete';
    var deleteAlertCancel = 'Cancel';
    var deleteAlertSuccess = 'Success';
    var deleteAlertFail = 'Failed';
    var deleteAlertFailHint = 'Failed to delete item!';
    var deleteAlertSuccessHint = 'Item deleted successfully.';
    var forbiddenRequestToastTitleLang = 'Forbidden Request';
    var forbiddenRequestToastMsgLang = 'You do not have access to this content.';
    var priceInvalidHintLang = 'Invalid price. Only numbers and decimals are accepted.';
    var clearLang = 'clear';
    var requestSuccessLang = 'Request completed successfully!';
    var saveSuccessLang = 'Item added successfully.';
    var requestFailedLang = 'Request Failed';
    var oopsLang = 'Oops...';
    var somethingWentWrongLang = 'Something went wrong...';
    var loadingDataPleaseWaitLang = 'Loading data. Please wait...';
    var deleteRequestLang = 'Content Deletion Request';
    var deleteRequestTitleLang = 'Are you sure to delete content?';
    var deleteRequestDescriptionLang = 'If you wish to remove your content, please provide a clear and detailed explanation.';
    var requestDetailsLang = 'Request Details';
    var sendRequestLang = 'Submit Request';
    var closeLang = 'Close';
    var generatedContentLang = 'Generated Content';
    var copyLang = 'Copy';
    var doneLang = 'Completed';
    var jsCurrentCurrency = '$';
    var defaultLocale = 'en';
    var appLocale = 'en';
    var dangerCloseIcon = `<svg width="24" height="24" class="icons text-danger" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 12h12M12 18V6"/>
</svg>`;
    var directSendIcon = `<svg width="24" height="24" class="icons text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9V2l-2 2M12 2l2 2M1.98 13h4.41c.38 0 .72.21.89.55l1.17 2.34A2 2 0 0010.24 17h3.53a2 2 0 001.79-1.11l1.17-2.34a1 1 0 01.89-.55h4.36"/>
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 5.13c-3.54.52-5 2.6-5 6.87v3c0 5 2 7 7 7h6c5 0 7-2 7-7v-3c0-4.27-1.46-6.35-5-6.87"/>
</svg>`;
    var closeIcon = `<svg width="25px" height="25px" class="close-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 12h12M12 18V6"/>
</svg>`;
    var bulDangerIcon = `<svg width="32px" height="32px" class="icons text-white" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
  <path d="M21.76 15.92L15.36 4.4C14.5 2.85 13.31 2 12 2s-2.5.85-3.36 2.4l-6.4 11.52c-.81 1.47-.9 2.88-.25 3.99.65 1.11 1.93 1.72 3.61 1.72h12.8c1.68 0 2.96-.61 3.61-1.72.65-1.11.56-2.53-.25-3.99z" opacity=".4"/>
  <path d="M12 14.75c-.41 0-.75-.34-.75-.75V9c0-.41.34-.75.75-.75s.75.34.75.75v5c0 .41-.34.75-.75.75zM12 18c-.06 0-.13-.01-.2-.02a.636.636 0 01-.18-.06.757.757 0 01-.18-.09l-.15-.12c-.18-.19-.29-.45-.29-.71 0-.26.11-.52.29-.71l.15-.12c.06-.04.12-.07.18-.09.06-.03.12-.05.18-.06.13-.03.27-.03.39 0 .07.01.13.03.19.06.06.02.12.05.18.09l.15.12c.18.19.29.45.29.71 0 .26-.11.52-.29.71l-.15.12c-.06.04-.12.07-.18.09-.06.03-.12.05-.19.06-.06.01-.13.02-.19.02z"/>
</svg>`;
    var defaultAvatarPath = "store/1/default_images/default_profile.jpg";
    var themeColorsMode = {"light":{"primary":"#E31E24","primary_saturated":"#67a9ff","secondary":"#0e2145","accent":"#fe6257","success":"#3fcd82","info":"#67a9ff","warning":"#ffa200","danger":"#f63c3c","dark":"#121f3e","black":"#000000","white":"#ffffff","gray_100":"#fafcff","gray_200":"#f0f4f9","gray_300":"#e9edf3","gray_400":"#cdd5e2","gray_500":"#97a7bf","gray":"#f5f8f9","section_bg":"#eaf0f3"},"dark":{"primary":"#3e93ff","primary_saturated":"#8dbeff","secondary":"#2658b7","accent":"#ff8077","success":"#5ade98","info":"#8dbeff","warning":"#ffb32d","danger":"#fe6363","dark":"#aab8c5","black":"#e1eaf6","white":"#1e1f26","gray_100":"#272832","gray_200":"#30313e","gray_300":"#3e404e","gray_400":"#5d5f72","gray_500":"#8391a2","gray":"#17181e","section_bg":"#2d323a"}};
</script>


<script type="text/javascript" src="assets/design_1/js/app.min.js"></script>
<script type="text/javascript" src="assets/default/vendors/simplebar/simplebar.min.js"></script>
<script defer src="assets/design_1/js/parts/content_delete.min.js"></script>





    <script src="assets/default/vendors/swiper/swiper-bundle.min.js"></script>
    <script src="assets/design_1/js/parts/swiper_slider.min.js"></script>

    <script src="assets/design_1/js/parts/auth_theme_1.min.js"></script>


<script>

    
    
</script>

<script src="assets/design_1/js/parts/general.min.js"></script>

</body>

<!-- Mirrored from lms.rocket-soft.org/login by HTTrack Website Copier/3.x [XR&CO'2014], Tue, 25 Aug 2026 16:22:22 GMT -->
</html>

