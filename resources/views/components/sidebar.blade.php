{{-- 大分類ごとに各カテゴリーを並べて表示 --}}
<div class="container">
    {{-- 親カテゴリーテーブルをforeach文でループ処理 --}}
    @foreach ($major_categories as $major_category)
         <h2>{{ $major_category->name }}</h2>
        {{-- 子カテゴリーをforeach文でループ処理 --}}
        {{-- 呼び出すルーティングの後に連想配列で変数を渡すことで、コントローラー側へ値を渡す --}}
        @foreach ($categories as $category)
            @if ($category->major_category_id === $major_category->id)
                <label class="samuraimart-sidebar-category-label"><a href="{{ route('products.index', ['category' => $category->id]) }}">{{ $category->name }}</a></label>
            @endif
        @endforeach
    @endforeach
</div>
