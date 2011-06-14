#!/usr/local/bin/perl

package main;

use FindBin '$Bin';

BEGIN{
    require $Bin."/bootstrap.pl";
}

use base Cpanel::Lite::JsonWrapper;
use Cpanel::Hostname;


my $m;

$m = new main();
$m->write({
   hostname =>  Cpanel::Hostname::gethostname()
});
__END__


