<div class="block ca-brand-list">
    <div class="wrap container">
        <div class="row mt-5 mb-5">
            <div class="col-12 title text-center">
                <h1>{{__('Brands') }}</h1>
                <span>{{__('(Corporate Sales Store)') }}</span>
            </div>
        </div>
        <div class="row">
            @foreach($CABrandList as $group => $items)
                <div class="col-12 group-title mb-5">
                    <span>{{$group}}</span>
                </div>
                @foreach($items as $item)
                    <div class="col-6 group-item mb-5">
                        <a href="{{ route('products.css', ['brand_ids' => [$item->id]]) }}">
                            {{$item->name}}
                        </a>
                    </div>
                @endforeach
            @endforeach
        </div>
    </div>
</div>

@section('css')
    @parent

    <style type="text/css">
        .ca-brand-list .title h1{
            font-size: 48px;
            line-height: 1.46;
            text-decoration-color: rgb(82, 95, 127);
            text-decoration-line: underline;
            text-decoration-style: solid;
            text-decoration-thickness: auto;
        }
        .ca-brand-list .title span{
            font-size: 16px;
            font-style: italic;
        }
        .ca-brand-list .group-title {
            padding-top: 15px;
            padding-bottom: 15px;
            border-top: 1px solid rgba(0,0,0,.1);
            border-bottom: 1px solid rgba(0,0,0,.1);
        }
        .ca-brand-list .group-title span {
            font-weight: 600;
            font-size: 36px;
        }
        .ca-brand-list .group-item a {
            font-size: 18px;
        }
    </style>
@endsection
