<HTML>
<HEAD>
</HEAD>

<BODY>
<script type="text/javascript" src="http://www.google.com/jsapi"></script>
<script type="text/javascript">



<!--
/* Loads the Google data JavaScript client library */
google.load("gdata", "2.x");

function init() {
  // init the Google data JS client library with an error handler
  google.gdata.client.init(handleGDError);
}

/**
 * Adds a leading zero to a single-digit number.  Used for displaying dates.
 */
function padNumber(num) {
  if (num <= 9) {
    return "0" + num;
  }
  return num;
}

/**
 * Callback function for the Google data JS client library to call when an error
 * occurs during the retrieval of the feed.  Details available depend partly
 * on the web browser, but this shows a few basic examples. In the case of
 * a privileged environment using ClientLogin authentication, there may also
 * be an e.type attribute in some cases.
 *
 * @param {Error} e is an instance of an Error 
 */
function handleGDError(e) {
   if (e.cause.status != 400) { /* don't know what is causing the http 400 error, but it doesn't seem to affect the results */
     document.getElementById('calendarTitle').setAttribute('style', 
         'display:none');
     if (e instanceof Error) {
       /* alert with the error line number, file and message */
       alert('Error at line ' + e.lineNumber +
             ' in ' + e.fileName + '\n' +
             'Message: ' + e.message);
       /* if available, output HTTP error code and status text */
       if (e.cause) {
         var status = e.cause.status;
         var statusText = e.cause.statusText;
         alert('Root cause: HTTP error ' + status + ' with status text of: ' + 
               statusText);
       }
     } else {
       alert(e.toString());
    }
  } 
}


function sortByRoom(a, b) {

    var x = a.gsx$room.$t.toLowerCase() + "/"
	+ a.gsx$date.$t.split("/")[2]+ "/"  
	+ padNumber(a.gsx$date.$t.split("/")[0]) + "/"  
	+ padNumber(a.gsx$date.$t.split("/")[1]) + "/"
	+ padNumber(a.gsx$starttime.$t.split(":")[0]) + "/"
	+ padNumber(a.gsx$starttime.$t.split(":")[1]);

    var y = b.gsx$room.$t.toLowerCase() + "/"
	+ b.gsx$date.$t.split("/")[2]+ "/"  
	+ padNumber(b.gsx$date.$t.split("/")[0]) + "/"  
	+ padNumber(b.gsx$date.$t.split("/")[1]) + "/"
	+ padNumber(b.gsx$starttime.$t.split(":")[0]) + "/"
	+ padNumber(b.gsx$starttime.$t.split(":")[1]);


    return ((x < y) ? -1 : ((x > y) ? 1 : 0));
}



function findRoomindex (roomname,json) {

/*alert(json.feed.entry.gsx$location.indexOf(roomname));*/


   var len = json.feed.entry.length;

   for (i=0;i<json.feed.entry.length;i++){
      if (json.feed.entry[i].gsx$location.$t==roomname) return i;
   }
   return -1;
}    


function displayConfig(json) { 

   scriptObj=document.createElement("script");
   document.body.appendChild(scriptObj);
   scriptObj.setAttribute('type','text/javascript');
   var scriptstring = "";

   var styleObj=document.createElement("style");
   document.getElementsByTagName("head")[0].appendChild(styleObj);

   bodydiv=document.createElement('div');
   bodydiv.setAttribute('id','maindiv');

/*   bodydiv.setAttribute('style','-webkit-transform:rotate(-10.329027deg) skew(18.50194131deg);-moz-transform:rotate(-10.329027deg) skew(18.50194131deg);-o-transform:rotate(-10.329027deg) skew(18.50194131deg)'); */
   document.body.appendChild(bodydiv);   

   var bodystring = "";
   for (i=0;i<json.feed.entry.length;i++){
      if (json.feed.entry[i].gsx$type.$t == "script") {
         scriptstring += json.feed.entry[i].gsx$html.$t+"\n";
      }
 
      if (json.feed.entry[i].gsx$type.$t == "style" ) {
         styleObj.appendChild(document.createTextNode(json.feed.entry[i].gsx$html.$t));
      }

      if (json.feed.entry[i].gsx$type.$t == "body") {

         bodystring += json.feed.entry[i].gsx$html.$t+"\n";
         
      }


   }
   
   bodydiv.innerHTML=bodystring;
   scriptObj.innerHTML=scriptstring;


/* document.getElementById('headline').innerHTML = json.feed.entry[0].gsx$html.$t; */

}

