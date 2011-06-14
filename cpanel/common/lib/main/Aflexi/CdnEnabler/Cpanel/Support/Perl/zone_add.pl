#!/usr/local/bin/perl

package main;

use FindBin '$Bin';

BEGIN{
    require $Bin."/bootstrap.pl";
}

use base Cpanel::Lite::JsonWrapper;
use Cpanel::ZoneEdit;
use Cpanel::Encoder::URI;

my $m;
my @inputs;
my @result;

sub hasfeature {
    my $name = shift;
    if ($Cpanel::rootlogin) {
        if ( Cpanel::StringFunc::Case::ToLower($name) eq 'style' || Cpanel::StringFunc::Case::ToLower($name) eq 'setlang' ) {
            return 1;
        }
        return 0;
    }
    $name &&= 'FEATURE-' . Cpanel::StringFunc::Case::ToUpper($name);
    if ( $name && defined $CPDATA{$name} && $CPDATA{$name} eq '0' ) {
        return 0;
    }
    return 1;
}

$m = new main();

@inputs = %{ $m->{inputs} };

@result = Cpanel::ZoneEdit::api2_add_zone_record(@inputs);

# JSON
# ------------------------------------------------------------------------------
$m->write({
    result => @result
});

__END__

