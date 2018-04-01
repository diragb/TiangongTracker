<!DOCTYPE html>
<html lang="en">
<head>
<meta name="viewport" content="width=device-width" />
<link href='https://fonts.googleapis.com/css?family=Roboto:light' rel='stylesheet'>
<style>
body {
    font-family: 'Roboto'; font-size: 22px; color: white;
}
@media screen and (max-device-width : 320px)
{
  body
  {
    font:22px;
  }
}
@media screen and (max-device-width : 1204px)
{
  body
  {
    font:90px;
  }
}
</style>
<title>Tiangong Falls</title>
<meta name="description" content="Tiangong - 1 falls."/>
<script type="text/javascript" src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
    <script type="text/javascript" src="scripts/standard.min.js"></script>
    <script type="text/javascript">
        function updateLocalTime(utc) {
            var localdiff = parseInt($('#utcOffset').val());
            var local_now = new Date(utc.getTime() + localdiff);
            $('#spanTime').text(formatTime(local_now));
        }

        $(function () {
            onClockTick = updateLocalTime;
            startClock();
        });
    </script>
    <style>
        .navbar {
            min-height: 0px;
            margin-bottom: 5px;
        }
    </style>
    
    <script src="scripts/moment.min.js"></script>
    <script src="scripts/numeral.min.js"></script>
    <script src="scripts/astrolib.min.js"></script>
    <script src="scripts/orbit-displays.min.js"></script>
    <script>
    	var playMusic0 = false;
    	var playMusic1 = false;
    	var lowestHeight = 100;
        var tles;

        $(function () {
            $.getJSON('api/tles/37820.json',
                function (data) {
                    tles = data;
                    initGroundTrack();
                }
            );
        });

        var earthImage = new Image();
        var gtd;
        earthImage.onload = function () {
            initGroundTrack();
        };
        earthImage.src = 'images/earthmap2k.jpg';

        var issIcon = new Image();
        issIcon.onload = function () {

            initGroundTrack();
        };
        issIcon.src = 'images/iss.png';

        var gsIcon = new Image();
        gsIcon.onload = function () {

            initGroundTrack();
        };
        gsIcon.src = 'images/groundstation32.png';

        var defaultIcon = new Image();
        defaultIcon.onload = function () {

            initGroundTrack();
        };
        defaultIcon.src = 'images/default.png';

        function initGroundTrack() {

            if (!issIcon.complete || !defaultIcon.complete || !earthImage.complete || !tles || !gsIcon.complete)
                return;

            var canv = document.getElementById('mycanvas');

            var sat = {
                tle: tles[0].elements,
                orbitsToShow: 1,
                icon: defaultIcon,
                orbitColor: 'white',
                text: 'Tiangong 1'
            }

            var whm = {
                latitude: 0,
                longitude: 0,
                name: 'Unspecified',
                zoneColor: '#ff0000',
                zoneBorderColor: '#f00',
                iconOffset: [0, 8]
            }

            var options = {
                canvas: canv,
                time: new Date().getTime(),
                earthImage: earthImage,
                primarySatellite: sat,
                groundStations: [whm],
                showSun: true,
                showEquator: true,
                showDayShading: true,
                showSAA: false,
                saaText: 'South Atlantic Anomaly',
                font: '14px Arial',
                secondarySatellites: []
            };

            gtd = new OrbitDisplays.GroundTrackDisplay(options);

            renderGroundTrack();

            setInterval(renderGroundTrack, 2000);
        }

        function renderGroundTrack() {
            gtd.time = new Date().getTime();
            gtd.render();

            var orbit = new AstroLib.Sgp4Orbit(gtd.primarySatellite.tle);
            var latlng = orbit.getLatLng(gtd.time);
            var satHeight = orbit.getHeight(gtd.time);
            if (satHeight <= lowestHeight) {
            	playMusic0 = true;
            }
            else if (satHeight > lowestHeight) {
            	playMusic0 = false;
            	if (playMusic1 == true) {
        		var ele = document.getElementById("musik");
            		ele.innerHTML = '';
            		playMusic1 = false;
            	}
            }
            if (playMusic0) {
            	if (playMusic1 == false) {
        		var ele = document.getElementById("musik");
            		ele.innerHTML = "<embed src='music/MozartEineKleineNachtmusik.mp3' autostart='true' type='audio/mpeg' loop='true' height='70' width='300'/><br/><br/><p style='color: black;font-size: 20px;text-align: center;'>IT'S FUCKING DONE FOR, MATE.";
            		playMusic1 = true;
            	}
       	    }
            $('#spanHeight').text(satHeight.toFixed(1));
            $('#spanPos').text(formatPos(latlng[0], latlng[1]));
            isTiangongClose(latlng[0], latlng[1]);
            $('#spanLng').text(latlng[1].toFixed(1));

        }

        function formatPos(lat, lng) {
            var latstr, lngstr;
            if (lat >= 0)
                latstr = lat.toFixed(1) + ' \u00B0N';
            else
                latstr = (-lat).toFixed(1) + ' \u00B0S';

            if (lng >= 0)
                lngstr = lng.toFixed(1) + ' \u00B0E';
            else
                lngstr = (-lng).toFixed(1) + ' \u00B0W';

            return latstr + ', ' + lngstr;
        }
        
        var userLat, userLong;
        var diffLat, diffLong;
        
        function isTiangongClose (lat, lng) {
            var latClose = false, longClose = false;
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(showUserLoc);
                
                if (parseInt(userLat) <= 0 && lat <= 0) {
                    diffLat = parseInt(userLat) - lat;
                }
                else if (parseInt(userLat) >= 0 && lat >= 0) {
                    diffLat = parseInt(userLat) - lat;
                }
                else if (parseInt(userLat) > 0 && lat < 0) {
                    diffLat = parseInt(userLat) - lat;
                }
                else if (parseInt(userLat) < 0 && lat > 0) {
                    diffLat = parseInt(userLat) - lat;
                }
                
                
                if (parseInt(userLong) <= 0 && lng <= 0) {
                    diffLong = parseInt(userLong) - lng;
                }
                else if (parseInt(userLong) >= 0 && lng >= 0) {
                    diffLong = parseInt(userLong) - lng;
                }
                else if (parseInt(userLong) > 0 && lng < 0) {
                    diffLong = parseInt(userLong) - lng;
                }
                else if (parseInt(userLong) < 0 && lng > 0) {
                    diffLong = parseInt(userLong) - lng;
                }
                
                if (diffLat >= -13.5 && diffLat <= 13.5) {
                    // Lat Proximity
                    latClose = true;
                }
                if (diffLong >= -13.5 && diffLong <= 13.5) {
                    // Long Proximity
                    longClose = true;
                }
                if (latClose && longClose) {
                    TiangongIsClose();
                }
            }
            
        }
        
        function showUserLoc(position) {
             userLat = position.coords.latitude.toFixed(1);
             userLong = position.coords.longitude.toFixed(1);
        }
        
        function TiangongIsClose() {
            if (!isNaN(diffLat)) {
                if (!isNaN(diffLong)) {
                    var diffLatRast = diffLat.toFixed(1);
                    var diffLongRast = diffLong.toFixed(1);
                    var x = document.getElementById("proximity");
                    x.innerHTML = "<br/><br/>Hey dude, trust me on this.<br/>Tiangong - 1 is REALLY close to your location.<br/><font style='font-size: 15px;'>Latitudinal difference: " + diffLatRast + "<br/>Longitudinal difference: " + diffLongRast + "</font>";
                }
            }
        }
    </script>
