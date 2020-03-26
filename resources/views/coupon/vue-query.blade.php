<div style='max-height:500px;overflow:auto;min-width:850px;'>
    <table class="table table-hover" style="min-width:850px;">
        <tbody>
        @foreach($coupons as $row)
            <tr>
                <td>
                    {{$row['name']}}
                </td>
                <td style="width:80px;"><a href="javascript:;" @click='select_coupon({{json_encode($row)}})'>选择</a></td>
            </tr>
        @endforeach
        @if(count($coupons)<=0)
        <tr>
            <td colspan='4' align='center'>未找到优惠券</td>
        </tr>
        @endif
        </tbody>
    </table>
</div>