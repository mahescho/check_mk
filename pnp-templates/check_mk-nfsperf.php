<?php

$name  =str_replace(' ','/',str_replace('  ',':/',str_replace('NFS performance ','',str_replace('_', ' ', $servicedesc))));

$defopt = "-l0 -b 1024 --color MGRID\"#cccccc\" --color GRID\"#dddddd\" ";

# Work with names rather then numbers.
global $mem_defines;
$mem_defines = array();
foreach ($NAME as $i => $n) {
    $mem_defines[$n] = "DEF:$n=$RRDFILE[$i]:$DS[$i]:MAX ";
}

# avoid redeclaration errors if this file is include multiple times (e.g. by basket)
if (!function_exists('nfsperf_area')) {
    function nfsperf_area($varname, $color, $title, $stacked, $unit)
    {
        return nfsperf_curve("AREA", $varname, $color, $title, $stacked, $unit);
    }

    function nfsperf_line($varname, $color, $title, $stacked, $unit)
    {
        return nfsperf_curve("LINE1", $varname, $color, $title, $stacked, $unit);
    }

    function nfsperf_curve($how, $varname, $color, $title, $stacked, $unit)
    {
        global $mem_defines;
        $tit = sprintf("%-15s", $title);
        if (isset($mem_defines[$varname])) {
            $x = $mem_defines[$varname] . "$how:$varname#$color:\"$tit\"";
            if ($stacked)
                $x .= ":STACK";
            $x .= " ";
            if ($unit == "byte") {
                $x .= "CDEF:${varname}_mb=$varname,1048576,/ ";
                $x .= "GPRINT:${varname}_mb:LAST:\"%8.1lf MB/s last\" ";
                $x .= "GPRINT:${varname}_mb:AVERAGE:\"%8.1lf MB/s avg\" ";
                $x .= "GPRINT:${varname}_mb:MAX:\"%8.1lf MB/s max\\n\" ";
            }
            else {
                $x .= "GPRINT:${varname}:LAST:\"%6.1lf $unit last\" ";
                $x .= "GPRINT:${varname}:AVERAGE:\"%6.1lf $unit avg\" ";
                $x .= "GPRINT:${varname}:MAX:\"%6.1lf $unit max\\n\" ";
            }
            return $x;
        }
        else {
            return "";
        }
    }
}

$opt[] = $defopt . "--vertical-label 'Bytes per Second' --title \"NFS read: $name\"";
$def[] = ""
        . nfsperf_area("readrx", "90D4A9", "NFS read RX", FALSE, "byte")
        . nfsperf_area("readtx", "FC2D80", "NFS read TX", TRUE, "byte")
        ;

$opt[] = $defopt . "--vertical-label 'Bytes per Second' --title \"NFS write: $name\"";
$def[] = ""
        . nfsperf_area("writetx", "90D4A9", "NFS write TX", FALSE, "byte")
        . nfsperf_area("writerx", "FC2D80", "NFS write RX", TRUE, "byte")
        ;

$opt[] = $defopt . "--vertical-label 'ms' --title \"NFS read request time: $name\"";
$def[] = ""
        . nfsperf_area("rtime", "3A9AD6", "NFS read", FALSE, "ms")
        ;

$opt[] = $defopt . "--vertical-label 'ms' --title \"NFS write request time: $name\"";
$def[] = ""
        . nfsperf_area("wtime", "3A9AD6", "NFS read", FALSE, "ms")
        ;

?>
