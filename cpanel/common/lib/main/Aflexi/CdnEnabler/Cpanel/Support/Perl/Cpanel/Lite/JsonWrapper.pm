#!/usr/local/bin/perl

# JSON implementation of Cpanel::Lite::Wrapper.
# 
# Documentation at the __END__

package Cpanel::Lite::JsonWrapper;

use JSON;

use base qw(Cpanel::Lite::Wrapper);

# Exporting JSON's constants
# ------------------------------------------------------------------------------
use constant {
    TRUE => JSON::true,
    FALSE => JSON::false
};

use Exporter qw(import);
our @EXPORT_OK = qw(TRUE FALSE);

# Private
# ------------------------------------------------------------------------------
my $json = JSON->new->allow_nonref;

# Public 
# ------------------------------------------------------------------------------

sub initialize(){
    my $self = shift;
    $self->SUPER::initialize(@_);
    $self->{json} = $json;
}

# Parse the first parameter to a hash.
sub parse(){
    my $self = shift;
    if(@_){
        return %{ $json->decode(@_[0]) };
    } else{
        return ();
    }
}

# Serialize everything to a JSON array.
sub serialize(){
    my $self = shift;
    return $json->encode(@_[0]);
}

1;

__END__

=head1 NAME

JSON implementation of Cpanel::Lite::Wrapper. 

=head1 DESCRIPTION

This package overrides the parse()
and serialize() methods, thus making itself to support:

 - Parsing/decoding JSON text (a JSON object), supporting one argument.
 - Serializing/encoding Perl types to JSON.

=head2 FEATURES

=head3 CONSTANTS

As Perl handles "boolean" using 0 and 1 integer, to serialize them to JSON's
boolean, you will have to use the TRUE and FALSE constants provided by this 
package.

=head1 AUTHOR

Written and maintained by Yuen-Chi Lian <yc@aflexi.net>.

=head1 VERSION

Version 2.4 (2010-05-30), since 2.4.

=head1 COPYRIGHT AND LICENSE

Copyright (c) 2010 Aflexi Sdn. Bhd. All rights reserved.

This program is free software. Refer to the LICENSE.txt shipped with it for
further legal (technical) details.

