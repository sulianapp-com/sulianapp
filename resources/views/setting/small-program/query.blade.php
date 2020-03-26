<div style='max-height:500px;overflow:auto;min-width:850px;'>
    <table class="table table-hover" style="min-width:850px;">
        <tbody>
            @foreach($temp_list as $row)
                <tr>
                    <td>{{$row['title']}}</td>
                    <td style="width:80px;">
                        <a href="javascript:;" onclick='select_temp({{json_encode($row)}})'>选择</a>
                    </td>
                </tr>
            @endforeach
        @if ($temp_list->count() <= 0)
            <tr>
                <td colspan='4' align='center'>未找到消息模板</td>
            </tr>
        @endif
        </tbody>
    </table>
</div>

