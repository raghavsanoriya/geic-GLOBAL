@php
    $visibleRows=array_slice($rows,0,20);
    $isTimeline=in_array($chartId,['months','years','conversions'],true);
    $metric=$chartId==='conversions'?'percent':(str_contains($chartId,'country')?'registered':'leads');
    $maximum=max([1,...array_map(fn($row)=>(float)($row[$metric]??0),$visibleRows)]);
    $height=$isTimeline?320:max(210,count($visibleRows)*58+90);
    $pointCount=count($visibleRows);
    $plotWidth=542;
    $points=[];
    foreach($visibleRows as $index=>$row){
        $x=58+($pointCount<=1?$plotWidth/2:$index*$plotWidth/($pointCount-1));
        $y=252-((float)($row[$metric]??0)/$maximum*192);
        $points[]=['x'=>round($x,2),'y'=>round($y,2),'value'=>$row[$metric]??null,'label'=>$row['label']];
    }
    $line=implode(' ',array_map(fn($point)=>$point['x'].','.$point['y'],$points));
    $segments=[];$segment=[];
    foreach($points as $point){
        if($point['value']===null){if($segment!==[]){$segments[]=$segment;$segment=[];}}
        else{$segment[]=$point;}
    }
    if($segment!==[]){$segments[]=$segment;}
@endphp
<svg xmlns="http://www.w3.org/2000/svg" width="640" height="{{$height}}" viewBox="0 0 640 {{$height}}" role="img" aria-labelledby="chart-title-{{$chartId}}" data-report-chart="{{$chartId}}">
    <title id="chart-title-{{$chartId}}">{{$title}}</title>
    <rect width="640" height="{{$height}}" fill="#fff"/>
    @if($isTimeline)
        <defs><linearGradient id="report-area-{{$chartId}}" x1="0" x2="0" y1="0" y2="1"><stop offset="0" stop-color="#5d87ff" stop-opacity=".22"/><stop offset="1" stop-color="#5d87ff" stop-opacity=".015"/></linearGradient></defs>
        @for($tick=0;$tick<=4;$tick++)
            @php($tickY=252-$tick*48)
            <line x1="58" y1="{{$tickY}}" x2="600" y2="{{$tickY}}" stroke="#e9edf3" stroke-dasharray="4 5"/>
            <text x="48" y="{{$tickY+5}}" text-anchor="end" fill="#62718a" font-size="16" font-family="Arial,sans-serif">{{number_format($maximum*$tick/4,$maximum<4?1:0)}}{{$metric==='percent'?'%':''}}</text>
        @endfor
        @if($chartId==='years')
            @foreach($points as $point)
                @php($barWidth=min(42,$plotWidth/max(1,$pointCount)*.5))
                @if($point['value']>0)<rect x="{{$point['x']-$barWidth/2}}" y="{{$point['y']}}" width="{{$barWidth}}" height="{{252-$point['y']}}" rx="6" fill="#5d87ff"/>@else<line x1="{{$point['x']-$barWidth/2}}" y1="252" x2="{{$point['x']+$barWidth/2}}" y2="252" stroke="#c9d8ff" stroke-width="3"/>@endif
            @endforeach
        @elseif($chartId==='conversions')
            @foreach($segments as $segment)
                <polyline points="{{implode(' ',array_map(fn($point)=>$point['x'].','.$point['y'],$segment))}}" fill="none" stroke="#5d87ff" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
                @foreach($segment as $point)<circle cx="{{$point['x']}}" cy="{{$point['y']}}" r="4" fill="#fff" stroke="#5d87ff" stroke-width="2"><title>{{$point['label']}}: {{$point['value']}}%</title></circle>@endforeach
            @endforeach
        @elseif($points!==[])
            <polygon points="{{$points[0]['x']}},252 {{$line}} {{$points[array_key_last($points)]['x']}},252" fill="url(#report-area-{{$chartId}})"/>
            <polyline points="{{$line}}" fill="none" stroke="{{$chartId==='months'?'#36bffa':'#5d87ff'}}" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/>
            @foreach($points as $point)<circle cx="{{$point['x']}}" cy="{{$point['y']}}" r="4" fill="#fff" stroke="{{$chartId==='months'?'#36bffa':'#5d87ff'}}" stroke-width="2"><title>{{$point['label']}}: {{$point['value']??'Not applicable'}}{{$metric==='percent'?'%':''}}</title></circle>@endforeach
        @endif
        @foreach($points as $point)
            @if($loop->first || $loop->last || $loop->index%max(1,(int)ceil($pointCount/8))===0)
            <text x="{{$point['x']}}" y="290" text-anchor="middle" fill="#62718a" font-size="16" font-family="Arial,sans-serif">{{$chartId==='months'?\Carbon\CarbonImmutable::createFromFormat('!Y-m',$point['label'])->format('M y'):$point['label']}}</text>
            @endif
        @endforeach
        @if($chartId==='conversions'&&$segments===[])<text x="329" y="155" text-anchor="middle" fill="#62718a" font-size="17" font-family="Arial,sans-serif">Not applicable — no enquiry cohorts in this period</text>
        @elseif($maximum===1 && array_sum(array_column($visibleRows,$metric))===0)<text x="329" y="155" text-anchor="middle" fill="#62718a" font-size="17" font-family="Arial,sans-serif">No positive recorded values in this period</text>@endif
    @else
        @if(str_contains($chartId,'country'))<text x="24" y="28" fill="#62718a" font-size="14" font-family="Arial,sans-serif">Registered (blue) · Successful (cyan)</text>@endif
        @forelse($visibleRows as $row)
            @php($y=55+$loop->index*54)
            @php($width=round((float)($row[$metric]??0)/$maximum*320,2))
            <text x="24" y="{{$y+13}}" fill="#40516b" font-size="17" font-family="Arial,sans-serif">{{\Illuminate\Support\Str::limit($row['label'],20)}}</text>
            <rect x="210" y="{{$y}}" width="320" height="13" rx="6" fill="#eef3ff"/>
            @if($width>0)<rect x="210" y="{{$y}}" width="{{$width}}" height="13" rx="6" fill="#5d87ff"/>@endif
            @if(isset($row['successful']))<rect x="210" y="{{$y+20}}" width="{{round($row['successful']/$maximum*320,2)}}" height="7" rx="3" fill="#36bffa"/>@endif
            <text x="555" y="{{$y+13}}" fill="#132745" font-size="17" font-family="Arial,sans-serif">{{$row[$metric]??0}}@if(isset($row['successful'])) / {{$row['successful']}}@endif</text>
        @empty
            <circle cx="320" cy="84" r="30" fill="#f0f4ff"/>
            <path d="M306 93V82m14 11V73m14 20V79" fill="none" stroke="#8ba3d3" stroke-width="5" stroke-linecap="round"/>
            <text x="320" y="150" text-anchor="middle" fill="#62718a" font-size="18" font-family="Arial,sans-serif">No recorded data for this section</text>
        @endforelse
    @endif
</svg>
