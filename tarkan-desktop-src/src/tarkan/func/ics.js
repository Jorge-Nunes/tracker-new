
const dtStart = /DTSTART;(VALUE=DATE:(?<date>\d*)|TZID=(?<tzid>.*):(?<tzDate>\d*)T(?<tzTime>\d*))/gm;
const dtEnd = /DTEND;(VALUE=DATE:(?<date>\d*)|TZID=(?<tzid>.*):(?<tzDate>\d*)T(?<tzTime>\d*))/gm;
const summary = /SUMMARY:([A-Z, 0-9a-z]*)/gm;
const rRule = /RRULE:(.*)/gm;

const rFreq = /FREQ=([A-Z]*)/gm;
const rWKST = /WKST=([A-Z]*)/gm;
const rUntil = /UNTIL=([0-9]*)/gm;
const rCount = /COUNT=([0-9]*)/gm;
const rByDay = /BYDAY=([A-Z,]*)/gm;
const rByMonthDay = /BYMONTHDAY=([0-9,]*)/gm;


function findEventData(str){


    let tmp = {};

    tmp.summary = new RegExp(summary).exec(str)[1]

    const _dtStart = new RegExp(dtStart).exec(str);
    if(_dtStart[4] && _dtStart[5]){
        tmp.startDate = _dtStart[4].substring(6,8)+'/'+_dtStart[4].substring(4,6)+'/'+_dtStart[4].substring(0,4);
        tmp.startTime = _dtStart[5].substring(0,2)+':'+_dtStart[5].substring(2,4);


        tmp.startDateTime = new Date(_dtStart[4].substring(0,4)+'/'+_dtStart[4].substring(4,6)+'/'+_dtStart[4].substring(6,8)+" "+tmp.startTime);

    }else{
        tmp.startDate = _dtStart[2].substring(6,8)+'/'+_dtStart[2].substring(4,6)+'/'+_dtStart[2].substring(0,4);
        tmp.startTime = '00:00';

        tmp.startDateTime = new Date(_dtStart[2].substring(0,4)+'/'+_dtStart[2].substring(4,6)+'/'+_dtStart[2].substring(6,8)+" "+tmp.startTime);


    }



    const _dtEnd = new RegExp(dtEnd).exec(str);


    console.log(_dtEnd);

    if(_dtEnd[4] && _dtEnd[5]){
        tmp.endDate = _dtEnd[4].substring(6,8)+'/'+_dtEnd[4].substring(4,6)+'/'+_dtEnd[4].substring(0,4);
        tmp.endTime = _dtEnd[5].substring(0,2)+':'+_dtEnd[5].substring(2,4);


        tmp.endDateTime = new Date(_dtEnd[4].substring(0,4)+'/'+_dtEnd[4].substring(4,6)+'/'+_dtEnd[4].substring(6,8)+" "+tmp.endTime);

    }else{

        if(_dtEnd[5]){
            tmp.endTime = _dtEnd[5];
        }else{

            tmp.endTime = '23:59';
        }

        if(_dtEnd[2] && !_dtEnd[4]) {
            tmp.endDate = _dtEnd[2].substring(6, 8) + '/' + _dtEnd[2].substring(4, 6) + '/' + _dtEnd[2].substring(0, 4);

            tmp.endDateTime = new Date(_dtEnd[2].substring(0,4)+'/'+_dtEnd[2].substring(4,6)+'/'+_dtEnd[2].substring(6,8)+" "+tmp.endTime);

        }else{
            tmp.endDate = '';
            tmp.endDateTime = new Date(tmp.startDateTime)
            tmp.endDateTime.setHours(23);
            tmp.endDateTime.setMinutes(59);
        }
    }

    const _rRule = new RegExp(rRule).exec(str);
    if(_rRule){
        tmp.rules = {};

        tmp.rules.frequency = new RegExp(rFreq).exec(_rRule[1])[1];

        const WKST = new RegExp(rWKST).exec(_rRule[1])
        tmp.rules.WKST = (WKST)?WKST[1]:'';

        const until = new RegExp(rUntil).exec(_rRule[1]);
        tmp.rules.until = (until)?new Date(until[1]*1000):'';

        const count = new RegExp(rCount).exec(_rRule[1]);
        tmp.rules.count = (count)?count[1]:'';

        if(tmp.rules.until!==''){
            tmp.rules.endMode = 1;
        }else if(tmp.rules.count!==''){
            tmp.rules.endMode = 2;
        }else{
            tmp.rules.endMode = 0;
        }

        const days = new RegExp(rByDay).exec(_rRule[1]);
        tmp.rules.days = (days)?days[1].split(","):'';

        const monthDay = new RegExp(rByMonthDay).exec(_rRule[1])
        tmp.rules.monthDay = (monthDay)?monthDay[1]:'';

    }else{
        tmp.rules = false;
    }

    return tmp;
}


