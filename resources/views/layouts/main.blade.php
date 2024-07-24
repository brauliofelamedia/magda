@include('parts.header-pink')

    <div id="loader">
        <img src="{{asset('assets/img/top-loader.png')}}" class="top">
        <div class="circle"></div>
        <div class="logo">
            <img src="{{asset('assets/img/logo.png')}}" alt="Tu talento finder">
        </div>
        <img src="{{asset('assets/img/bottom-loader.png')}}" class="bottom">
    </div>
    
    @yield('content')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="{{asset('assets/js/main.js')}}"></script>
    @stack('js')
    <script>
        $(document).ready(function(){
            $(window).scroll(function() {    
                var scroll = $(window).scrollTop();
                if (scroll >= 100) {
                    $("#navbar").addClass("fixed");
                } else {
                    $("#navbar").removeClass("fixed");
                }
            });
        });
    </script>
</body>
</html>


