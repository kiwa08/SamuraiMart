@extends('layouts.app')

 @section('content')
 <div class="row">

    <div class="col-2">
        {{-- サイドバーをコンポーネントとして呼び出す --}}
        {{-- 連想配列を作成することで、コンポーネントへと変数を渡す --}}
        @component('components.sidebar', ['categories' => $categories, 'major_categories' => $major_categories])
        @endcomponent
    </div>

     <div class="col-9">

         <div class="container">
            @if ($category !== null)
                <a href="{{ route('products.index') }}">トップ</a> > <a href="#">{{ $major_category->name }}</a> > {{ $category->name }}
                {{-- 絞り込んでいるカテゴリー名と件数を表示 --}}
                <h1>{{ $category->name }}の商品一覧{{$total_count}}件</h1>
            @endif
         </div>

         <div>
            Sort By
            {{-- ソートするためのリンクを追加する関数 --}}
             @sortablelink('id', 'ID')
             @sortablelink('price', 'Price')
         </div>

         <div class="container mt-4">
             <div class="row w-100">
                 @foreach($products as $product)
                 <div class="col-3">
                     <a href="{{ route('products.show', $product) }}">
                        {{-- データベースに登録した商品画像を表示 --}}
                        @if ($product->image !== "")
                        <img src="{{ asset($product->image) }}" class="img-thumbnail">
                        @else
                        <img src="{{ asset('img/dummy.png')}}" class="img-thumbnail">
                        @endif
                     </a>
                     <div class="row">
                         <div class="col-12">
                             <p class="samuraimart-product-label mt-2">
                                 {{$product->name}}<br>
                                 <label>￥{{$product->price}}</label>
                             </p>
                         </div>
                     </div>
                 </div>
                 @endforeach
             </div>
         </div>
         {{-- カテゴリーで絞り込んだ条件を保持してページング --}}
         {{ $products->appends(request()->query())->links() }}
     </div>
 </div>
 @endsection
