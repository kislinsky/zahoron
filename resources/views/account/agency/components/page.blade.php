@include('header.header-agency')
<div class="account_container">
    <div class="flex_account">
        @include('account.agency.components.sidebar')
        <div class="container_content_account">
            <div class="container">
                <div class="title_middle">@yield('title')</div>
                @yield('content')
           </div>
        </div>
    </div>
</div>

@include('footer.footer')