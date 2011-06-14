#!/usr/local/bin/perl

package main;

use FindBin '$Bin';

BEGIN{
    require $Bin."/bootstrap.pl";
}

use base Cpanel::Lite::JsonWrapper;
use Whostmgr::Packages;

my $m;

$m = new main();

my $packages = Whostmgr::Packages::fetch_package_list('all');
my $bandwidth_limits = {};

while(my ($k, $v) = each %$packages){
    $bandwidth_limits->{$k} = defined($v->{'BWLIMIT'}) ? 
       $v->{'BWLIMIT'} eq 'unlimited' ?
            0 : int($v->{'BWLIMIT'}) :
       0;
}

$m->write({
    'bandwidth_limits' => $bandwidth_limits
});

__END__

