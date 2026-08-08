<html>
<head>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap');

    </style>

</head>
<body style="font-family: 'Montserrat', 'trebuchet ms'">




@forelse($resumes as $resume)


    <div style="padding: 10px;padding-top: 0px;border-bottom: silver 1px solid;">
        <div style="font-size: 22px;float: left;">TARKAN.APP</div>

        <div style="float: right;text-align: right;">
            <div style="font-weight: bold;">RESUMO DO DISPOSITIVO</div>
            <div style="font-size: 12px;">Gerado em <?= date("d/m/Y H:i:s") ?></div>
        </div>

        <DIV style="clear: both;"></div>
    </div>

    <div style="padding: 10px;margin-bottom: 0px;">
    <div style="font-size: 12px;float: left;">
        @isset($devices[$resume['deviceId']]['attributes']['placa'])
        <B>{{$devices[$resume['deviceId']]['attributes']['placa']}}</B> |
            @endisset

            {{$resume['deviceName']}}</div>

    <div style="float: right;text-align: right;font-size: 12px;">
        @isset($devices[$resume['deviceId']]['attributes']['contact'])
            <div>Contato <b>{{$devices[$resume['deviceId']]['attributes']['contact']}}</b></div>
        @endisset
    </div>

    <div style="clear: both;"></div>
</div>

<div style="border: silver 1px solid;border-radius: 5px;">
    <div style="border-right: silver 1px dotted;border-bottom: silver 1px dotted;float: left;width: 50%;text-align: center;padding-top: 20px;padding-bottom: 20px;box-sizing: border-box;">
        <div style="font-size: 11px;text-transform: uppercase;">Data de Início</div>
        <div style="font-size: 12px;font-weight: bold;color: #007eff;margin-top: 3px;">
            {{date("d/m/Y H:i",strtotime($resume['startTime']))}}
        </div>
    </div>
    <div style="border-bottom: silver 1px dotted;float: left;width: 50%;text-align: center;padding-top: 20px;padding-bottom: 20px;box-sizing: border-box;">
        <div style="font-size: 11px;text-transform: uppercase;">Data Final</div>
        <div style="font-size: 12px;font-weight: bold;color: #007eff;margin-top: 3px;">
            {{date("d/m/Y H:i",strtotime($resume['endTime']))}}
        </div>
    </div>

    <div style="clear: both;"></div>

    <div style="border-right: silver 1px dotted;border-bottom: silver 1px dotted;float: left;width: 50%;text-align: center;padding-top: 20px;padding-bottom: 20px;box-sizing: border-box;">
        <div style="font-size: 11px;text-transform: uppercase;">DISTÂNCIA TOTAL</div>
        <div style="font-size: 12px;font-weight: bold;color: #007eff;margin-top: 3px;">
           {{$resume['distance']/1000}} KM
        </div>
    </div>
    <div style="border-right: silver 1px dotted;border-bottom: silver 1px dotted;float: left;width: 50%;text-align: center;padding-top: 20px;padding-bottom: 20px;box-sizing: border-box;">
        <div style="font-size: 11px;text-transform: uppercase;">HORAS LIGADO</div>
        <div style="font-size: 12px;font-weight: bold;color: #007eff;margin-top: 3px;">{{(($resume['engineHours']/60)/60)/1000}} hs</div>
    </div>
    <div style="clear: both;"></div>

    <div style="border-right: silver 1px dotted;border-bottom: silver 1px dotted;float: left;width: 50%;text-align: center;padding-top: 20px;padding-bottom: 20px;box-sizing: border-box;">
        <div style="font-size: 11px;text-transform: uppercase;">Inicio do Hodometro</div>
        <div style="font-size: 12px;font-weight: bold;color: #007eff;margin-top: 3px;">{{$resume['startOdometer']/1000}} km</div>
    </div>
    <div style="border-bottom: silver 1px dotted;float: left;width: 50%;text-align: center;padding-top: 20px;padding-bottom: 20px;box-sizing: border-box;">
        <div style="font-size: 11px;text-transform: uppercase;">Final do Hodometro</div>
        <div style="font-size: 12px;font-weight: bold;color: #007eff;margin-top: 3px;">{{$resume['endOdometer']/1000}} km</div>
    </div>

    <div style="clear: both;"></div>

    <div style="border-right: silver 1px dotted;float: left;width: 50%;text-align: center;padding-top: 20px;padding-bottom: 20px;box-sizing: border-box;">
        <div style="font-size: 11px;text-transform: uppercase;">Velocidade Média</div>
        <div style="font-size: 12px;font-weight: bold;color: #007eff;margin-top: 3px;">{{round($resume['averageSpeed']*1.852)}} km/h</div>
    </div>
    <div style="float: left;width: 50%;text-align: center;padding-top: 20px;padding-bottom: 20px;box-sizing: border-box;">
        <div style="font-size: 11px;text-transform: uppercase;">Velocidade Máxima</div>
        <div style="font-size: 12px;font-weight: bold;color: #007eff;margin-top: 3px;">{{round($resume['maxSpeed']*1.852)}} km/h</div>
    </div>

    <div style="clear: both;"></div>
