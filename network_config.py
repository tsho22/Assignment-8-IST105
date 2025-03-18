#!/usr/bin/env python3
import sys
import json
import re
import time
import random
import os
from datetime import datetime, timedelta

# Define the IP pools for DHCPv4 and DHCPv6
IPV4_SUBNET = "192.168.1.0/24"
IPV4_POOL_START = 10  # Starting from 192.168.1.10
IPV4_POOL_END = 200   # Ending at 192.168.1.200
IPV6_SUBNET = "2001:db8::/64"

lease_database = {}
LEASE_TIME = 3600  


def validate_mac_address(mac):
    """Validate MAC address format: AA:BB:CC:DD:EE:FF"""
    pattern = re.compile(r'^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$')
    return bool(pattern.match(mac))

def generate_ipv6_from_mac(mac):
    """Generate IPv6 address using EUI-64 format based on MAC address"""


    mac_parts = mac.replace(':', '').lower()
    
   
   
    eui64 = mac_parts[0:6] + "fffe" + mac_parts[6:12]
    
    
    first_byte = int(eui64[0:2], 16)
    first_byte ^= 0x02 
    
    modified_eui64 = format(first_byte, '02x') + eui64[2:]
    


    ipv6_parts = [modified_eui64[i:i+4] for i in range(0, len(modified_eui64), 4)]
    ipv6_suffix = ":".join(ipv6_parts)


    ipv6_address = IPV6_SUBNET.replace("/64", "") + ipv6_suffix
    
    return ipv6_address

def assign_ipv4_address(mac):
    """Assign an IPv4 address from the pool"""


    if mac in lease_database and 'ipv4' in lease_database[mac]:
      
      
        lease_database[mac]['expiry'] = datetime.now() + timedelta(seconds=LEASE_TIME)
        return lease_database[mac]['ipv4']
    
  
  
    assigned_ips = {lease['ipv4'] for mac, lease in lease_database.items() if 'ipv4' in lease}
    
 
 
    subnet_base = ".".join(IPV4_SUBNET.split('.')[0:3])


    for i in range(IPV4_POOL_START, IPV4_POOL_END + 1):
        candidate_ip = f"{subnet_base}.{i}"
        if candidate_ip not in assigned_ips:
            if mac not in lease_database:
                lease_database[mac] = {}
            lease_database[mac]['ipv4'] = candidate_ip
            lease_database[mac]['expiry'] = datetime.now() + timedelta(seconds=LEASE_TIME)
            return candidate_ip
    
    
    return None

def process_dhcp_request(mac, dhcp_version):
    """Process DHCP request based on version and MAC address"""
    if not validate_mac_address(mac):
        return {
            "error": "Invalid MAC address format. Please use format XX:XX:XX:XX:XX:XX"
        }
    
    result = {
        "mac_address": mac,
        "lease_time": f"{LEASE_TIME} seconds"
    }
    
    if dhcp_version == "DHCPv4":
        ipv4_address = assign_ipv4_address(mac)
        if ipv4_address:
            result["assigned_ipv4"] = ipv4_address
            result["subnet"] = IPV4_SUBNET
        else:
            result["error"] = "No IPv4 addresses available in the pool"
    
    elif dhcp_version == "DHCPv6":
        ipv6_address = generate_ipv6_from_mac(mac)
        if mac not in lease_database:
            lease_database[mac] = {}
        lease_database[mac]['ipv6'] = ipv6_address
        lease_database[mac]['expiry'] = datetime.now() + timedelta(seconds=LEASE_TIME)
        
        result["assigned_ipv6"] = ipv6_address
        result["subnet"] = IPV6_SUBNET
    
    else:
        result["error"] = "Invalid DHCP version specified. Use DHCPv4 or DHCPv6."
    
    return result

def main():
    """Main function to process parameters from PHP or command line"""
    
    
    if len(sys.argv) > 1:
    
    
        mac_address = sys.argv[1] if len(sys.argv) > 1 else None
        dhcp_version = sys.argv[2] if len(sys.argv) > 2 else None
    else:
       
       
        params = os.environ.get('QUERY_STRING', '')
        post_data = sys.stdin.read() if 'CONTENT_LENGTH' in os.environ else ''
        
     
     
        if params:
            param_dict = dict(p.split('=') for p in params.split('&') if '=' in p)
            mac_address = param_dict.get('mac_address', '')
            dhcp_version = param_dict.get('dhcp_version', '')
        elif post_data:
      
      
            param_dict = dict(p.split('=') for p in post_data.split('&') if '=' in p)
            mac_address = param_dict.get('mac_address', '')
            dhcp_version = param_dict.get('dhcp_version', '')

  
    if mac_address and dhcp_version:
        result = process_dhcp_request(mac_address, dhcp_version)
        print("Content-Type: application/json")
        print() 
        print(json.dumps(result, indent=4))
    else:
      
        print("Content-Type: application/json")
        print() 
        print(json.dumps({"error": "Missing parameters. Required: mac_address, dhcp_version"}, indent=4))

if __name__ == "__main__":
    main()