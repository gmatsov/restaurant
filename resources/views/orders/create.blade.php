@extends('layouts.app')
@section('scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script type="text/javascript" src="{{ URL::asset('js/create_new_order_page.js') }}"></script>
@endsection

@section('content')
    <h1 class="col-md-10 text-center" xmlns="http://www.w3.org/1999/html">New order on table № {{ Request()->table }}</h1>
    <div class="row col-md-10">
    <div class="row col-md-5 float-left mr-2 ml-2">
        <form id="order_form" action="{{ route('order.process', ['order_id' => $order_id]) }}" method="POST">
            @csrf
            <div class="form-group">
                @if($order_id)
                <h4>Order № {{ $order_id }}</h4>
                @else
                <h4>Create New Order</h4>
                @endif
            </div>
            <div id="order_form_body">
                <input type="number" name="table_id" readonly hidden value="{{ Request()->table }}">
                {{-- <input type="number" name="order_id" readonly hidden value="{{ $order_id }}"> --}}

            </div>
            <div class="row">
                <input class="btn btn-success col-md-10" type="submit">
            </div>
        </form>
    </div>

    <div class="row col-md-4">
        <div>
            <h3 class="text-center">Filter</h3>
            <div class="row" id="product_types_filter">
                @foreach($types as $type)
                    <button class="btn btn-danger float-left mr-1 mb-1"
                            id="type_{{$type->id}}">{{$type->type}}
                    </button>
                @endforeach
            </div>
            <div id="displayed_products">
                <h3 class="text-center">Products</h3>
                @foreach($types as $type)
                    <div id="products_from_type_{{$type->id}}" class="row" hidden>
                        @foreach($products as $product)
                        @if($product->product_type == $type->id)
                        <div class="flex-row">
                            <span>max qty:{{ $product->quantity / $product->sell_quantity_base }}</span><br>
                            <button class="btn btn-info float-left mr-1 mb-1" id="{{$product->id}}">
                                {{$product->name}}
                            </button>
                        </div>
                        @endif
                        @endforeach
                    </div>
                @endforeach
            </div>
            <div id="product_for_cart" hidden>
                <div class="row">
                    <p id="current_product_for_cart" class="float-left mr-2"></p>
                    <p id="current_product_for_cart_delimiter">&nbsp;:&nbsp;</p>
                    <p id="current_product_for_cart_quantity"></p>
                </div>
                <div>
                    <button id="quantity_increase"
                            class="btn btn-success float-left mr-2"
                            onclick="increaseQuantity()">+
                    </button>
                    <button id="quantity_decrease"
                            class="btn btn-danger float-left mr-4"
                            onclick="decreaseQuantity()">-
                    </button>

                    <button id="add_to_order" class="btn btn-info" onclick="addOrderRow()">Add</button>
                </div>
            </div>
            <div>
                @if ($order_id)
                <a class="btn btn-warning btn-lg active btn-block mt-4" href="{{ route('order.close', ['order_id' => $order_id]) }}" role="button">Close Bill</a> 
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-3 justify-content-md-center">
        @if ($order_details)
            <h3>Order Detials</h3>
            @php
            $total = 0; 
            @endphp
            @foreach ($order_details as $item)
            <span>{{ $item->name }}</span></br>
            <span>{{ $item->product_quantity }} x {{ $item->product_price }} - {{ $item->product_quantity * $item->product_price }}</span></br>
            @php
            $total += $item->product_quantity * $item->product_price; 
            @endphp
            @endforeach
            <span>--------</span></br>
            <span>Total: {{ $total }}</span>
        @endif
    </div>
    </div>
@endsection