<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Network Configuration Tool</title>
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
        form {
            margin-top: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .radio-group {
            margin-top: 10px;
        }
        .radio-label {
            margin-right: 20px;
            font-weight: normal;
        }
        button {
            background-color: #3498db;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
        }
        button:hover {
            background-color: #2980b9;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Network Configuration Tool</h1>
        <form action="process.php" method="POST">
            <div class="form-group">
                <label for="mac_address">MAC Address:</label>
                <input type="text" id="mac_address" name="mac_address" placeholder="Format: 00:1A:2B:3C:4D:5E" required>
            </div>
            
            <div class="form-group">
                <label>DHCP Version:</label>
                <div class="radio-group">
                    <input type="radio" id="dhcpv4" name="dhcp_version" value="DHCPv4" required>
                    <label class="radio-label" for="dhcpv4">DHCPv4</label>
                    
                    <input type="radio" id="dhcpv6" name="dhcp_version" value="DHCPv6">
                    <label class="radio-label" for="dhcpv6">DHCPv6</label>
                </div>
            </div>
            
            <button type="submit">Request IP Address</button>
        </form>
    </div>
</body>
</html>