#!/bin/bash
/bin/bash -c "ssh -o StrictHostKeyChecking=no -i /key/process_maker.pem ubuntu@ec2-13-210-72-132.ap-southeast-2.compute.amazonaws.com 'sudo chmod -R 777 /opt/processmaker/'"
/bin/bash -c "scp -o StrictHostKeyChecking=no -r -i /key/process_maker.pem . ubuntu@ec2-13-210-72-132.ap-southeast-2.compute.amazonaws.com:/opt/processmaker/"
