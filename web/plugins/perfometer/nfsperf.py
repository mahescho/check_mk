#!/usr/bin/python
# -*- encoding: utf-8; py-indent-offset: 4 -*-

def perfometer_check_mk_nfsperf(row, check_command, perf_data):

    read_bytes = float(perf_data[0][1])+float(perf_data[1][1])
    write_bytes = float(perf_data[2][1])+float(perf_data[3][1])

    text = "%-.2f M/s  %-.2f M/s" % \
            (read_bytes / (1024*1024.0), write_bytes / (1024*1024.0))

    return text, perfometer_logarithmic_dual(
            read_bytes, "#60e0a0", write_bytes, "#60a0e0", 5000000, 10)

perfometers["check_mk-nfsperf"] = perfometer_check_mk_nfsperf
