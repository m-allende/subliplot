<div class="col-lg-3 col-md-12 mt-5">
    <!-- category Start -->
    <div class="border-bottom pb-4 truncate">
        <h5 class="font-weight-semi-bold mb-4">Filtrar por Categoria</h5>
        <form>
            <div class="custom-control custom-radio d-flex align-items-center justify-content-between mb-3">
                <input class="custom-control-input category" type="radio" @checked(true) name="category"
                    value="0" id="category-all">
                <label class="custom-control-label" for="category-all">
                    Todas las Categorias
                </label>
                <span class="badge border font-weight-normal">{{ $cant_prod }} </span>
            </div>
            @foreach ($categories as $category)
                <div class="custom-control custom-radio d-flex align-items-center justify-content-between mb-3 ml-3">
                    <input class="custom-control-input category" type="radio" name="category"
                        value="{{ $category->id }}" id="category-{{ $category->id }}">
                    <label class="custom-control-label" for="category-{{ $category->id }}">
                        {{ $category->name }}
                    </label>
                    @php
                        $quantity = 0;
                        foreach ($prod_by_category as $prod) {
                            if ($prod->category == $category->id) {
                                $quantity = $prod->quantity;
                            }
                        }
                    @endphp
                    <span class="badge border font-weight-normal">{{ $quantity }} </span>
                </div>
            @endforeach
        </form>
    </div>
    @if (sizeof($categories) > 6)
        <div class="txtcol mb-4 text-center"><a class="rd-nav-link" href="#">Ver Más</a></div>
    @endif
    <!-- category End -->
    <!-- brand Start -->
    <div class="border-bottom pb-4 truncate">
        <h5 class="font-weight-semi-bold mb-4">Filtrar por Marcas</h5>
        <form>
            <div class="custom-control custom-radio d-flex align-items-center justify-content-between mb-3">
                <input class="custom-control-input brand" type="radio" @checked(true) name="brand"
                    value="0" id="brand-all">
                <label class="custom-control-label" for="brand-all">
                    Todas las Marcas
                </label>
                <span class="badge border font-weight-normal">{{ $cant_prod }} </span>
            </div>
            @foreach ($brands as $brand)
                <div class="custom-control custom-radio d-flex align-items-center justify-content-between mb-3 ml-3">
                    <input class="custom-control-input brand" type="radio" name="brand" value="{{ $brand->id }}"
                        id="brand-{{ $brand->id }}">
                    <label class="custom-control-label" for="brand-{{ $brand->id }}">
                        {{ $brand->name }}
                    </label>
                    @php
                        $quantity = 0;
                        foreach ($prod_by_brand as $prod) {
                            if ($prod->brand == $brand->id) {
                                $quantity = $prod->quantity;
                            }
                        }
                    @endphp
                    <span class="badge border font-weight-normal">{{ $quantity }} </span>
                </div>
            @endforeach
        </form>
    </div>
    @if (sizeof($brands) > 6)
        <div class="txtcol mb-4 text-center"><a class="rd-nav-link" href="#">Ver Más</a></div>
    @endif

    <!-- category End -->
    <!-- Price Start -->
    <div class="border-bottom mb-4 pb-4">
        <h5 class="font-weight-semi-bold mb-4">Filtrar por Precio</h5>
        <form>
            <input type="text" class="js-range-slider" name="my_range" value="" />
        </form>
    </div>
    <!-- Price End -->
</div>
