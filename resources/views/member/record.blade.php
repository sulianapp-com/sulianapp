<div style='max-height:500px;overflow:auto;min-width:850px;'>
    <table class="table table-hover" style="min-width:850px;">
        <tbody>
        @if (count($records) > 0)
            <tr>
                <td>原上线ID</td>
                <td>修改时间</td>
            </tr>
            @foreach($records as $row)
                <tr>
                    <td>{{$row['parent_id']}}</td>
                    <td>{{$row['created_at']}}</td>
                </tr>
            @endforeach
        @endif

        @if (count($records) <= 0)
        <tr>
            <td colspan='4' align='center'>未找到记录</td>
        </tr>
        @endif
        </tbody>
    </table>
</div>

