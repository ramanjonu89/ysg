<div class="sub-cat-main row no-gutters">
    <div class="col-12">
        <div class="sub-cat-content">
            <div class="sub-cat-list">
                <div class="card-columns">
                    @foreach ($cats['subCats'] as $subCategory)
                            <div class="card">
                                <ul class="sub-cat-items">
                                    <li class="sub-cat-name">
                                        <a 
                                            href="{{ $subCategory->getSubCategoryLink() }}">
                                            {{ $subCategory->getTranslationNameAttribute() }}
                                        </a>
                                    </li>
                                    @foreach ($cats['subsubCats'][$subCategory->id] as $subSubCategory)
                                            <li>
                                                <a href="{{ $subSubCategory->getSubSubCategoryLink() }}">
                                                    {{ $subSubCategory->getTranslationNameAttribute() }}
                                                </a>
                                            </li>
                                    @endforeach
                                </ul>
                            </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

