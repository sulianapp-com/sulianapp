<div style='max-height:500px;overflow:auto;min-width:850px;'>
    <table class="table table-hover" style="min-width:850px;">
        <tbody>
        @foreach($goods as $row)
            <tr>
                <td><img src='{{yz_tomedia($row['thumb'])}}'
                         style='width:30px;height:30px;padding1px;border:1px solid #ccc'/> {{$row['title']}}[ID:{{$row['id']}}]
                </td>
                <td style="width:80px;"><a href="javascript:;" onclick='@if ($exchange == 1) select_goods_exchange({{json_encode($row)}}) @else select_good({{json_encode($row)}}) @endif'>选择</a></td>
            </tr>
        @endforeach
        @if(count($goods)<=0)
        <tr>
            <td colspan='4' align='center'>未找到商品</td>
        </tr>
        @endif
        </tbody>
    </table>
</div>