function pausecomp(millis) 
{
var date = new Date();
var curDate = null;

do { curDate = new Date(); } 
while(curDate-date < millis);
} 


function displaySchedule(json) { 

  entries = json.feed.entry;

  entries.sort(sortByRoom);
  var shortmonths =["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec"];
  var len = entries.length;

  var roomArray = new Array(20);
  var j = 0;
  var maxj = 0;


  var room = "";

  for (var i = 0; i < len; i++) {
    var entry = entries[i];
    var title = entry.gsx$topic.$t;
    var startDateTime = null;
    var startJSDate = null;
    var endDateTime = null;
    var endJSDate = null;
    startDateTime = entry.gsx$starttime.$t.split(":")[0]+":"+entry.gsx$starttime.$t.split(":")[1];
    startJSDate = entry.gsx$date.$t;
    endDateTime = entry.gsx$endtime.$t.split(":")[0]+":"+entry.gsx$endtime.$t.split(":")[1];
    endJSDate = entry.gsx$date.$t;

    var startdateString=startJSDate;
    var enddateString=startJSDate;

    var description = entry.gsx$speakeranddescription.$t;

    var cal_location = entry.gsx$room.$t ;
    if (cal_location == "") {cal_location = "To Be Assigned";} 

    if (cal_location != room) {
       
       var roomxdiv = document.createElement('div');

       roomArray[j] = roomxdiv;
       j++;
       maxj = j;


/*       roomxdiv.addEventListener('touchstart', function(){this.className = "hover";}, false);
       roomxdiv.addEventListener('touchend', function(){currentobject=this; setTimeout('currentobject.className = "";',2000)}, false) */

       roomxdiv.addEventListener('click', function(){var previousclass = this.className ; for (var k = 0 ; k < maxj ; k++ ) { roomArray[k].className = ""; }; if (previousclass == "") {this.className = "hover"}} ,false)

       roomxdiv.setAttribute('id',cal_location.replace(/ /g,"-")); 

       document.getElementById('maindiv2').appendChild(roomxdiv);


       var roomtextdiv = roomxdiv.appendChild(document.createElement('div'));

       roomtextdiv.setAttribute('id','roomtext');

       var roomname = roomtextdiv.appendChild(document.createElement('div'));
       roomname.setAttribute('id','roomname');
       roomname.appendChild(document.createTextNode(cal_location)); 
       room = cal_location;
    }
    evententry = roomtextdiv.appendChild(document.createElement('div'));
    evententry.setAttribute('id','event');


    var eventtime = evententry.appendChild(document.createElement('div'));
    eventtime.setAttribute('id','time');
    eventtime.appendChild(document.createTextNode( startDateTime+ '-' + endDateTime ));

    var eventtitle = evententry.appendChild(document.createElement('div'));
    eventtitle.setAttribute('id','title');
    eventtitle.appendChild(document.createTextNode(title));

    var descriptionevent =  evententry.appendChild(document.createElement('div'));
    descriptionevent.setAttribute('id','description');
    descriptionevent.appendChild(document.createTextNode(description));

  }
  document.getElementById('maindiv').addEventListener('click', function(){for (var k = 0 ; k < maxj ; k++ ) { roomArray[k].className = ""; }} ,true)



}



google.setOnLoadCallback(init);
//-->
</script>



<script type="text/javascript"
src="http://spreadsheets.google.com/feeds/list/0AsSgLD5gOC2OdE5TQXN4TC0wTHJGMUVLMVdRMktaV3c/od6/public/values?alt=json-in-script&callback=displayConfig">
</script>

<script type="text/javascript"
src="http://spreadsheets.google.com/feeds/list/0AsSgLD5gOC2OdHpWVUlBeUxOdUptemJQR2JKeFdCZVE/od6/public/values?alt=json-in-script&callback=displaySchedule">
</script>



</BODY
</HTML>