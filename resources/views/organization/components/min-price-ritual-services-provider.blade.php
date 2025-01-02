@if($categories_organization!=null && count($categories_organization)>0)
    @foreach ($categories_organization as $category_organization)
        @php 
            $categories_children=childrenCategoryOrganizationProvider($organization,$category_organization);
        @endphp
        <div class="block_content_organization_single">
            <div class="title_li title_price_organization_ritual_services">Минимальная стоимость {{$category_organization->title}} "{{ $organization->title }}"</div>
            @if($categories_children!=null && count($categories_children)>0)
                <div class="scroll_block">
                    <table class="block_table_price_organization_ritual_services">
                        <thead>
                            <tr>
                                @foreach($categories_children as $category_children)
                                    <th class='text_black'>{{$category_children->title}}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                @foreach($categories_children as $category_children)
                                @php 
                                    $price_category=$category_children->priceCategoryOrganization($organization);
                                @endphp
                                    @if($price_category!=null)
                                        <td class='title_blue'>от {{$price_category}} ₽</td>
                                    @else
                                        <td class='title_blue'></td>
                                    @endif 
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>                
            @endif
        </div>      
    @endforeach
    
@endif