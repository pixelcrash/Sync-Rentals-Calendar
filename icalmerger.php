<?php
// LOAD AUTOLOAD

require_once __DIR__ . '/vendor/autoload.php';
// SET YOUR TIMEZONE
date_default_timezone_set('Europe/Vienna');
// SET YOUR DOMAIN NAME
$vCalendar = new \Eluceo\iCal\Component\Calendar('www.yourdomain.com');
$cyear = date("Y");

$bookings = array();
// SET YOUR ICALS URL
$ical_airbnb = "https://www.airbnb.com/calendar/ical/xxx.ics?s=xxx";
$ical_atraveo = "https://owner.atraveo.com/accommodation/calendar/xxx/ical.ics?securitytoken=xxx";
$ical_booking = "https://admin.booking.com/hotel/hoteladmin/ical.html?t=xxx";



// ATREAVEO
if($ical_atraveo):
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $ical_atraveo);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
$output = curl_exec($ch);
curl_close($ch);

echo "<h2>ATRAVEO</h2>";
preg_match_all('/BEGIN:VEVENT(.*?)END:VEVENT/s', $output, $matches);
foreach($matches[1] as $event):
  $SUMMARY = "ATRAVEO";
  $event_line = preg_split('/\s+/', $event);
  $DTSTART = str_replace("DTSTART;TZID=CET:","",$event_line[2]);
  $DTSTART = str_replace("T110000","",$DTSTART);
  $DTSTART = join('-', str_split($DTSTART, 4));
  $DTSTART = join('-', str_split($DTSTART, 7));
  
  $DTEND = str_replace("DTEND;TZID=CET:","",$event_line[5]);
  $DTEND = str_replace("T110000","",$DTEND);
  $DTEND = join('-', str_split($DTEND, 4));
  $DTEND = join('-', str_split($DTEND, 7));
  
  $uid = $DTSTART . "x" . $DTEND;
  $year = substr($DTSTART, 0, 4);
  
  if($year >= $cyear):
    
  if(!in_array($uid, $bookings)):
  $bookings[$uid] = array(
    "start" => $DTSTART,
    "end" => $DTEND,
    "summary" => $SUMMARY);
  endif;
    
  echo "<br>";
  echo $DTSTART . " - " . $DTEND . " - " . $SUMMARY;
  echo "<br>";
  
  $vEvent = new \Eluceo\iCal\Component\Event();
  $vEvent->setDtStart(new \DateTime($DTSTART));
  $vEvent->setDtEnd(new \DateTime($DTEND));
  $vEvent->setNoTime(true);
  $vEvent->setSummary($SUMMARY);
  $vEvent->setUseTimezone(true);
  $vCalendar->addComponent($vEvent);
  unset($vEvent);
  endif;
   
endforeach;
endif;

// END ATRAVEO



// AIRBNB
if($ical_airbnb):
echo "<h2>AIRBNB</h2>";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $ical_airbnb);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
$output = curl_exec($ch);
curl_close($ch);

preg_match_all('/BEGIN:VEVENT(.*?)VEVENT/s', $output, $matches);
foreach($matches[1] as $event):
  $event_line = preg_split('/\s+/', $event);
  
  /*  echo "<pre>"; var_dump($event); echo "</pre>"; */
  
  preg_match_all('/SUMMARY:(.*?)LOCATION:/s', $event, $descriptions);
  
  
  $DTSTART = str_replace("DTSTART;VALUE=DATE:","",$event_line[2]);
  $DTSTART = join('-', str_split($DTSTART, 4));
  $DTSTART = join('-', str_split($DTSTART, 7));
  
  $DTEND = str_replace("DTEND;VALUE=DATE:","",$event_line[1]);
  $DTEND = join('-', str_split($DTEND, 4));
  $DTEND = join('-', str_split($DTEND, 7));

  $year = substr($DTSTART, 0, 4);
  $SUMMARY = "AIRBNB";
  $uid = $DTSTART . "x" . $DTEND;
  
  if(isset($descriptions[1][0])): $descriptions[1][0] = $descriptions[1][0]; else: $descriptions[1][0] = "no Summary"; endif;
  
  if($year >= $cyear):
    
  if(!in_array($uid, $bookings)):
  $bookings[$uid] = array(
    "start" => $DTSTART,
    "end" => $DTEND,
    "summary" => $SUMMARY);
  endif;
  
  echo "<br>";
  echo $DTSTART . " - " . $DTEND . " - " . $SUMMARY;
  echo "<br>";
  unset($vEvent);
  endif;

endforeach;
endif;
// END AIRBNB

// BOOKING
if($ical_booking):
echo "<h2>BOOKING</h2>";
$output = file_get_contents($ical_booking);

preg_match_all('/BEGIN:VEVENT(.*?)VEVENT/s', $output, $matches);
foreach($matches[1] as $event):
  $event_line = preg_split('/\s+/', $event);  
  
  $DTSTART = str_replace("DTSTART;VALUE=DATE:","",$event_line[1]);
  $DTSTART = join('-', str_split($DTSTART, 4));
  $DTSTART = join('-', str_split($DTSTART, 7));
  
  $DTEND = str_replace("DTEND;VALUE=DATE:","",$event_line[2]);
  $DTEND = join('-', str_split($DTEND, 4));
  $DTEND = join('-', str_split($DTEND, 7));
  
  $year = substr($DTSTART, 0, 4);
  
  if($year >= $cyear):
  $SUMMARY = "Booking";
  
  $uid = $DTSTART . "x" . $DTEND;
  
  if(!in_array($uid, $bookings)):
  $bookings[$uid] = array(
    "start" => $DTSTART,
    "end" => $DTEND,
    "summary" => $SUMMARY);
  endif;
  
  echo "<br>";
  echo $DTSTART . " - " . $DTEND . " - " . $SUMMARY;
  echo "<br>";
  
  endif;

endforeach;
endif;
// END BOOKING

//echo "<hr>";
//echo "<pre>"; var_dump($bookings); echo "</pre>";

foreach($bookings as $booking):
  $vEvent = new \Eluceo\iCal\Component\Event();
  $vEvent->setDtStart(new \DateTime($booking['start']));
  $vEvent->setDtEnd(new \DateTime($booking['end']));
  $vEvent->setNoTime(true);
  $vEvent->setSummary($booking['start']);
  $vEvent->setUseTimezone(true);
  $vCalendar->addComponent($vEvent);
endforeach;

$mergeCalendar = fopen(__DIR__ . "/ics/allservices.ics", "w") or die("Unable to open file!");
fwrite($mergeCalendar, $vCalendar->render());
fclose($mergeCalendar);

echo "<h1>Alle Kalender aktualisiert</h1>";

?>