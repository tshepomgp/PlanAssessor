<nav class="navbar hidden-xs m-menu navbar-default">
   <div class="container-fluid no-pad">
        @widget('desktopNav')
	@widget('latestAnnouncementTicker')
    </div>
</nav>
    <nav class="navbar hidden-lg hidden-md hidden-sm m-menu navbar-light">
        <div class="nav-side-menu">
            <div class="brand-sacap">
                <i class="fa fa-bars fa-2x toggle-btn" data-toggle="collapse" data-target="#menu-content"></i>
                <a class="brand-name" href="/"><img src="{{ url('/android-icon-48x48.png') }}" alt="logo"></a>
                <a class="brand-action" target="_blank" href="https://sacap.site-ym.com/members/manage_profile.asp">RP LOGIN<i class="fa fa-shield"></i></a>
            </div>
            <div>
                <div class="header-top">
                    <ul class="social-icon2" style="padding-left: 20px">
                        <li><a href="https://www.linkedin.com/company/sacapsa" target="_blank" class="fa fa-linkedin" aria-hidden="true"> </a></li>
                        <li><a href="https://www.facebook.com/SACAPOfficialPage" target="_blank" class="fa fa-facebook" aria-hidden="true"> </a></li>
                        <li><a href="https://www.youtube.com/channel/UCPHfMxgQS24qZi6eAPxbJrA" target="_blank" class="fa fa-youtube" aria-hidden="true"> </a></li>
                        <li><a href="{{ url('/news') }}" class="fa fa-rss" aria-hidden="true"> </a></li>
                    </ul>
                    <div>
                        <i class="fa fa-search search-ico" style="font-size: x-large;" aria-hidden="true"></i>
                    </div>
                </div>
            </div>
            <div class="menu-list">
                <ul id="menu-content" class="menu-content collapse out">
                    @foreach(\App\Utils\Menu::getMenuItems() as $index => $item)
                        @if(empty($item->slug) === false && empty($hidden[$item->slug]) === false)
                            @continue;
                        @endif
                        <li data-toggle="collapse" data-target="#{{$item->target}}" class="collapsed">
                            <a href="#">
                                @if(empty($item->icon) === false) <i class="fa {{$item->icon}} fa-lg"></i> @endif
                                    {{ ucwords($item->label) }}
                                @if(empty($item->submenu) === false)  <span class="arrow"></span></a> @endif
                        </li>
                        @if(empty($item->submenu) === false)
                            <ul class="sub-menu collapse" id="{{$item->target}}">
                                @foreach($item->submenu as $index => $itemLevel2)
                                    @if(empty($itemLevel2->target) === true)
                                        <li>
                                            <a href="{{$itemLevel2->slug}}" @if(substr($itemLevel2->slug, 0, 4) === "http") target="_blank" @endif>
                                                {{ucwords($itemLevel2->label)}}
                                            </a>
                                        </li>
                                    @else
                                        <li data-toggle="collapse" data-target="#{{$itemLevel2->target}}" class="collapsed">
                                            <a href="#" @if(substr($itemLevel2->slug, 0, 4) === "http") target="_blank" @endif>
                                                {{ucwords($itemLevel2->label)}}
                                                <span class="arrow"></span>
                                            </a>
                                        </li>

                                        <ul class="sub-menu collapse" id="{{$itemLevel2->target}}">
                                            @if($itemLevel2->submenu)
                                                @foreach($itemLevel2->submenu as $index => $itemLevel3)
                                                    @if(empty($itemLevel3->target) === true)
                                                        <li data-toggle="collapse" data-target="#{{$itemLevel2->target}}" class="collapsed" style="margin-left:30px">
                                                            <a href="{{$itemLevel3->slug}}" @if(substr($itemLevel3->slug, 0, 4) === "http") target="_blank" @endif>
                                                                {{ucwords($itemLevel3->label)}}
                                                            </a>
                                                        </li>
                                                    @else
                                                        <li data-toggle="collapse" data-target="#{{$itemLevel3->target}}" class="collapsed" style="margin-left:30px">
                                                            <a href="#" @if(substr($itemLevel3->slug, 0, 4) === "http") target="_blank" @endif>
                                                                {{ucwords($itemLevel3->label)}}
                                                            </a>
                                                            <span class="arrow"></span>
                                                        </li>


                                                        <ul class="sub-menu collapse" id="{{$itemLevel3->target}}">
                                                            @if($itemLevel3->submenu)
                                                                @foreach($itemLevel3->submenu as $index => $itemLevel4)
                                                                    @if(empty($itemLevel4->target) === true)
                                                                        <li data-toggle="collapse" data-target="#{{$itemLevel3->target}}" class="collapsed" style="margin-left:60px">
                                                                            <a href="{{$itemLevel4->slug}}" @if(substr($itemLevel4->slug, 0, 4) === "http") target="_blank" @endif>
                                                                                {{ucwords($itemLevel4->label)}}
                                                                            </a>
                                                                        </li>
                                                                    @else
                                                                        <li data-toggle="collapse" data-target="#{{$itemLevel4->target}}" class="collapsed" style="margin-left:60px">
                                                                            <a href="{{$itemLevel4->slug}}" @if(substr($itemLevel4->slug, 0, 4) === "http") target="_blank" @endif>
                                                                                {{ucwords($itemLevel4->label)}}
                                                                            </a>
                                                                        </li>
                                                                    @endif
                                                                @endforeach
                                                            @endif
                                                        </ul>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </ul>
                                    @endif
                                @endforeach
                            </ul>
                        @endif
                    @endforeach
                </ul>
            </div>
        </div>
    </nav>
@if(empty($breadcrumb) === false)
    <div class="col-sm-12 no-pad breadcrumb @if(empty($greenHeader) === false) header-shape-about @endif">
        <div class="container"></div>
        <div class="@if(empty($greenHeader) === false) header-shape-about @else shape-header @endif">
            <div class="col-lg-8"><h3 class="shape-text">{{ $breadcrumb }}</h3></div>
            <div class="col-lg-4" style="padding-top: 10px">@yield('breadcrumb-quick-links')</div>
            <div class="header-shape"></div>
        </div>
    </div>
@endif
