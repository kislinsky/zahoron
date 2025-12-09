<div class="sidebar_account">
    <div class="item_page_sidebar">
        <a href='{{ route('home') }}' class="title_page_sidebar">
            <img class='icon_page' src="{{ asset('storage/uploads/Icon_sidebar_2.svg') }}" alt=""> 
            Главная
        </a>
    </div>

    @php
        $pages = organizationPages();
        $reviewDetails = $pages['review_details'] ?? ['organization_reviews' => 0, 'product_comments' => 0];
    @endphp

    @foreach($pages as $key => $children_pages)
        @if($key !== 'review_details' && isset($children_pages[2]))
            <div class="item_page_sidebar">
                <div class="title_page_sidebar">
                    <img class='icon_page' src="{{ asset($children_pages[1]) }}" alt=""> 
                    {{ $children_pages[0] }}
                    
                    {{-- Бейдж с количеством уведомлений для основного пункта --}}
                    @if(isset($children_pages['notification_count']) && $children_pages['notification_count'] > 0)
                    <span class="notification-badge">
                        {{ $children_pages['notification_count'] > 99 ? '99+' : $children_pages['notification_count'] }}
                    </span>
                    @endif
                    
                    <img class='open_children_pages_sidebar img_light_theme' src="{{ asset('storage/uploads/Arrow_sidebar.svg') }}" alt="">
                    <img class='open_children_pages_sidebar img_black_theme' src="{{ asset('storage/uploads/Arrow_right_black.svg') }}" alt="">
                </div>
                
                <div class="pages_children_sidebar">
                    @foreach($children_pages[2] as $children_page)
                        <a href="<?php 
                            if(isset($children_page[2])){ 
                                echo route($children_page[1], $children_page[2]);
                            } else { 
                                echo route($children_page[1]);
                            }
                        ?>" class="li_children_page_sidebar {{ activateLink($children_page[1], 'li_children_page_sidebar_active') }}">
                            {{ $children_page[0] }}
                            
                            {{-- Бейдж для подпунктов (особенно для отзывов) --}}
                            @if(isset($children_page[3]) && $children_page[3] > 0)
                            <span class="notification-badge-small">
                                {{ $children_page[3] > 99 ? '99+' : $children_page[3] }}
                            </span>
                            @endif
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    @endforeach
</div>