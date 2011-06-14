#!/usr/local/bin/perl

package main;

use FindBin '$Bin';

BEGIN{
    require $Bin."/bootstrap.pl";
}

use base Cpanel::Lite::JsonWrapper;
use Whostmgr::Bandwidth;

my $m;

$m = new main();

my $bandwidth_limits = {};

Whostmgr::Bandwidth::loaduserbwlimits($bandwidth_limits, 1, 1);
while(my ($k, $v) = each %$bandwidth_limits){
    $bandwidth_limits->{$k} = $v eq 'unlimited' ? 0 : int($v);
}

$m->write({
    'bandwidth_limits' => $bandwidth_limits
});

__END__

