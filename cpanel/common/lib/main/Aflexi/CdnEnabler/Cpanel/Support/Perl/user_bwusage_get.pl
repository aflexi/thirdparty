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

my $bandwidth_usage = undef;
my $user_name = $m->{inputs}->{user_name};
my $month = $m->{inputs}->{month};
my $year = $m->{inputs}->{year};

$bandwidth_usage = Cpanel::BandwidthUsage::getmonthbwusage($user_name, $month, $year);

$m->write({
    'bandwidth_usage' => $bandwidth_usage
});

__END__
