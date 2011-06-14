#!/usr/local/bin/perl

package main;

use FindBin '$Bin';

BEGIN{
    require $Bin."/bootstrap.pl";
}

use base Cpanel::Lite::JsonWrapper;

my $m = undef;

$m = new main();
$m->write({
    result => Whostmgr::ACLS::checkacl($m->{inputs}->{privilege})
});

__END__

