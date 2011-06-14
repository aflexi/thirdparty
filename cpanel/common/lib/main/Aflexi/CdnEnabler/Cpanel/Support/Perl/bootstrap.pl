#!/usr/local/bin/perl

# Bootstrap file for Cpanel::Lite modules. Use to initialize global and environ-
# ment variables.
# 
# Documentation at the __END__
# 
use strict;
use warnings;
    
BEGIN{

    # Presetting environment variables.
    unless(exists $ENV{'CPANEL_HOME'} && $ENV{'CPANEL_HOME'}){
        $ENV{'CPANEL_HOME'} = "/usr/local/cpanel";
    }
    unless(exists $ENV{'CPANEL_DATA'} && $ENV{'CPANEL_DATA'}){
        $ENV{'CPANEL_DATA'} = "/var/cpanel";
    }
    unless(exists $ENV{'CPANEL_AFX_HOME'} && $ENV{'CPANEL_AFX_HOME'}){
        $ENV{'CPANEL_AFX_HOME'} = "$ENV{'CPANEL_HOME'}/3rdparty/aflexi";
    }
    unless(exists $ENV{'CPANEL_AFX_LIB'} && $ENV{'CPANEL_AFX_LIB'}){
        $ENV{'CPANEL_AFX_LIB'} = "$ENV{'CPANEL_AFX_HOME'}/lib/main";
    }
    
    unshift(@INC, ($ENV{'CPANEL_HOME'}, $ENV{'CPANEL_AFX_LIB'}."/Aflexi/CdnEnabler/Cpanel/Support/Perl"));
}

END{
}

1;

__END__

=head1 NAME

Bootstrap file for all Perl scripts.

=head1 AUTHOR

Written and maintained by Yuen-Chi Lian <yc@aflexi.net>.

=head1 VERSION

Version 2.4 (2010-05-25), since 2.4.

=head1 COPYRIGHT AND LICENSE

Copyright (c) 2010 Aflexi Sdn. Bhd. All rights reserved.

This program is free software. Refer to the LICENSE.txt shipped with it for
further legal (technical) details.

