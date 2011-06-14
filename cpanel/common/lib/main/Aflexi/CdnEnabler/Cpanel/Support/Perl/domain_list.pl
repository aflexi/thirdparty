#!/usr/local/bin/perl

package main;

use FindBin '$Bin';

BEGIN{
    require $Bin."/bootstrap.pl";
}

use base Cpanel::Lite::JsonWrapper;
use Cpanel::Config::LoadUserDomains;
use Cpanel::DomainKeys;

my $m;
my @domains;

$m = new main();

# Read all user domains from /etc/userdomains, non-reverse and array. Returned
# as hash.
# ------------------------------------------------------------------------------

if ($ENV{'REMOTE_USER'} eq 'root') {
    @domains = Cpanel::Config::LoadUserDomains::loaduserdomains({}, 0, 1);
}
else {
    @domains = Cpanel::DomainKeys::get_all_domains_ref($ENV{'USER'});
}

# JSON
# ------------------------------------------------------------------------------
$m->write({
    domains => @domains
});

__END__

