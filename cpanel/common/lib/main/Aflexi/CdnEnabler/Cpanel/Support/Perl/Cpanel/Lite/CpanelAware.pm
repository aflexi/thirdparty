#!/usr/local/bin/perl

# Base package to be inherited for Cpanel related features.
#
# Documentation at the __END__

package Cpanel::Lite::CpanelAware;

use Whostmgr::ACLS;

sub new{
    my $class = shift;
    my $self = {};
    bless $self, $class;
    $self->initialize();
    return $self;
}

sub initialize(){
    my $self = shift;
    $self->initializeCpanel();
}

sub initializeCpanel(){
    Whostmgr::ACLS::init_acls();
}

1;

__END__

=head1 NAME

Base package to be inherited for cPanel related features. This package boot-
straps the cPanel state.

=head1 AUTHOR

Written and maintained by Yuen-Chi Lian <yc@aflexi.net>.

=head1 VERSION

Version 2.4 (2010-05-28), since 2.4.

=head1 COPYRIGHT AND LICENSE

Copyright (c) 2010 Aflexi Sdn. Bhd. All rights reserved.

This program is free software. Refer to the LICENSE.txt shipped with it for
further legal (technical) details.

