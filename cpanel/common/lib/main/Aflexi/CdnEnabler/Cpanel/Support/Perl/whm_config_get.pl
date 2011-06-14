#!/usr/local/bin/perl

package main;

use FindBin '$Bin';

BEGIN{
    require $Bin."/bootstrap.pl";
}

use base Cpanel::Lite::JsonWrapper;
use Cpanel::Config::LoadWwwAcctConf ();
use Cpanel::Encoder::URI;

my $m;
my $wwwacctconf_ref;

$m = new main();

@inputs = %{ $m->{inputs} };

$wwwacctconf_ref = Cpanel::Config::LoadWwwAcctConf::loadwwwacctconf();

# JSON
# ------------------------------------------------------------------------------
$m->write({
    result => $wwwacctconf_ref
});

__END__

