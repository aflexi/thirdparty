#!/usr/local/bin/perl

package main;

use FindBin '$Bin';

BEGIN{
    require $Bin."/bootstrap.pl";
}

use base Cpanel::Lite::JsonWrapper;

use Cpanel::Config::LoadCpUserFile;
use Cpanel::Lite::CpanelExtUtils;

my $m;

$m = new main();

if(exists($m->{inputs}->{user_name})){
     my %user = Cpanel::Config::LoadCpUserFile::load($m->{inputs}->{user_name});
     $m->write({
        user => \%user
    });    
} else{
    my @user_names;
    my %users;
        
    @user_names = Cpanel::Lite::CpanelExtUtils::get_user_names();
    %users = ();
    foreach(@user_names){
        my %user;
        my $user_name = $_;
        %user = Cpanel::Config::LoadCpUserFile::load($user_name);
        $users{$user_name} = \%user;
    }
    $m->write({
        users => \%users
    });
}
__END__

