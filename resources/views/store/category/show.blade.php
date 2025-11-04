@extends('store.layouts.app')
@section('title', $category->name)

@section('content')
  <section class="container-xxl py-4">
    <h1 class="h4 text-white mb-3">{{ $category->name }}</h1>
    <div class="row g-3">
      @forelse($products as $product)
        <div class="col-6 col-md-4 col-lg-3">
          @include('store.components.product-card', ['product'=>$product])
        </div>
      @empty
        <div class="col-12"><div class="alert alert-secondary">Sin productos.</div></div>
      @endforelse
    </div>
    {{ $products->links() }}
  </section>
@endsection
