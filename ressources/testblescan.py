# test BLE Scanning software
# jcs 6/8/2014

import blescan
import sys

import bluetooth._bluetooth as bluez

dev_id = 0
try:
	sock = bluez.hci_open_dev(dev_id)
	#print "ble thread started"

except:
	print "error accessing bluetooth device..."
    	sys.exit(1)

blescan.hci_le_set_scan_parameters(sock)
blescan.hci_enable_le_scan(sock)

returnedList = blescan.parse_events(sock, 10)
#print "----------"
unique=[]
[unique.append(item[0:17]) for item in returnedList if item[0:17] not in unique]
for beacon in unique:
	print beacon[0:17]

