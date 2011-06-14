#!/usr/local/bin/perl

# Abstract wrapper that extends CpanelAware to ease writing helper scripts on 
# cPanel.
# 
# Documentation at the __END__

package Cpanel::Lite::Wrapper;

use IO::Handle;

use base qw(Cpanel::Lite::CpanelAware);

sub new{
    my $class = shift;
    my $self = new Cpanel::Lite::CpanelAware();    
    $self->{inputs} = undef;
    bless $self, $class;
    $self->initialize();
    return $self;
}
sub initialize(){
    $self = shift;
    $self->read();
}

# Read the inputs coming from client and store into $self->{inputs}.
# Return the inputs (hash).
sub read(){

    my $self = shift;
    
    if(@_){
        $self->{inputs} = [@_];
    } else{
    
        # If no parameter is passed to this method, we read from STDIN. If
        # there's nothing, we try @ARGV.
        # NOTE [yclian 20100529] Am giving STDIN the priority as it has no li-
        # mit.
        
        my $stdInString = "";
        
        STDIN->blocking(0);
        while (defined(my $stdInLine = <STDIN>)){
            $stdInString .= $stdInLine;
            if(!$hasStdIn){
                $hasStdIn = 1;
           }
        }
        
        if($stdInString){        
            $self->{inputs} = [$stdInString];
        } else{
            $self->{inputs} = [@ARGV];
        }
    }
    
    # Process it, parse()
    # NOTE [yclian 20100527] if I use the commented below, yes, I manage to 
    # obtain a scalar, but Perl will complain later that it's not a hash re-
    # ference. After speaking with the IRC guys, they asked a very valid 
    # question - why bother about the reference? Let Perl handles it, just put 
    # it into an anonymous array.
    # $self->{inputs} = \$self->parse(@{ $self->{inputs} });
    # NOTE [yclian 20100528] Hash shall hold reference to array/hash. That's why
    # we are creating an anonymous hash.
    $self->{inputs} = { $self->parse(@{ $self->{inputs} }) };
    
    # Dereferencing.
    return %{ $self->{inputs} };
}

# Parse a given array (or its element) and return the parsed value.
# 
sub parse(){
    shift;
    return @_;
}

# Write the specified parameter(s) to the STDIN, or other files, depends on 
# the implementation.
sub write(){

    my $self = shift;
    my $output = undef;
    
    $output = @_;
    $output = $self->serialize(@_);
    
    print "$output\n";
    return $output;
}

sub serialize(){
    shift;
    return @_;
}

1;

__END__

=head1 NAME

Abstract wrapper that extends CpanelAware to ease writing helper scripts on 
cPanel.

We are currently using this (specifically JsonWrapper) to allow PHP to communi-
cate with cPanel's Perl.

=head1 AUTHOR

Written and maintained by Yuen-Chi Lian <yc@aflexi.net>.

=head1 VERSION

Version 2.4 (2010-05-30), since 2.4.

=head1 COPYRIGHT AND LICENSE

Copyright (c) 2010 Aflexi Sdn. Bhd. All rights reserved.

This program is free software. Refer to the LICENSE.txt shipped with it for
further legal (technical) details.

