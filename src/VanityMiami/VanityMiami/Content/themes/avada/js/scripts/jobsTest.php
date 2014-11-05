<?php
/**
 * Job scheduler for testing. Jobs.php run every 30 sec. by default.
 */
$sec = (int) @$_REQUEST['sec'];
if ($sec == 0) {
	$sec = 30;
}

require_once 'jobs.php';

echo "
<html>
<head>
<script type='text/javascript'>
    function runJobs(sec) {
        var messageDiv = document.getElementById('message');
        if (sec == 0) {
            messageDiv.innerHTML = 'Jobs is running...';
            location.reload();
            return;
        }
        messageDiv.innerHTML = 'Jobs will start on '+sec+' sec...';
        setTimeout('runJobs('+(sec - 1)+')',1000);
    }
</script>
</head>
<body>
<div id='message'></div>
<script type='text/javascript'>
    runJobs($sec);
</script>
</body>
</html>
";
?>