function findEvents(str){
    const regex = /BEGIN:VEVENT([\w\W]*?)END:VEVENT/gm;

    let m;
    let tmp = [];

    while ((m = regex.exec(str)) !== null) {

        tmp.push(findEventData(m[0]));

        // This is necessary to avoid infinite loops with zero-width matches
        if (m.index === regex.lastIndex) {
            regex.lastIndex++;
        }

    }

    return tmp;
}



export function parseIcs(data){
    return findEvents(data);
}


export function toIcs(data){
    let str = 'BEGIN:VCALENDAR\n' +
        'PRODID:-//Tarkan//KoreAG//EN\n' +
        'VERSION:2.0\n' +
        'CALSCALE:GREGORIAN\n' +
        'METHOD:PUBLISH\n' +
        'X-WR-CALNAME:TRACCAR\n' +
        'X-WR-TIMEZONE:UTC\n' +
        'BEGIN:VTIMEZONE\n' +
        'TZID:America/Sao_Paulo\n' +
        'X-LIC-LOCATION:America/Sao_Paulo\n' +
        'BEGIN:STANDARD\n' +
        'TZOFFSETFROM:-0300\n' +
        'TZOFFSETTO:-0300\n' +
        'TZNAME:-03\n' +
        'DTSTART:19700101T000000\n' +
        'END:STANDARD\n' +
        'END:VTIMEZONE\n';

    data.forEach((d)=>{
        const dtStart = new Date(d.startDateTime);
        const dtEnd = new Date(d.endDateTime);
        const strStart = dtStart.toISOString().replaceAll('-','').replaceAll(':','').substr(0,15);
        const strEnd = dtEnd.toISOString().replaceAll('-','').replaceAll(':','').substr(0,15);

        str+='BEGIN:VEVENT\n' +
            'DTSTART;TZID=America/Sao_Paulo:'+strStart+'\n' +
            'DTEND;TZID=America/Sao_Paulo:'+strEnd+'\n'

            if(d.rules) {

                str+='RRULE:FREQ='+d.rules.frequency+';';

                if(d.rules.frequency==='WEEKLY'){
                    str+='BYDAY='+d.rules.days.join(",")+';';
                }else if(d.rules.frequency==='MONTHLY'){
                    str+='BYMONTHDAY='+dtStart.getDate()+';';
                }

                if(d.rules.expires===1){
                    str+='COUNT='+d.rules.count+';'
                }else if(d.rules.expires===2){

                    const tmp = d.rules.until.toISOString().replaceAll('-','').split("T");

                    str+='UNTIL='+tmp[0]+';'
                }

                str+='\n';

            }
            str+='DTSTAMP:20220128T131146Z\n' +
            'UID:4ttr41esn4jnde6hfa80q7qq7p@google.com\n' +
            'CREATED:20220128T131120Z\n' +
            'DESCRIPTION:\n' +
            'LAST-MODIFIED:20220128T131120Z\n' +
            'LOCATION:\n' +
            'SEQUENCE:0\n' +
            'STATUS:CONFIRMED\n' +
            'SUMMARY:VAU\n' +
            'TRANSP:OPAQUE\n' +
            'END:VEVENT\n';
    });



    str +='END:VCALENDAR';



    return str;
}