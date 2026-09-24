#!/usr/bin/env python3
"""
Sample RFID reader helper (concept). Many USB RFID readers act as HID keyboards and will 'type' the UID.
This script shows how to post a UID to the server endpoint when read. Adapt to your reader's SDK.
"""
import requests
import time

SERVER = 'http://localhost/LIBAI/?url=book/scanRfid'

def post_uid(uid):
    try:
        r = requests.get(SERVER + '&uid=' + uid, timeout=5)
        print(r.text)
    except Exception as e:
        print('Error posting UID', e)

def main():
    print('RFID reader helper started. Enter UID to simulate:')
    while True:
        uid = input('UID> ').strip()
        if not uid: break
        post_uid(uid)
        time.sleep(0.2)

if __name__ == '__main__':
    main()
