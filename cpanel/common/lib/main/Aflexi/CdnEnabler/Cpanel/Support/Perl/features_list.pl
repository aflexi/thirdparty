#!/usr/local/bin/perl

package main;

use FindBin '$Bin';

BEGIN{
    require $Bin."/bootstrap.pl";
}

use base Cpanel::Lite::JsonWrapper;
use Cpanel::Features;


my $m;

my %result = ();

foreach(Cpanel::Features::get_feature_lists()){
        $result{$_}=$_;
}

$m = new main();
$m->write({
   'result' => \%result
});
__END__
