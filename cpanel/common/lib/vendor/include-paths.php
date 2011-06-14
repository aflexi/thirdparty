<?php

set_include_path(
    realpath(dirname(__FILE__)).':'.
    realpath(dirname(__FILE__).'/../vendor').':'.
    get_include_path()
);

?>