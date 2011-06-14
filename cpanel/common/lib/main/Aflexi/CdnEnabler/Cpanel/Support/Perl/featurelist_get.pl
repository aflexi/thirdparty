#!/usr/local/bin/perl

package main;

use FindBin '$Bin';

BEGIN{
    require $Bin."/bootstrap.pl";
}

use base Cpanel::Lite::JsonWrapper;
use Cpanel::Lite::CpanelExtUtils;

my $m;
my $feature_list_name;
my %feature_list;

$m = new main();

$feature_list_name = $m->{inputs}->{feature_list_name};
%feature_list = Cpanel::Lite::CpanelExtUtils::get_feature_list($feature_list_name);

# JSON
# ------------------------------------------------------------------------------
$m->write({
    feature_list => \%feature_list
});

__END__

