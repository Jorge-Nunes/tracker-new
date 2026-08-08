

import "firebase/app"
import "firebase/messaging"
import firebase from "firebase/compat";

let m = false;


function isBadIosXX() {
	var regex = /(iPhone|iPad|iPod);[^OS]*OS (\d)/;
	var matches = navigator.userAgent.match(regex);

	if (!matches) return false;
	return true;
}

// eslint-disable-next-line no-undef
console.log(isBadIosXX());

// eslint-disable-next-line no-undef
if(window.location.protocol==='https:' && !isBadIosXX()){

	// eslint-disable-next-line no-undef
	if(firebaseConfig){
		// eslint-disable-next-line no-undef
		firebase.initializeApp(firebaseConfig)
		m = firebase.messaging();
	}


}
export default m