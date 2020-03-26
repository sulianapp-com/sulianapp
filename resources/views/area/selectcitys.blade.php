@foreach ($citys as $city)
    <li>
        <label class='checkbox-inline'>
            <input type='checkbox' class='city' style='margin-top:8px;' city="{{ $city['areaname'] }}" city_id="{{ $city['id'] }}" /> {{ $city['areaname'] }}
        </label>
    </li>
@endforeach
<script>
    $('.city').click(function(){
        var checked = $(this).get(0).checked;
        var cityall = $(this).parent().parent().parent().parent().find('.cityall');
        if(checked){
            cityall.get(0).checked = true;
        }
        var count = cityall.parent().parent().find('.city:checked').length;
        if(count>0){
            cityall.next().html("(" + count + ")")    ;
        }
        else{
            cityall.next().html("");
        }
    });
</script>
