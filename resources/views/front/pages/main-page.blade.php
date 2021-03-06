@extends('front.pages.layout', ['title' => 'Main Page'])
@section('content')
    <div class="container">

        <div class="row">
            <aside class="col-md-3">
                <nav class="card">
                    <ul class="menu-category">
                        @foreach($categories as $category)
                            <li><a href="{{route('front.productsByCategory', [$category->slug])}}">{{$category->name}}</a></li>
                        @endforeach
                    </ul>
                </nav>
            </aside> <!-- col.// -->
            <div class="col-md-9">
                <article class="banner-wrap">
                    <img src="{{asset('vendor/front/images/banner.jpg')}}" class="w-100 rounded">
                </article>
            </div> <!-- col.// -->
        </div> <!-- row.// -->
    </div> <!-- container //  -->

    <section class="section-name padding-y-sm">
        <div class="container">

            <header class="section-heading">
                <a href="#" class="btn btn-outline-primary float-right">See all</a>
                <h3 class="section-title">Popular products</h3>
            </header><!-- sect-heading -->


            <div class="row">
                @forelse($lastProducts as $product)
                    <div class="col-md-3">
                        <div href="#" class="card card-product-grid">
                            <a href="#" class="img-wrap"> <img src="{{$product->coverImagePath()}}"> </a>
                            <figcaption class="info-wrap">
                                <a href="{{route('front.productDetail', [$product->slug])}}"
                                   class="title">{{$product->name}}</a>
                                <div class="price mt-1">${{$product->price}}</div> <!-- price-wrap.// -->
                            </figcaption>
                        </div>
                    </div> <!-- col.// -->
                @empty
                    There is no products
                @endforelse
            </div> <!-- row.// -->

        </div><!-- container // -->
    </section>
    <!-- ========================= SECTION  END// ========================= -->

@endsection