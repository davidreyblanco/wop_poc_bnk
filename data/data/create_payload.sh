#!/bin/bash
TMP_FILE=tmp/tmp_file.txt
head -n 1 data/data_wop.csv.txt >$TMP_FILE
grep $1 data/data_wop.txt >>$TMP_FILE
php process_csv.php $TMP_FILE
#rm $TMP_FILE
