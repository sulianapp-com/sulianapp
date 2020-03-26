{{! $expressCompanies = \app\common\repositories\ExpressCompany::create()->all()}}
@foreach ($expressCompanies as $item)
    <option value="{{$item['value']}}" data-name="{{$item['name']}}">{{$item['name']}}</option>
@endforeach