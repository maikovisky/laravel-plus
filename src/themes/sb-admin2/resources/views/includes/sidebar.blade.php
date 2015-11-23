 
<div class="navbar-default sidebar" role="navigation">
    <!-- sidebar nav -->
    <ul class="nav" id="side-menu">
        @if (Auth::check())
        <li><a href="{{URL::to('/')}}">{{Lang::get('menu.FleetPanel')}}</a></li>
        <li><a href="{{URL::to('company')}}">{{Lang::get('menu.Company')}}</a></li>
        <li><a href="{{URL::to('user')}}">{{Lang::get('menu.Users')}}</a></li>
        <li><a href="{{URL::to('modelmonitor')}}">{{Lang::get('menu.Monitors')}}</a></li>
        <li><a href="{{URL::to('modelvehicle')}}">{{Lang::get('menu.Vehicles')}}</a></li>                
        <li><a href="{{URL::to('fleets')}}">{{Lang::get('menu.Fleet')}}</a></li>          
        <li>
            <a href="#">
                {{Lang::get('menu.Reports')}}
                <span class="fa arrow"></span>
            </a>
            <ul class="nav nav-second-level"> 
                <li><a href="{{URL::to('report')}}">{{Lang::get('menu.Vehicle')}}</a></li>
                <li><a href="{{URL::to('report')}}">{{Lang::get('menu.Tires')}}</a></li>
                <li><a href="{{URL::to('report')}}">{{Lang::get('menu.Maintenance')}}</a></li>
                <li><a href="{{URL::to('report')}}">{{Lang::get('menu.Efficiency')}}</a></li>
            </ul>           
        </li>
        <li>
            <a href="#">
                {{Lang::get('menu.AuxiliarTables')}}
                <span class="fa arrow"></span>
            </a>
            <ul class="nav nav-second-level"> 
                <li><a href="{{URL::to('company')}}">{{Lang::get('menu.PlanType')}}</a></li>
                <li><a href="{{URL::to('person')}}">{{Lang::get('menu.UserProfile')}}</a></li>
                <li><a href="{{URL::to('modelvehicle')}}">{{Lang::get('menu.ModelVehicle')}}</a></li>
                <li><a href="{{URL::to('modelmonitor')}}">{{Lang::get('menu.ModelMonitor')}}</a></li>
                <li><a href="{{URL::to('modelsensor')}}">{{Lang::get('menu.ModelSensor')}}</a></li>
            </ul>
        </li>
        @else
        <li><a href="{{URL::asset("auth/login")}}">{{Lang::get('menu.Login')}}</a></li>
        
        @endif
        <li><a href="{{URL::asset("contact")}}">{{Lang::get('menu.Contact')}}</a></li>
        <li><a href="{{URL::asset("about")}}">{{Lang::get('menu.About')}}</a></li>
    </ul>
</div>


