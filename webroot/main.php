<?php
/**
 * Main Controller for Saltfan
 */

require_once(sprintf('%s/boot.php', dirname(__DIR__)));

header('cache-control: no-store,max-age=0');
header('content-type: text/plain');


print_r(get_included_files());


print_r(memory_get_peak_usage(true));

exit(0);
