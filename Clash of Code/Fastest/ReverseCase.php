<?php

$S = stream_get_line(STDIN, 100, "\n");
echo strtolower($S) ^ strtoupper($S) ^ $S;