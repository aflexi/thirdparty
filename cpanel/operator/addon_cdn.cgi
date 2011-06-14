#!/usr/bin/perl
#WHMADDON:cdn:Aflexi CDN
print "Status: 301 Moved\n";
print "Location: ".$ENV{'cp_security_token'}."/aflexi/index.php\n\n";