</head>
<body>
<p id="proximity" align="center" style="text-align: center; color: black;"></p>
<div id="musik" align="center">
</div>
<div id="boss" align="center" style="position: relative; top: 100%; transform: translateY(100%); background: linear-gradient(45deg, #ec407a, #0277bd);">
<br/>
<div style="font-size: 15px;">Tiangong - 1's current height: </div>
<span id="spanHeight">0</span> kilometres
<br/><br/>
<div style="font-size: 15px;">Tiangong - 1's current LatLong: </div>
<span id="spanPos">00.0, 00.0</span>
<div class="container">
        <canvas style="width:0%" width="900" height="450" id="mycanvas">
        </canvas>
</div>
</div>
<br/><br/><br/><br/><br/><br/><br/>
<p style="color: black; font-size: 13px; text-align: center;">If shit dips below 100 kilometres,<br/> it's fucking done for. (id est, re-entry)</p>
<br/><br/><br/>
<br/><br/><br/><br/>
<div style="background:#ebebeb;text-align: center; margin-left:-2.2%; margin-right:-2.2%; margin-bottom: 0px; height: 450px;">
	<div style="height:50px;"></div>
	<div style="display: inline-block; text-align: left;">
	<p style="color: black; font-size: 13px;"><strong>Thanks to HeavensAbove for the information.</strong></p>
	<br/>
	<p style="color: black; font-size: 12px;">
	Hi, I am Dirag Biswas. I made this because<br/>
	I'm kinda comfortable with JavaScript;<br/>
	and since because my exams just ended, I<br/>
	figured that the best way to make use of my<br/>
	free time (before I'm sent back to prison)<br/>
	was to see shit falling apart from the sky.<br/>
	<br/>
	This nifty piece of shit extracts data from<br/>
	the math libraries of HeavensAbove, and makes sure<br/>
	that whenever the altitude of the space-station goes<br/>
	<strong>EEH</strong>, id est, below 100 km,<br/>
	it plays Mozart's Eine Kleine Nachtmusik.<br/>
	<br/>
	Because why the fuck not?
	<br/>
	<br/>
	</p>
	<p style="color: black; font-size: 13px;"><strong>
	The space-station, by the way,<br/>
	is the ex-Chinese Space Station, Tiangong - 1.
	</strong>
	</p>
    </div>
</div>
</body>
</html>
