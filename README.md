# Assignment8-IST105
# Network Configuration Tool

This repository contains a Python-based network automation tool that simulates IPv6 address allocation and DHCPv4/DHCPv6 configuration. The system is designed to generate and validate IPv6 addresses, simulate a DHCP server for IPv4 or IPv6 address leasing, and can be deployed on AWS EC2 instances with a load balancer.

## Components

1. **form.php**: User input form that collects MAC address and DHCP version selection
2. **network_config.py**: Python script that simulates DHCP server functionality
3. **process.php**: PHP script that processes form data and displays results

## Features

- Generate and validate IPv6 addresses using EUI-64 format
- Simulate DHCP server for both IPv4 and IPv6 address leasing
- Track IP address leases with expiration times
- Validate network configurations before assignment
- Prevent duplicate IP allocations

## Usage

1. Open `form.php` in a web browser
2. Enter a valid MAC address (format: AA:BB:CC:DD:EE:FF)
3. Select either DHCPv4 or DHCPv6 as the allocation method
4. Submit the form to receive an assigned IP address with lease information

## Example Output

For DHCPv6 request:
```json
{
  "mac_address": "00:1A:2B:3C:4D:5E",
  "assigned_ipv6": "2001:db8::1a2b:3c4d:5e01",
  "lease_time": "3600 seconds"
}
```

For DHCPv4 request:
```json
{
  "mac_address": "00:1A:2B:3C:4D:5E",
  "assigned_ipv4": "192.168.1.10",
  "lease_time": "3600 seconds"
}
```

## Deployment

This application is deployed on AWS EC2 instances with a load balancer for high availability.

## Repository Structure

This repository contains three branches:
- main: Used for implementing new features
- development: Used for integrating changes and testing
- featureHassanOmotoshoFolarori: Contains the final, tested version of the application
