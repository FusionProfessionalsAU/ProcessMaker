#!/bin/bash
/bin/bash -c "scp -o StrictHostKeyChecking=no --exclude /opt/processmaker//./.git/ -r -i /key/process_maker.pem . ubuntu@ec2-54-252-242-51.ap-southeast-2.compute.amazonaws.com:/opt/processmaker/"
