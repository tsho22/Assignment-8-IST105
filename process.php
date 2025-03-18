<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {
   
    
    $mac_address = isset($_POST['mac_address']) ? trim($_POST['mac_address']) : '';
    $dhcp_version = isset($_POST['dhcp_version']) ? trim($_POST['dhcp_version']) : '';
    
   
    
    $errors = [];
    
    if (empty($mac_address)) {
        $errors[] = "MAC address is required";
    } else {
      
        
        if (!preg_match('/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/', $mac_address)) {
            $errors[] = "Invalid MAC address format. Please use format XX:XX:XX:XX:XX:XX";
        }
    }
    
    if (empty($dhcp_version)) {
        $errors[] = "DHCP version selection is required";
    } else if ($dhcp_version !== "DHCPv4" && $dhcp_version !== "DHCPv6") {
        $errors[] = "Invalid DHCP version. Please select DHCPv4 or DHCPv6";
    }
    
  
    if (empty($errors)) {
      
        $escapedMac = escapeshellarg($mac_address);
        $escapedDhcp = escapeshellarg($dhcp_version);
        
     
        
        $command = "python3 network_config.py $escapedMac $escapedDhcp 2>&1";
        exec($command, $output, $return_var);
        
      
        if ($return_var !== 0) {
            $errors[] = "Error executing network configuration script: " . implode("\n", $output);
        } else {
          
            
            $jsonString = "";
            $jsonStarted = false;
            
            foreach ($output as $line) {
              
                
                if (trim($line) === "") {
                    $jsonStarted = true;
                    continue;
                }
                
                if ($jsonStarted) {
                    $jsonString .= $line;
                }
            }
            
           
            
            if (empty($jsonString)) {
               
                
                $fullOutput = implode("\n", $output);
                if (preg_match('/{.*}/s', $fullOutput, $matches)) {
                    $jsonString = $matches[0];
                } else {
                    $jsonString = $fullOutput; 
                }
            }
            
         
            $responseData = json_decode($jsonString, true);
       
            if ($responseData === null) {
                $errors[] = "Error parsing script output: " . json_last_error_msg();
                $errors[] = "Raw output: " . implode("\n", $output);
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DHCP Configuration Result</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f7f9;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            text-align: center;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            background-color: #e8f4fc;
            border-radius: 4px;
        }
        .result-item {
            margin-bottom: 10px;
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }
        .result-item:last-child {
            border-bottom: none;
        }
        .label {
            font-weight: bold;
        }
        .error {
            color: #e74c3c;
            background-color: #fadbd8;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 10px;
        }
        .back-button {
            display: inline-block;
            margin-top: 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            border-radius: 4px;
        }
        .back-button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>DHCP Configuration Result</h1>
        
        <?php if (!empty($errors)): ?>
            <div class="error">
                <strong>Error:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php elseif (isset($responseData)): ?>
            <div class="result">
                <?php if (isset($responseData['error'])): ?>
                    <div class="error">
                        <strong>Error:</strong> <?php echo htmlspecialchars($responseData['error']); ?>
                    </div>
                <?php else: ?>
                    <div class="result-item">
                        <span class="label">MAC Address:</span> 
                        <?php echo htmlspecialchars($responseData['mac_address']); ?>
                    </div>
                    
                    <?php if (isset($responseData['assigned_ipv4'])): ?>
                        <div class="result-item">
                            <span class="label">Assigned IPv4 Address:</span> 
                            <?php echo htmlspecialchars($responseData['assigned_ipv4']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($responseData['assigned_ipv6'])): ?>
                        <div class="result-item">
                            <span class="label">Assigned IPv6 Address:</span> 
                            <?php echo htmlspecialchars($responseData['assigned_ipv6']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($responseData['subnet'])): ?>
                        <div class="result-item">
                            <span class="label">Subnet:</span> 
                            <?php echo htmlspecialchars($responseData['subnet']); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (isset($responseData['lease_time'])): ?>
                        <div class="result-item">
                            <span class="label">Lease Time:</span> 
                            <?php echo htmlspecialchars($responseData['lease_time']); ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        
        <a href="form.php" class="back-button">Back to Form</a>
    </div>
</body>
</html>