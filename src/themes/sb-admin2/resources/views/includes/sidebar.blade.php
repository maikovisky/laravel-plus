 
<div class="navbar-default sidebar" role="navigation">
    <!-- sidebar nav -->
    <ul class="nav" id="side-menu">
        @if (Auth::check())
        <li><a href="{{URL::to('user')}}">{{Lang::get('menu.Users')}}</a></li>
        @else
        <li><a href="{{URL::asset("auth/login")}}">{{Lang::get('menu.Login')}}</a></li>
        
        @endif
        <li><a href="{{URL::asset("contact")}}">{{Lang::get('menu.Contact')}}</a></li>
        <li><a href="{{URL::asset("about")}}">{{Lang::get('menu.About')}}</a></li>
    </ul>
</div>


