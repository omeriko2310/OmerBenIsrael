<?php

echo $_GET['caseNumber'];
// Check if the case number is sent via POST
if(isset($_GET['caseNumber'])) {
    // Sanitize the case number input
    $caseNumber = filter_var($_GET['caseNumber'], FILTER_SANITIZE_STRING);

    // Define the source file (chartData.json) and the destination file
    $sourceFile = 'chartData.json';
    $destinationFile = 'chartData' . $caseNumber . '.json';

    // Command to copy the source file to the destination file
    $copyCommand = 'cp ' . $sourceFile . ' ' . $destinationFile;
    $permission = 'chmod 777 ' . $destinationFile;
    // Execute the command
    $output = shell_exec($copyCommand);
    $output = shell_exec($permission);
    if($output !== null) {
    	error_log('Error changing permissions: ' . $output);
    }
}
?>
