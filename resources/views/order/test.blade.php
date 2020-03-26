@extends('order.index')

@section('search_bar')
    @parent
    <div class='input-group'>

        <select name="search[supplier]" class="form-control">
            <option value=""
                    @if( array_get($requestSearch,'supplier_id',''))  selected="selected"@endif>
                杨洋
            </option>
            <option value=""
                    @if( array_get($requestSearch,'supplier_id',''))  selected="selected"@endif>
                杨雷
            </option>
        </select>
    </div>
@endsection
@section('shop_name')
    <label class="label label-primary">所属供应商：杨洋</label>
@endsection