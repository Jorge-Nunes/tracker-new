import i18n from "../../lang/";





export default function(t,d){
    const it = i18n.global.t(t);

    if(it){
        let tmp = it;
        if(d) {
            for (const st of Object.keys(d)){

                tmp = tmp.replace('%'+st+'%',d[st]);
            }
        }


        return tmp;
    }else{
        return '_KT_'+t+'_KT_';
    }


}