</div>

<div style="padding: 10px;border-top: silver 1px solid;border-bottom: silver 1px solid;margin-top: 10px;">
    <div style="font-size: 12px;text-align: center;">Total de <B>{{count($trips[$resume['deviceId']])}}</B> Viagens</div>
</div>

    @foreach($trips[$resume['deviceId']] as $id=>$trip)


        @if($id==2 || (($id-2)%3 === 0))

            <div style="page-break-before: always;"></div>
            <div style="padding: 10px;padding-top: 0px;border-bottom: silver 1px solid;">
                <div style="font-size: 22px;float: left;">TARKAN.APP</div>

                <div style="float: right;text-align: right;">
                    <div style="font-weight: bold;">RESUMO DO DISPOSITIVO</div>
                    <div style="font-size: 12px;">Gerado em <?= date("d/m/Y H:i:s") ?></div>
                </div>

                <DIV style="clear: both;"></div>
            </div>

            <div style="padding: 10px;margin-bottom: 0px;">
                <div style="font-size: 12px;float: left;">
                    @isset($devices[$resume['deviceId']]['attributes']['placa'])
                        <B>{{$devices[$resume['deviceId']]['attributes']['placa']}}</B> |
                    @endisset

                    {{$resume['deviceName']}}</div>

                <div style="float: right;text-align: right;font-size: 12px;">
                    @isset($devices[$resume['deviceId']]['attributes']['contact'])
                        <div>Contato <b>{{$devices[$resume['deviceId']]['attributes']['contact']}}</b></div>
                    @endisset
                </div>

                <DIV style="clear: both;"></div>
            </div>
            @endif


        <div style="border: silver 1px solid;border-radius: 5px;overflow: hidden;margin-top: 10px;">



    <div style="float: left; width: 50%;">

        <div style="border-bottom: silver 1px dotted;text-align: center;padding: 10px;padding-left: 20px;box-sizing: border-box;">
            <div style="font-size: 11px;text-transform: uppercase;float: left;">
                {{date("d/m/Y",strtotime($trip['startTime']))}}<br>
                {{date("H:i",strtotime($trip['startTime']))}}
            </div>

            <div style="font-size: 11px;text-transform: uppercase;margin-left: 100px;position: relative;">
                <div style="position: absolute;left: -20px;top: 0px;">
                    <div style="background: url('https://demo.tarkan.app/img/flag-ini.png');background-size: cover;width: 15px;height: 15px;"></div>
                </div>

                @if($trip['startAddress'])
                {{$trip['startAddress']}}
                @else
                    {{$trip['startLat']}}, {{$trip['startLon']}}
                @endif
            </div>
            <div style="clear: both;"></div>
        </div>

        <div style="clear: both;"></div>

        <div style="border-bottom: silver 1px dotted;border-right: silver 1px dotted;float: left;width: 50%;text-align: center;padding-top: 15px;padding-bottom: 15px;box-sizing: border-box;">
            <div style="font-size: 11px;text-transform: uppercase;">Velocidade Média</div>
            <div style="font-size: 12px;font-weight: bold;color: #007eff;margin-top: 3px;">{{round($trip['averageSpeed']*1.852)}} km/h</div>
        </div>
        <div style="border-bottom: silver 1px dotted;float: right;width: 50%;text-align: center;padding-top: 15px;padding-bottom: 15px;box-sizing: border-box;">
            <div style="font-size: 11px;text-transform: uppercase;">Velocidade Máxima</div>
            <div style="font-size: 12px;font-weight: bold;color: #007eff;margin-top: 3px;">{{round($trip['maxSpeed']*1.852)}} km/h</div>
        </div>

        <div style="clear: both;"></div>

        <div style="border-bottom: silver 1px dotted;border-right: silver 1px dotted;float: left;width: 50%;text-align: center;padding-top: 15px;padding-bottom: 15px;box-sizing: border-box;">
            <div style="font-size: 11px;text-transform: uppercase;">Inicio do Hodometro</div>
            <div style="font-size: 12px;font-weight: bold;color: #007eff;margin-top: 3px;">{{$trip['startOdometer']/1000}} km</div>
        </div>
        <div style="border-bottom: silver 1px dotted;float: right;width: 50%;text-align: center;padding-top: 15px;padding-bottom: 15px;box-sizing: border-box;">
            <div style="font-size: 11px;text-transform: uppercase;">Final do Hodometro</div>
            <div style="font-size: 12px;font-weight: bold;color: #007eff;margin-top: 3px;">{{$trip['endOdometer']/1000}} km</div>
        </div>

        <div style="clear: both;"></div>

        <div style="border-bottom: silver 1px dotted;border-right: silver 1px dotted;float: left;width: 50%;text-align: center;padding-top: 15px;padding-bottom: 15px;box-sizing: border-box;">
            <div style="font-size: 11px;text-transform: uppercase;">DISTANCIA TOTAL</div>
            <div style="font-size: 12px;font-weight: bold;color: #007eff;margin-top: 3px;">{{$trip['distance']/1000}} km</div>
        </div>
        <div style="border-bottom: silver 1px dotted;float: right;width: 50%;text-align: center;padding-top: 15px;padding-bottom: 15px;box-sizing: border-box;">
            <div style="font-size: 11px;text-transform: uppercase;">tempo total</div>
            <div style="font-size: 12px;font-weight: bold;color: #007eff;margin-top: 3px;">{{(($trip['duration']/60)/60)/1000}} hs</div>
        </div>

        <div style="clear: both;"></div>

        <div style="text-align: center;padding: 10px;padding-left: 20px;box-sizing: border-box;">
            <div style="font-size: 11px;text-transform: uppercase;float: left;">
                {{date("d/m/Y",strtotime($trip['endTime']))}}<br>
                {{date("H:i",strtotime($trip['endTime']))}}
            </div>
            <div style="font-size: 11px;text-transform: uppercase;margin-left: 100px;position: relative;">
                <div style="position: absolute;left: -20px;top: 0px;">
                    <div style="background: url('https://demo.tarkan.app/img/flag-end.png');background-size: cover;width: 15px;height: 15px;"></div>
                </div>

                @if($trip['endAddress'])
                    {{$trip['endAddress']}}
                @else
                    {{$trip['endLat']}}, {{$trip['endLon']}}
                @endif
            </div>
            <div style="clear: both;"></div>
        </div>

    </div>
    <div style="float: right;width: 50%;height: 290px;background-size: cover;background-position: center;background-image: url('https://maps.googleapis.com/maps/api/staticmap?size=500x300&maptype=roadmap%20&markers=color:blue%7Clabel:S%7C{{$trip['startLat']}},{{$trip['startLon']}}&markers=color:blue%7Clabel:E%7C{{$trip['endLat']}},{{$trip['endLon']}}&key={{ config('services.google_maps.key') }}&path={{$trip['points']}}');">


    </div>

    <div style="clear: both;"></div>
</div>
    @endforeach

    <div style="page-break-before: always;"></div>

@empty
    <div style="padding: 10px;margin-bottom: 20px;">
        <div style="font-size: 12px;float: left;">NENUM DISPOSITIVO ENCONTRADO</div>
        <div style="clear: both;"></div>
    </div>
@endforelse

</body>
</html>
