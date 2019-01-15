# Sync iCals from Airbnb, Booking and more

1. Upload files to your server (use a subdomain)
2. Enter your urls for your calendars (booking, airbnb, atraveo)
3. Check if you the script has writing permission to create the .ics file
4. Run http://sub.domain.com/icalmerger.php
5. Check for errors
6. Check your generated ical http://sub.domain.com/ics/allservices.ics
7. If everything is fine, create a cronjob to run the icalmerger.php
8. Copy the allservices.ics and import it to your services 
9. There is still going to be delay, as all services refresh on different rates

This script is using the php ELUCEO Library by Markus Poerschke