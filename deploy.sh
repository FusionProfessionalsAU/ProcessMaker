#!/bin/bash
/bin/bash -c "scp -o StrictHostKeyChecking=no -r -i /key/process_maker.pem /var/lib/go-agent/pipelines/Deploy/ ubuntu@ec2-54-252-242-51.ap-southeast-2.compute.amazonaws.com:/opt/processmaker/"
