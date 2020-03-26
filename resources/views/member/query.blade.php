<div style='max-height:500px;overflow:auto;'>
    <table class="table table-hover">
        <tbody>
        @if (is_array($members))
            @foreach($members as $row)
                {!! $row['nick_name'] = str_replace("'","’",$row['nick_name']) !!}
                <tr>
                    <td><img src='{{$row['avatar']}}' style='width:30px;height:30px;padding1px;border:1px solid #ccc' /> {{$row['nick_name']}}</td>
                    <td>{{$row['realname']}}</td>
                    <td>{{$row['mobile']}}</td>
                    <td style="width:80px;">
                        <a href="javascript:;" onclick='select_member({{json_encode($row)}})'>选择</a>
                    </td>
                </tr>
            @endforeach
        @elseif (is_numeric($members))
            <tr>
                <td>总店</td>
                <td></td>
                <td></td>
                <td style="width:80px;">
                    <a href="javascript:;" onclick='select_member({{json_encode(['uid' => 0, 'nickname' => '总店'])}})'>选择</a>
                </td>
            </tr>
        @endif

        @if (count($members) <= 0)
        <tr>
            <td colspan='4' align='center'>未找到会员</td>
        </tr>
        @endif
        </tbody>
    </table>
</div>

