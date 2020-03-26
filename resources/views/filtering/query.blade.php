<div style='max-height:500px;overflow:auto;min-width:850px;'>
    <table class="table table-hover" style="min-width:850px;">
        <tbody>
        @foreach($filter_group as $row)
            <tr>
                <td>{{$row['name']}}
                </td>
                <td style="width:80px;"><a href="javascript:;" onclick='select_filter({{json_encode($row)}})'>选择</a></td>
            </tr>
        @endforeach
        @if(count($filter_group)<=0)
        <tr>
            <td colspan='4' align='center'>未找到标签组</td>
        </tr>
        @endif
        </tbody>
    </table>
</div>