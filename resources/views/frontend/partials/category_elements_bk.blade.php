@php

    $brands = array();

@endphp

<div class="sub-cat-main row no-gutters">

    <div class="col-12">

        <div class="sub-cat-content">

            <div class="sub-cat-list">

                <div class="card-columns">

                    @foreach ($category->subCategories as $subcategory)
                        @php
                            $counters = DB::select( DB::raw("select * from sub_categories where id in (SELECT subcategory_id FROM products a left join sellers b on a.user_id = b.user_id WHERE a.published = 1 and b.verification_status = 1 and a.subcategory_id = $subcategory->id) and status = 1") );
                           // echo "select * from sub_sub_categories where id in (SELECT subsubcategory_id FROM products a left join sellers b on a.user_id = b.user_id WHERE a.published = 1 and b.verification_status = 1 and a.subsubcategory_id = $subsubcategory->id) and status = 1";
                        @endphp
                        @foreach ($counters as $key => $counter)
                            @if ($subcategory->status == 1)
                                <div class="card">
                                    <ul class="sub-cat-items">
                                        <li class="sub-cat-name"><a
                                                href="{{ route('category.view', ['category_slug' => $category->slug,'subcategory_slug' => $subcategory->slug]) }}">{{ $subcategory->getTranslationNameAttribute() }}</a>
                                        </li>
                                        @foreach ($subcategory->subSubcategories as $subsubcategory)
                                            @php
                                                $counters = DB::select( DB::raw("select * from sub_sub_categories where id in (SELECT subsubcategory_id FROM products a left join sellers b on a.user_id = b.user_id WHERE a.published = 1 and b.verification_status = 1 and a.subsubcategory_id = $subsubcategory->id) and status = 1") );
                                               // echo "select * from sub_sub_categories where id in (SELECT subsubcategory_id FROM products a left join sellers b on a.user_id = b.user_id WHERE a.published = 1 and b.verification_status = 1 and a.subsubcategory_id = $subsubcategory->id) and status = 1";
                                            @endphp
                                            @foreach ($counters as $key => $counter)
                                                @if ($subsubcategory->status == 1)
                                                    <li>
                                                        <a href="{{ route('category.view',['category_slug' => $category->slug,'subcategory_slug' => $subcategory->slug, 'subsubcategory_slug' => $subsubcategory->slug]) }}">{{ $subsubcategory->getTranslationNameAttribute()  }}</a>
                                                    </li>
                                                @endif
                                            @endforeach
                                        @endforeach
                                    </ul>

                                </div>

                            @endif
                        @endforeach
                    @endforeach

                </div>

            </div>

        </div>

    </div>

</div>

