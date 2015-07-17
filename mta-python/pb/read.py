import nyct_subway_pb2
import gtfs_realtime_pb2
import sys
import json
import urllib2
import inspect

subways = gtfs_realtime_pb2.FeedMessage()

u = urllib2.urlopen('http://datamine.mta.info/mta_esi.php?key=b2e51ef286420ed1e855a0cd4fca4dd6')
subways.ParseFromString(u.read())
u.close()
print subways.__str__

index = 0
print subways.header

for entity in subways.entity:
 	index = index+1
	print index,': ',entity,'\n'
# 	print  entity.trip_update._fields
# 	#print inspect.getmembers(entity)
# 	print 
	
