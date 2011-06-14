#!/usr/local/bin/perl

package main;

use FindBin '$Bin';

BEGIN{
    require $Bin."/bootstrap.pl";
}

use base Cpanel::Lite::JsonWrapper;
use Cpanel::AcctUtils::Domain;

my $m;

$m = new main();
my $user = ( getpwuid($>) )[0];
$m->write({
   'result' =>  Cpanel::AcctUtils::Domain::getdomain('yclian')
});
__END__


