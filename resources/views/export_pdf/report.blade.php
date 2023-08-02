<html>
        <head>
        <meta charset="UTF-8">
            <style>
                body {font-family: sans-serif;
                 font-size: 10pt;
                 color:#444444;
             }
             p {    margin: 0pt; }
             table.items {
                 border: 0.1mm solid #000000;
             }
            .cont-header { *display: inline-block; }
            * html .cont-header { height: 1%; }
            .cont-header:after { content: "."; display: block; height: 0; clear: both; visibility: hidden; font-size: 0; }
        </style>
        </head>
        <body>
        @foreach($reportLines as $line)
            @if($line->page_type == 2)
                <pagebreak>
            @endif
            <div>
                <h1 style="text-align:center;color:{{$report->empresa->color}};">{{$line->title}}</h1>
                <p>{!! $line->description !!}</p>
                @if(!empty($line->files))
                    @foreach(json_decode($line->files) as $img)
                        <img src="{{asset('storage/report/'.$line->id.'/'.$img)}}" style="width:250px;" alt="">
                    @endforeach
                @endif
            </div>
        @endforeach
        <div style="text-align: center; font-style: italic;"></div>
        </body>
        </html>