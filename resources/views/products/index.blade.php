@extends('layouts.app')

@section('content')

    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Products</h1>
    </div>


    <div class="card">
        <form action="" method="get" id="searchForm" class="card-header">
            <div class="form-row justify-content-between">
                <div class="col-md-2">
                    <input type="text" name="title" placeholder="Product Title" class="form-control">
                </div>
                <div class="col-md-2">
                    <select name="variant" id="" class="form-control">
                    @foreach($variants as $variant)
                    <option value="{{ $variant->id }}">{{ $variant->title }}</option>
                    @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">Price Range</span>
                        </div>
                        <input type="text" name="price_from" aria-label="First name" placeholder="From" class="form-control">
                        <input type="text" name="price_to" aria-label="Last name" placeholder="To" class="form-control">
                    </div>
                </div>
                <div class="col-md-2">
                    <input type="date" name="date" placeholder="Date" class="form-control">
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary float-right"><i class="fa fa-search"></i></button>
                </div>
            </div>
        </form>

        <div class="card-body">
            <div class="table-response">
                <table class="table">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>Title</th>
                        <th>Description</th>
                        <th>Variant</th>
                        <th width="150px">Action</th>
                    </tr>
                    </thead>

                    <tbody id="product_container">

                    @if($products)
                        @foreach ($products as $product)

                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $product->title }} <br> Created at : {{ date('d-M-Y', strtotime($product->created_at)) }}</td>
                            <td>{{ $product->description }}</td>
                            <td>
                                <dl class="row mb-0" style="height: 80px; overflow: hidden" id="variant">

                                    <dt class="col-sm-3 pb-0">
                                        @if($product->variantPrices)
                                            @foreach($product->variantPrices as $price)
                                                {{ $price->variant }}
                                            @endforeach
                                        @endif
                                    </dt>
                                    <dd class="col-sm-9">
                                        <dl class="row mb-0">
                                            @if($product->variantPrices)
                                                @foreach($product->variantPrices as $price)
                                                    <dt class="col-sm-4 pb-0">Price : {{ number_format( $price->price, 2) }}</dt>
                                                
                                                    @if($price->stock)
                                                    <dd class="col-sm-8 pb-0">InStock : {{ number_format($price->stock, 2) }}</dd>
                                                    @else
                                                    <dd class="col-sm-8 pb-0"> OutOfStock</dd>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </dl>
                                    </dd>
                                </dl>
                                <button onclick="$('#variant').toggleClass('h-auto')" class="btn btn-sm btn-link">Show more</button>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('product.edit', 1) }}" class="btn btn-success">Edit</a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    @endif

                    </tbody>

                </table>
            </div>

        </div>

        <div class="card-footer">
            <div class="row justify-content-between">
                <div class="col-md-6">
                    <p>
                        Showing {{ ($products->currentpage()-1) * $products->perpage() + 1 }} to 
                        {{ $products->currentpage() * $products->perpage() }} out of {{ $products->total() }}
                    </p>
                </div>
                <div class="col-md-2">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>

    <script>
    document.getElementById("searchForm").addEventListener("submit", search);

    function search (event) {
        event.preventDefault();

        let params = {
            title: document.querySelector('input[name=title').value,
            variant: document.querySelector('select[name=variant').value,
            price_from: document.querySelector('input[name=price_from').value,
            price_to: document.querySelector('input[name=price_to').value,
            date: document.querySelector('input[name=date').value,
            _token: '{{ csrf_token() }}'
        }

        $.post('/filter', params).then(data => {
            let count = 0;
            let html = ``;

            $.map(data, (val, key) => {
                let entry_date = new Date(val['created_at']);
                let month = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul","Aug", "Sep", "Oct", "Nov", "Dec"];
                html += `
                        <tr>
                            <td>${++count}</td>
                            <td>${val['title']} <br> Created at : ${entry_date.getDate()}-${month[entry_date.getMonth()]}-${entry_date.getFullYear()}</td>
                            <td>${val['description']}</td>
                            <td>
                                <dl class="row mb-0" style="height: 80px; overflow: hidden" id="variant">

                                    <dt class="col-sm-3 pb-0">
                                        Price: 
                                    </dt>
                                    <dd class="col-sm-9">
                                        <dl class="row mb-0">
                                            Stock info
                                        </dl>
                                    </dd>
                                </dl>
                                <button onclick="$('#variant').toggleClass('h-auto')" class="btn btn-sm btn-link">Show more</button>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ URL('product') }}/${val['id']}/edit" class="btn btn-success">Edit</a>
                                </div>
                            </td>
                        </tr>`;
            });

            $('#product_container').val(html)
        });
    }
    </script>

@endsection
